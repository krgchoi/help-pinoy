from flask import Blueprint, jsonify, request, current_app
from flask_mail import Message, Mail
from werkzeug.security import check_password_hash
import jwt
import datetime
import time
from backend.utils import db_conn
import os

admin_auth_bp = Blueprint('admin_auth', __name__)

def send_otp(email, otp_code):
    try:
        mail = Mail(current_app)
        msg = Message('Admin Login OTP', sender=current_app.config['MAIL_USERNAME'], recipients=[email])
        msg.body = f'Your OTP code is {otp_code}. It will expire in 5 minutes.'
        mail.send(msg)
        return True
    except Exception as e:
        return str(e)

def generate_otp():
    import random
    return str(random.randint(100000, 999999))

@admin_auth_bp.route('/login', methods=['POST'])
def admin_login():
    data = request.get_json()
    email = data['email']
    password = data['password']

    conn = db_conn()
    cursor = conn.cursor(dictionary=True)
    cursor.execute("SELECT id, name, email, password FROM admin WHERE email = %s", (email,))
    admin = cursor.fetchone()

    if admin and check_password_hash(admin['password'], password):
        otp_code = generate_otp()
        print(f"Generated OTP: {otp_code}")
        otp_expiry = int((datetime.datetime.now(datetime.timezone.utc) + datetime.timedelta(minutes=5)).timestamp())

        cursor.execute("UPDATE admin SET otp_code = %s, otp_expiry = %s WHERE id = %s", (otp_code, otp_expiry, admin['id']))
        conn.commit()

        send_result = send_otp(email, otp_code)
        if send_result is not True:
            return jsonify({'status': 'error', 'message': 'Failed to send OTP', 'error': send_result})

        return jsonify({'status': 'otp_sent', 'message': 'OTP sent to your email', 'admin_id': admin['id'], 'otp_expiry': otp_expiry})
    else:
        return jsonify({'status': 'error', 'message': 'Invalid email or password'})

@admin_auth_bp.route('/verify_otp', methods=['POST'])
def verify_admin_otp():
    data = request.get_json()
    admin_id = data['admin_id']
    otp_code = data['otp']

    conn = db_conn()
    cursor = conn.cursor(dictionary=True)
    cursor.execute("SELECT otp_code, otp_expiry FROM admin WHERE id = %s", (admin_id,))
    admin = cursor.fetchone()

    if not admin:
        return jsonify({'status': 'error', 'message': 'Admin not found'})

    if admin['otp_code'] != otp_code:
        return jsonify({'status': 'error', 'message': 'Invalid OTP'})

    if int(time.time()) > admin['otp_expiry']:
        return jsonify({'status': 'error', 'message': 'OTP expired'})

    token = jwt.encode(
        {'id': admin_id, 'exp': datetime.datetime.now(datetime.timezone.utc) + datetime.timedelta(minutes=5)},
        current_app.config['SECRET_KEY']
    )

    cursor.execute("UPDATE admin SET otp_code = NULL, otp_expiry = NULL WHERE id = %s", (admin_id,))
    conn.commit()
    cursor.close()
    conn.close()

    return jsonify({'status': 'success', 'token': token, 'message': 'OTP verified successfully'})

@admin_auth_bp.route('/resend_otp', methods=['POST'])
def resend_admin_otp():
    data = request.get_json()
    admin_id = data['admin_id']

    conn = db_conn()
    cursor = conn.cursor(dictionary=True)
    cursor.execute("SELECT email FROM admin WHERE id = %s", (admin_id,))
    admin = cursor.fetchone()

    if not admin:
        return jsonify({'status': 'error', 'message': 'Admin not found'})

    otp_code = generate_otp()
    otp_expiry = int((datetime.datetime.now(datetime.timezone.utc) + datetime.timedelta(minutes=5)).timestamp())

    cursor.execute("UPDATE admin SET otp_code = %s, otp_expiry = %s WHERE id = %s", (otp_code, otp_expiry, admin_id))
    conn.commit()

    send_result = send_otp(admin['email'], otp_code)
    if send_result is not True:
        return jsonify({'status': 'error', 'message': 'Failed to resend OTP', 'error': send_result})

    cursor.close()
    conn.close()
    return jsonify({'status': 'otp_resent', 'message': 'OTP resent to your email', 'otp_expiry': otp_expiry})
