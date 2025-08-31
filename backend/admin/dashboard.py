from flask import Blueprint, jsonify
from backend.admin.jwt_token import verify_token
from backend.utils import db_conn, cipher_suite

dashboard_bp = Blueprint('dashboard', __name__)

@dashboard_bp.route('/dashboard_data', methods=['GET'])
@verify_token
def dashboard_data(current_user):
    conn = db_conn()
    cursor = conn.cursor(dictionary=True)

    cursor.execute("SELECT SUM(amount) AS total FROM donations")
    sd = cursor.fetchone()['total']

    cursor.execute("SELECT SUM(amount) AS total FROM donations WHERE MONTH(donation_date) = MONTH(CURDATE()) AND YEAR(donation_date) = YEAR(CURDATE())")
    sd_month = cursor.fetchone()['total']

    cursor.execute("SELECT COUNT(DISTINCT donor_id) AS dontotal FROM donations WHERE donor_id IS NOT NULL")
    td = cursor.fetchone()['dontotal']

    cursor.execute("SELECT COUNT(*) AS total FROM users")
    tu = cursor.fetchone()['total']

    cursor.execute("SELECT payment_method, COUNT(*) AS total FROM donations GROUP BY payment_method")
    dm = cursor.fetchall()

    cursor.execute("SELECT payment_status, COUNT(*) AS count FROM donations GROUP BY payment_status")
    dr = cursor.fetchall()

    cursor.execute("SELECT DATE_FORMAT(donation_date, '%Y-%m') AS month, SUM(amount) AS total_donations FROM donations WHERE donation_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01') - INTERVAL 2 MONTH GROUP BY DATE_FORMAT(donation_date, '%Y-%m') ORDER BY month DESC")
    dt = cursor.fetchall()

    cursor.execute("SELECT full_name, email, amount, payment_status, donation_date FROM donations ORDER BY donation_date DESC LIMIT 3")
    rd = cursor.fetchall()
    for donation in rd:
        try:
            donation['full_name'] = cipher_suite.decrypt(donation['full_name'].encode()).decode()
            donation['email'] = cipher_suite.decrypt(donation['email'].encode()).decode()
        except Exception as e:
            print("Decryption error (recent donations):", donation['full_name'], donation['email'], type(e), str(e))
            donation['full_name'] = "fail to decrypt"
            donation['email'] = "fail to decrypt"

    cursor.execute("SELECT full_name, SUM(amount) AS total FROM donations GROUP BY full_name ORDER BY total DESC LIMIT 3")
    tp = cursor.fetchall()
    for donor in tp:
        try:
            donor['full_name'] = cipher_suite.decrypt(donor['full_name'].encode()).decode()
        except Exception as e:
            print("Decryption error (top donors):", donor['full_name'], type(e), str(e))
            donor['full_name'] = "fail to decrypt"

    cursor.execute("SELECT DATE_FORMAT(donation_date, '%M') AS month, SUM(amount) AS total_donations FROM donations WHERE donation_date >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH) GROUP BY YEAR(donation_date), MONTH(donation_date) ORDER BY YEAR(donation_date), MONTH(donation_date)")
    dtr = cursor.fetchall()

    cursor.close()
    conn.close()

    return jsonify({
        'sd': sd,
        'sd_month': sd_month,
        'td': td,
        'tu': tu,
        'dm': dm,
        'dr': dr,
        'dt': dt,
        'rd': rd,
        'tp': tp,
        'dtr': dtr
    })