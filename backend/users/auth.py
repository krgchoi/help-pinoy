from flask import Blueprint, request, jsonify
from werkzeug.security import check_password_hash
from backend.utils import db_conn

auth_bp = Blueprint('auth', __name__)

@auth_bp.route('/user_login', methods=['POST'])
def user_login():
    data = request.get_json()
    email = data.get('email')
    password = data.get('password')
    if not email or not password:
        return jsonify({"status": "fail", "message": "Missing credentials"}), 400

    try:
        conn = db_conn()
        cursor = conn.cursor(dictionary=True)
        sql = "SELECT id, name, email, password, is_verified, role FROM users WHERE email=%s"
        cursor.execute(sql, (email,))
        user = cursor.fetchone()
        cursor.close()
        conn.close()
    except Exception as e:
        return jsonify({"status": "fail", "message": "Database error"}), 500

    if not user:
        return jsonify({"status": "fail", "message": "User not found"}), 404

    if not check_password_hash(user['password'], password):
        return jsonify({"status": "fail", "message": "Incorrect password"}), 401

    if not user['is_verified']:
        return jsonify({"status": "fail", "message": "Account not verified"}), 403

    return jsonify({
        "status": "success",
        "email": user['email'],
        "name": user['name'],
        "user_id": user['id']
    }), 200
    