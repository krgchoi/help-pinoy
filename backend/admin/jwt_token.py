from flask import request, jsonify, current_app
from functools import wraps
import jwt
from backend.utils import db_conn

def verify_token(f):
    @wraps(f)
    def decorated(*args, **kwargs):
        auth_header = request.headers.get('Authorization')

        if not auth_header:
            return jsonify({'status': 'error', 'message': 'Token Missing'}), 401

        try:
            token = auth_header.split(" ")[1]

            data = jwt.decode(
                token,
                current_app.config['SECRET_KEY'],
                algorithms=['HS256']
            )
            
            if data.get('type') != 'access':
                return jsonify({'status': 'error', 'message': 'Invalid token type'}), 401

            conn = db_conn()
            cursor = conn.cursor(dictionary=True)

            cursor.execute("SELECT * FROM admin WHERE id = %s", (data['id'],))
            current_user = cursor.fetchone()

            cursor.close()
            conn.close()

            if not current_user:
                return jsonify({'status': 'error', 'message': 'Invalid user'}), 401

        except jwt.ExpiredSignatureError:
            return jsonify({'status': 'error', 'message': 'Token expired'}), 401

        except jwt.InvalidTokenError:
            return jsonify({'status': 'error', 'message': 'Invalid token'}), 401

        return f(current_user, *args, **kwargs)

    return decorated


