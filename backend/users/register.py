from flask import Blueprint, request, jsonify
import random
import datetime
import time
import re
from flask_mail import Message
from werkzeug.security import generate_password_hash
from backend.utils import db_conn
from flask import current_app as app
from flask_mail import Mail

register_bp = Blueprint('register', __name__)

def generate_otp():
    return str(random.randint(100000, 999999))

def send_otp(email, otp_code):
    try:
        mail = Mail(app)
        msg = Message('Verify Your Email', sender=app.config['MAIL_USERNAME'], recipients=[email])
        msg.body = f'Your OTP code is {otp_code}. It will expire in 5 minutes.'
        mail.send(msg)
        return True
    except Exception as e:
        return str(e)

def resend_otp(email, conn, cursor):
    otp_code = generate_otp()
    print(f"Generated OTP: {otp_code}")
    otp_expiry = (datetime.datetime.now(datetime.timezone.utc) + datetime.timedelta(minutes=5)).timestamp()

    cursor.execute("UPDATE users SET otp_code = %s, otp_expiry = %s WHERE email = %s", (otp_code, otp_expiry, email))
    conn.commit()

    send_result = send_otp(email, otp_code)
    if send_result is not True:
        return jsonify({'status': 'error', 'message': 'Failed to send email', 'error': send_result})

    return jsonify({'status': 'success', 'message': 'New OTP sent to your email', 'otp_expiry': otp_expiry})

@register_bp.route('/register', methods=['POST'])
def register_user():
    data = request.get_json()
    name = data.get('name', '').strip()
    email = data.get('email', '').strip()
    phone_number = data.get('phone_number', '').strip()
    password = data.get('password', '').strip()
    gender = data.get('gender', '').strip()

    if not all([name, email, phone_number, password, gender]):
        return jsonify({'status': 'error', 'message': 'All fields are required'})

    if not re.match(r"[^@]+@[^@]+\.[^@]+", email):
        return jsonify({'status': 'error', 'message': 'Invalid email format'})

    if len(password) < 8 or not re.search(r"[A-Z]", password) or not re.search(r"[a-z]", password) or not re.search(r"[0-9]", password) or not re.search(r"[!@#$%^&*(),.?\":{}|<>]", password):
        return jsonify({'status': 'error', 'message': 'Password must be at least 8 characters long and include an uppercase letter, a lowercase letter, a number, and a special character'})

    hashed_password = generate_password_hash(password)
    otp_code = generate_otp()
    print(f"Generated OTP: {otp_code}")
    otp_expiry = (datetime.datetime.now(datetime.timezone.utc) + datetime.timedelta(minutes=5)).timestamp()

    conn = db_conn()
    cursor = conn.cursor(dictionary=True)

    cursor.execute("SELECT * FROM users WHERE email = %s", (email,))
    existing_user = cursor.fetchone()
    if existing_user:
        if existing_user.get('is_verified'):
            cursor.close()
            conn.close()
            return jsonify({'status': 'error', 'message': 'Email already registered'})
        result = resend_otp(email, conn, cursor)
        cursor.close()
        conn.close()
        return result

    cursor.execute(
        "INSERT INTO users (name, email, phone_number, password, gender, role, otp_code, otp_expiry, is_verified, created_at) "
        "VALUES (%s, %s, %s, %s, %s, %s, %s, %s, 0, NOW())",
        (name, email, phone_number, hashed_password, gender, 'User', otp_code, otp_expiry)
    )

    conn.commit()
    cursor.close()
    conn.close()

    send_result = send_otp(email, otp_code)
    if send_result is not True:
        return jsonify({'status': 'error', 'message': 'Failed to send email', 'error': send_result})

    return jsonify({'status': 'success', 'message': 'Registration successful. Check your email for OTP.'})

@register_bp.route('/verify_otp', methods=['POST'])
def verify_otp():
    data = request.get_json()
    email = data['email']
    otp_code = data['otp']

    if not all([email, otp_code]):
        return jsonify({'status': 'error', 'message': 'Email and OTP are required'})

    conn = db_conn()
    cursor = conn.cursor(dictionary=True)

    cursor.execute("SELECT otp_code, otp_expiry, is_verified FROM users WHERE email = %s", (email,))
    user = cursor.fetchone()

    if not user:
        cursor.close()
        conn.close()
        return jsonify({'status': 'error', 'message': 'User not found'})

    if user['is_verified']:
        cursor.close()
        conn.close()
        return jsonify({'status': 'error', 'message': 'Account already verified'})

    if user['otp_code'] != otp_code:
        cursor.close()
        conn.close()
        return jsonify({'status': 'error', 'message': 'Invalid OTP'})
    
    if time.time() > user['otp_expiry']:
        cursor.close()
        conn.close()
        return jsonify({'status': 'error', 'message': 'OTP expired'})

    cursor.execute("UPDATE users SET is_verified = 1, otp_code = NULL, otp_expiry = NULL WHERE email = %s", (email,))
    conn.commit()
    cursor.close()
    conn.close()

    return jsonify({'status': 'success', 'message': 'Account verified successfully!'})

@register_bp.route('/resend_otp', methods=['POST'])
def user_resend_otp():
    data = request.get_json()
    email = data['email']

    if not email:
        return jsonify({'status': 'error', 'message': 'Email is required'})

    conn = db_conn()
    cursor = conn.cursor(dictionary=True)

    cursor.execute("SELECT id, is_verified FROM users WHERE email = %s", (email,))
    user = cursor.fetchone()

    if not user:
        cursor.close()
        conn.close()
        return jsonify({'status': 'error', 'message': 'User not found'})

    if user['is_verified']:
        cursor.close()
        conn.close()
        return jsonify({'status': 'error', 'message': 'Account already verified'})

    result = resend_otp(email, conn, cursor)
    cursor.close()
    conn.close()
    return result

