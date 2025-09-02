from flask import request, jsonify, current_app
from functools import wraps
import jwt
from backend.utils import db_conn

def verify_token(f):
    @wraps(f)
    def decorated(*args, **kwargs):
        token = request.headers.get('get-token')
        if not token:
            return jsonify({'status': 'error', 'message': 'Token Missing'}),
        try:
            data = jwt.decode(token, current_app.config['SECRET_KEY'], algorithms=['HS256'])
            conn = db_conn()
            cursor = conn.cursor(dictionary=True)
            cursor.execute("SELECT * FROM admin WHERE id = %s", (data['id'],))
            current_user = cursor.fetchone()
            cursor.close()
            conn.close()
            if not current_user:
                return jsonify({'status': 'error', 'message': 'Invalid user'})
        except jwt.ExpiredSignatureError:
            return jsonify({'status': 'expire', 'message': 'Token expired'})
        except jwt.InvalidTokenError:
            return jsonify({'status': 'error', 'message': 'Token Errors'})
        return f(current_user, *args, **kwargs)
    return decorated