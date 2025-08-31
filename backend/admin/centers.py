from flask import Blueprint, jsonify, request
from backend.admin.jwt_token import verify_token
from backend.utils import db_conn

centers_bp = Blueprint('centers', __name__)

@centers_bp.route('/get_locations', methods=['GET'])
@verify_token
def get_locations(current_user):
    conn = db_conn()
    cursor = conn.cursor(dictionary=True)
    cursor.execute('SELECT * FROM locations')
    locations = cursor.fetchall()
    cursor.close()
    conn.close()
    return jsonify(locations)

@centers_bp.route('/add_location', methods=['POST'])
@verify_token
def add_location(current_user):
    data = request.get_json()
    name = data['name']
    address = data['address']
    latitude = data['latitude']
    longitude = data['longitude']
    contact_number = data.get('contact_number')
    email = data.get('email')
    operating_hours = data.get('operating_hours')
    type_ = data.get('type')
    website_url = data.get('website_url')

    conn = db_conn()
    cursor = conn.cursor(dictionary=True)
    cursor.execute(
        'INSERT INTO locations (name, address, latitude, longitude, contact_number, email, operating_hours, type, website_url) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)',
        (name, address, latitude, longitude, contact_number, email, operating_hours, type_, website_url)
    )
    conn.commit()
    cursor.close()
    conn.close()
    return jsonify({'status': 'success', 'message': 'Location added successfully'})

@centers_bp.route('/edit_location', methods=['POST'])
@verify_token
def edit_location(current_user):
    data = request.get_json()
    location_id = data['location_id']
    name = data['name']
    address = data['address']
    latitude = data['latitude']
    longitude = data['longitude']
    contact_number = data.get('contact_number')
    email = data.get('email')
    operating_hours = data.get('operating_hours')
    type_ = data.get('type')
    website_url = data.get('website_url')

    conn = db_conn()
    cursor = conn.cursor(dictionary=True)
    cursor.execute(
        'UPDATE locations SET name = %s, address = %s, latitude = %s, longitude = %s, contact_number = %s, email = %s, operating_hours = %s, type = %s, website_url = %s WHERE id = %s',
        (name, address, latitude, longitude, contact_number, email, operating_hours, type_, website_url, location_id)
    )
    conn.commit()
    cursor.close()
    conn.close()
    return jsonify({'status': 'success', 'message': 'Location updated successfully'})

@centers_bp.route('/delete_location', methods=['POST'])
@verify_token
def delete_location(current_user):
    data = request.get_json()
    location_id = data['location_id']
    conn = db_conn()
    cursor = conn.cursor(dictionary=True)
    cursor.execute('DELETE FROM locations WHERE id = %s', (location_id,))
    conn.commit()
    cursor.close()
    conn.close()
    return jsonify({'status': 'success', 'message': 'Location deleted successfully'})

