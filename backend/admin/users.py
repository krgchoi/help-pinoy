from flask import Blueprint, jsonify, request
from werkzeug.security import generate_password_hash
from backend.admin.jwt_token import verify_token
from backend.utils import db_conn

users_bp = Blueprint('users', __name__)

@users_bp.route('/get_users', methods=['GET'])
@verify_token
def get_users(current_user):
    conn = db_conn()
    cursor = conn.cursor(dictionary=True)
    cursor.execute('SELECT id, name, email, role, created_at FROM users')
    users = cursor.fetchall()
    cursor.close()
    conn.close()
    return jsonify(users)

@users_bp.route('/add_user', methods=['POST'])
@verify_token
def add_user(current_user):
    data = request.get_json()
    name = data['name']
    email = data['email']
    password = data['password']
    role = data['role']
    # Accept "User" as a valid role
    if role not in ['Donor', 'User']:
        return jsonify({'status': 'fail', 'message': 'Invalid role'}), 400

    hashed_password = generate_password_hash(password)
    conn = db_conn()
    cursor = conn.cursor(dictionary=True)
    cursor.execute('INSERT INTO users (name, email, password, role) VALUES (%s, %s, %s, %s)', 
                   (name, email, hashed_password, role))
    conn.commit()
    cursor.close()
    conn.close()
    return jsonify({'status': 'success', 'message': 'User added successfully'})

@users_bp.route('/edit_user', methods=['POST'])
@verify_token
def edit_user(current_user):
    data = request.get_json()
    user_id = data['user_id']
    name = data['name']
    email = data['email']
    role = data['role']
    # Accept "User" as a valid role
    if role not in ['Donor', 'User']:
        return jsonify({'status': 'fail', 'message': 'Invalid role'}), 400
    conn = db_conn()
    cursor = conn.cursor(dictionary=True)
    cursor.execute('UPDATE users SET name = %s, email = %s, role = %s WHERE id = %s', (name, email, role, user_id))
    conn.commit()
    cursor.close()
    conn.close()
    return jsonify({'status': 'success', 'message': 'User updated successfully'})

@users_bp.route('/delete_user', methods=['POST'])
@verify_token
def delete_user(current_user):
    data = request.get_json()
    user_id = data['user_id']
    conn = db_conn()
    cursor = conn.cursor(dictionary=True)
    cursor.execute('DELETE FROM users WHERE id = %s', (user_id,))
    conn.commit()
    cursor.close()
    conn.close()
    return jsonify({'status': 'success', 'message': 'User deleted successfully'})