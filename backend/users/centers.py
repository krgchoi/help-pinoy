from flask import Blueprint, jsonify
import requests
from backend.utils import db_conn

user_centers_bp = Blueprint('user_centers_bp', __name__)


@user_centers_bp.route('/user_get_locations', methods=['GET'])
def public_get_locations():
    conn = db_conn()
    cursor = conn.cursor(dictionary=True)
    cursor.execute('SELECT id, name, address, contact_number, email, operating_hours, type, website_url, latitude, longitude FROM locations')
    locations = cursor.fetchall()
    cursor.close()
    conn.close()
    return jsonify(locations)