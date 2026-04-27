from flask import Blueprint, request, jsonify, current_app
import datetime
import requests
from backend.utils import db_conn, cipher_suite
import os
import hashlib
import json
from decimal import Decimal
from web3 import Web3
import uuid
print("DONATION FORM HIT2")
user_donation_bp = Blueprint('user_donation', __name__)

ALLOWED_EXTENSIONS = {'pdf', 'png', 'jpg', 'jpeg', 'jfif', 'gif'}

def allowed_file(filename):
    return '.' in filename and filename.rsplit('.', 1)[1].lower() in ALLOWED_EXTENSIONS

#Temporary Gcash Donation Flow for testing without Xendit integration
@user_donation_bp.route('/donate', methods=['POST'])
def create_donation():
    print("DONATION FORM HIT")
    data = request.json

    full_name = data['full_name']
    email = data['email']
    contact_number = data['contact_number']
    birthday = data.get('birthday', None)
    amount = data['amount']
    donor_id = data.get('donor_id')

    # Encrypt sensitive data
    encrypted_full_name = cipher_suite.encrypt(full_name.encode()).decode()
    encrypted_email = cipher_suite.encrypt(email.encode()).decode()
    encrypted_contact = cipher_suite.encrypt(contact_number.encode()).decode()

    # Generate public ID
    public_id = "DON-" + uuid.uuid4().hex[:10].upper()

    conn = db_conn()
    cursor = conn.cursor()

    cursor.execute("""
        INSERT INTO temp_donations 
        (public_id, donor_id, full_name, email, contact_number, birthday, amount, donation_status, donation_date)
        VALUES (%s, %s, %s, %s, %s, %s, %s, 'PENDING', NOW())
    """, (
        public_id,
        donor_id,
        encrypted_full_name,
        encrypted_email,
        encrypted_contact,
        birthday,
        amount
    ))

    conn.commit()
    cursor.close()
    conn.close()

    return jsonify({
        "message": "Donation submitted successfully",
        "public_id": public_id
    })

@user_donation_bp.route('/upload_receipt', methods=['POST'])
def upload_receipt():
    public_id = request.form.get('public_id')

    if 'receipt' not in request.files or not public_id:
        return jsonify({'success': False, 'message': 'Missing receipt or public_id'}), 400

    file = request.files['receipt']

    if file.filename == '':
        return jsonify({'success': False, 'message': 'No selected file'}), 400

    if file and allowed_file(file.filename):
        from werkzeug.utils import secure_filename
        import os
        import uuid

        ext = os.path.splitext(file.filename)[1].lower()
        filename = f"receipt_{public_id}_{uuid.uuid4().hex[:6]}{ext}"

        save_dir = os.path.abspath(os.path.join(current_app.root_path, 'frontend', 'admin', 'assets'))
        os.makedirs(save_dir, exist_ok=True)

        save_path = os.path.join(save_dir, filename)
        file.save(save_path)

        # Save filename in DB
        conn = db_conn()
        cursor = conn.cursor()

        cursor.execute("""
            UPDATE temp_donations
            SET proof_image = %s,
                donation_status = 'UNDER_REVIEW'
            WHERE public_id = %s
        """, (filename, public_id))

        conn.commit()
        cursor.close()
        conn.close()

        return jsonify({
            'success': True,
            'message': 'Receipt uploaded successfully',
            'filename': filename
        })

    return jsonify({'success': False, 'message': 'Invalid file type'}), 400

@user_donation_bp.route('/user_data', methods=['GET'])
def user_data():
    conn = db_conn()
    cursor = conn.cursor(dictionary=True)
    cursor.execute("SELECT * FROM users")
    user_data = cursor.fetchall()
    cursor.close()
    conn.close()
    return jsonify(user_data)


@user_donation_bp.route('/test', methods=['POST'])
def test():
    return jsonify({"status": "working"})