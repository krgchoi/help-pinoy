from flask import Blueprint, jsonify, request
from backend.admin.jwt_token import verify_token
from backend.utils import db_conn, cipher_suite

donation_bp = Blueprint('donation', __name__)

@donation_bp.route('/donations', methods=['GET'])
@verify_token
def get_donations(current_user):
    conn = db_conn()
    cursor = conn.cursor(dictionary=True)
    cursor.execute("SELECT donation_id, donor_id, full_name, email, contact_number, birthday, amount, payment_status, payment_method, xendit_payment_id, paid_at, receipt_url, donation_date FROM donations ORDER BY donation_date DESC")
    donations = cursor.fetchall()
    for donation in donations:
        try:
            donation['full_name'] = cipher_suite.decrypt(donation['full_name'].encode()).decode()
            donation['email'] = cipher_suite.decrypt(donation['email'].encode()).decode()
            donation['contact_number'] = cipher_suite.decrypt(donation['contact_number'].encode()).decode()
        except Exception:
            donation['full_name'] = "fail to decrypt"
            donation['email'] = "fail to decrypt"
            donation['contact_number'] = "fail to decrypt"
    cursor.close()
    conn.close()
    return jsonify(donations)