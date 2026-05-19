from flask import Blueprint, request, jsonify, send_from_directory, current_app
from backend.utils import db_conn, cipher_suite
from werkzeug.security import generate_password_hash
from werkzeug.utils import secure_filename
from backend.blockchain_verify import verify_donation_record
import re
import os

profile_bp = Blueprint('profile', __name__)

ALLOWED_EXTENSIONS = {'png', 'jpg', 'jpeg', 'gif'}

def allowed_file(filename):
    return '.' in filename and filename.rsplit('.', 1)[1].lower() in ALLOWED_EXTENSIONS

@profile_bp.route('/user_profile', methods=['POST'])
def user_profile():
    data = request.get_json()
    user_id = data.get('user_id')

    if not user_id:
        return jsonify({'error': 'Missing user_id'}), 400

    conn = db_conn()
    cursor = conn.cursor(dictionary=True)

    cursor.execute(
        "SELECT id, name, email, phone_number, gender, profile_img FROM users WHERE id = %s",
        (user_id,)
    )
    user_row = cursor.fetchone()

    user = {}
    if user_row:
        user = {
            'id': user_row['id'],
            'name': user_row['name'],
            'email': user_row['email'],
            'contact_number': user_row.get('phone_number', ''),
            'gender': user_row.get('gender', ''),
            'profile_img': user_row.get('profile_img', None)
        }

    cursor.execute("""
        SELECT  donation_id,
                public_id,
                donor_id,
                full_name,
                email,
                contact_number,
                birthday,
                amount,
                remaining_amount,
                donation_status,
                proof_image,
                receipt_reference,
                blockchain_tx,
                donation_date
        FROM temp_donations
        WHERE donor_id = %s
        ORDER BY donation_date DESC
    """, (user_id,))

    donation_rows = cursor.fetchall()

    cursor.execute("""
        SELECT 
            da.donation_id,
            d.project_name,
            da.allocated_amount,
            d.created_at
        FROM disbursement_allocations da
        JOIN disbursements d 
            ON da.disbursement_id = d.disbursement_id
        WHERE da.donor_id = %s
    """, (user_id,))

    allocation_rows = cursor.fetchall()

    allocations_map = {}

    for row in allocation_rows:
        donation_id = row['donation_id']

        if donation_id not in allocations_map:
            allocations_map[donation_id] = []

        allocations_map[donation_id].append({
            'project_name': row['project_name'],
            'allocated_amount': float(row['allocated_amount']),
            'date': row['created_at']
        })

    donations = []

    for row in donation_rows:

        # 🔓 decrypt sensitive data
        try:
            decrypted_full_name = cipher_suite.decrypt(row['full_name'].encode()).decode()
        except Exception:
            decrypted_full_name = ""

        try:
            decrypted_email = cipher_suite.decrypt(row['email'].encode()).decode()
        except Exception:
            decrypted_email = ""

        try:
            decrypted_contact = cipher_suite.decrypt(row['contact_number'].encode()).decode()
        except Exception:
            decrypted_contact = ""
        
        amount = float(row['amount'])
        remaining = float(row.get('remaining_amount', amount))
        
        if remaining == 0:
            usage_status = "COMPLETED"
        elif remaining < amount:
            usage_status = "PARTIALLY USED"
        else:
            usage_status = "AVAILABLE"

        donations.append({
            'donation_id': row['donation_id'],
            'public_id': row['public_id'],
            'donor_id': row['donor_id'],
            'full_name': decrypted_full_name,
            'email': decrypted_email,
            'contact_number': decrypted_contact,
            'birthday': row['birthday'],
            'amount': amount,
            'remaining_amount': remaining,
            'usage_status': usage_status,
            'donation_status': row['donation_status'],
            'proof_image': row['proof_image'],
            'receipt_reference': row['receipt_reference'],
            'blockchain_tx': row.get('blockchain_tx', ''),
            'donation_date': row['donation_date'],
            'allocations': allocations_map.get(row['donation_id'], [])
        })

    cursor.close()
    conn.close()

    return jsonify({
        'user': user,
        'donations': donations
    })

@profile_bp.route('/user_update_profile', methods=['POST'])
def user_update_profile():
    data = request.get_json()
    print("Incoming data:", data)
    user_id = data.get('user_id')
    name = data.get('name')
    contact_number = data.get('contact_number')
    gender = data.get('gender')
    password = data.get('password')
    confirm_password = data.get('confirm_password')

    if not user_id:
        return jsonify({'success': False, 'message': 'Missing user_id'}), 400

    if password:
        if len(password) < 8 or \
           not re.search(r'[A-Z]', password) or \
           not re.search(r'[a-z]', password) or \
           not re.search(r'[0-9]', password) or \
           not re.search(r'[\W]', password):
            return jsonify({'success': False, 'message': 'Password must be at least 8 characters long and include an uppercase letter, a lowercase letter, a number, and a special character.'}), 400
        if password != confirm_password:
            return jsonify({'success': False, 'message': 'Passwords do not match'}), 400

    conn = db_conn()
    cursor = conn.cursor()

    try:
        if password:
            hashed_password = generate_password_hash(password)
            cursor.execute(
                "UPDATE users SET name=%s, phone_number=%s, gender=%s, password=%s WHERE id=%s",
                (name, contact_number, gender, hashed_password, user_id)
            )
        else:
            cursor.execute(
                "UPDATE users SET name=%s, phone_number=%s, gender=%s WHERE id=%s",
                (name, contact_number, gender, user_id)
            )
        conn.commit()
        return jsonify({'success': True, 'message': 'Profile updated successfully'})
    except Exception as e:
        conn.rollback()
        return jsonify({'success': False, 'message': 'Failed to update profile'}), 500
    finally:
        cursor.close()
        conn.close()

@profile_bp.route('/user_upload_profile_image', methods=['POST'])
def user_upload_profile_image():
    user_id = request.form.get('user_id')
    if 'profile_img' not in request.files or not user_id:
        print("No file or user_id provided")
        return jsonify({'success': False, 'message': 'No file or user_id provided'}), 400
    file = request.files['profile_img']
    if file.filename == '':
        print("No selected file")
        return jsonify({'success': False, 'message': 'No selected file'}), 400
    if file and allowed_file(file.filename):
        filename = secure_filename(file.filename)
        ext = os.path.splitext(filename)[1].lower()
        filename = f"user_{user_id}{ext}"
        save_dir = os.path.abspath(os.path.join(current_app.root_path, 'frontend', 'users', 'assets','img','profile_img'))
        print(f"Saving image to: {save_dir}")
        try:
            os.makedirs(save_dir, exist_ok=True)
        except Exception as e:
            print(f"Failed to create directory: {save_dir}, error: {str(e)}")
            return jsonify({'success': False, 'message': f'Failed to create directory: {save_dir}, error: {str(e)}'}), 500
        save_path = os.path.join(save_dir, filename)
        try:
            file.save(save_path)
            print(f"File saved to: {save_path}")
        except Exception as e:
            print(f"Failed to save image: {save_path}, error: {str(e)}")
            return jsonify({'success': False, 'message': f'Failed to save image: {save_path}, error: {str(e)}'}), 500
        try:
            conn = db_conn()
            cursor = conn.cursor()
            cursor.execute("UPDATE users SET profile_img=%s WHERE id=%s", (filename, user_id))
            conn.commit()
            cursor.close()
            conn.close()
            print("Database updated successfully")
        except Exception as e:
            print(f"Failed to update profile image in database: {str(e)}")
            return jsonify({'success': False, 'message': f'Failed to update profile image in database: {str(e)}'}), 500
        return jsonify({'success': True, 'filename': filename, 'message': 'Profile image updated'})
    print("Invalid file type")
    return jsonify({'success': False, 'message': 'Invalid file type'}), 400

@profile_bp.route('/verify_donation', methods=['POST'])
def verify_donation():
    data = request.get_json()
    public_id = data.get('public_id')

    result = verify_donation_record(public_id)

    return jsonify(result)

@profile_bp.route('/donation_usage/<int:donation_id>', methods=['GET'])
def donation_usage(donation_id):

    conn = db_conn()
    cursor = conn.cursor(dictionary=True)

    try:
        # ==============================
        # 1. GET ORIGINAL DONATION INFO
        # ==============================
        cursor.execute("""
            SELECT amount, remaining_amount
            FROM temp_donations
            WHERE donation_id = %s
        """, (donation_id,))
        
        donation = cursor.fetchone()

        if not donation:
            return jsonify({
                "status": "error",
                "message": "Donation not found"
            }), 404

        # ==============================
        # 2. GET ALLOCATION DETAILS
        # ==============================
        cursor.execute("""
            SELECT 
                d.project_name,
                d.disbursement_id,
                d.created_at AS disbursement_date,
                da.allocated_amount
            FROM disbursement_allocations da
            JOIN disbursements d 
                ON da.disbursement_id = d.disbursement_id
            WHERE da.donation_id = %s
            ORDER BY d.created_at ASC
        """, (donation_id,))

        allocations = cursor.fetchall()

        # ==============================
        # 3. FORMAT RESPONSE
        # ==============================
        total_amount = float(donation['amount'])
        remaining_amount = float(donation.get('remaining_amount', 0))

        usage_list = []

        for row in allocations:
            usage_list.append({
                "project_name": row['project_name'],
                "allocated_amount": float(row['allocated_amount']),
                "disbursement_date": row['disbursement_date'],
                "disbursement_id": row['disbursement_id']
            })

        return jsonify({
            "status": "success",
            "donation": {
                "total_amount": total_amount,
                "remaining_amount": remaining_amount,
                "used_amount": total_amount - remaining_amount
            },
            "usage": usage_list
        })

    except Exception as e:
        return jsonify({
            "status": "error",
            "message": str(e)
        }), 500

    finally:
        cursor.close()
        conn.close()