from flask import Blueprint, jsonify
from backend.admin.jwt_token import verify_token
from backend.utils import db_conn, cipher_suite

dashboard_bp = Blueprint('dashboard', __name__)

@dashboard_bp.route('/dashboard_data', methods=['GET'])
@verify_token
def dashboard_data(current_user):
    conn = db_conn()
    cursor = conn.cursor(dictionary=True)

    #Total donations
    cursor.execute("SELECT SUM(amount) AS total FROM donations WHERE payment_status = 'PAID'")
    sd = cursor.fetchone()['total'] or 0

    #Total donations for this month
    cursor.execute("""
        SELECT SUM(amount) AS total 
        FROM donations 
        WHERE payment_status = 'PAID'
        AND MONTH(donation_date) = MONTH(CURDATE()) 
        AND YEAR(donation_date) = YEAR(CURDATE())
    """)
    sd_month = cursor.fetchone()['total'] or 0

    #Total unique donors
    cursor.execute("""
        SELECT COUNT(DISTINCT donor_id) AS dontotal 
        FROM donations 
        WHERE donor_id IS NOT NULL AND payment_status = 'PAID'
    """)
    td = cursor.fetchone()['dontotal'] or 0

    # Total users
    cursor.execute("SELECT COUNT(*) AS total FROM users")
    tu = cursor.fetchone()['total'] or 0

    # Payment method distribution
    cursor.execute("""
        SELECT payment_method, COUNT(*) AS total 
        FROM donations 
        WHERE payment_status = 'PAID'
        GROUP BY payment_method
    """)
    dm = cursor.fetchall()

    # Donation status distribution
    cursor.execute("SELECT payment_status, COUNT(*) AS count FROM donations GROUP BY payment_status")
    dr = cursor.fetchall()

    # Donation trends
    cursor.execute("""
        SELECT DATE_FORMAT(donation_date, '%Y-%m') AS month, SUM(amount) AS total_donations
        FROM donations 
        WHERE payment_status = 'PAID'
        AND donation_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01') - INTERVAL 2 MONTH
        GROUP BY DATE_FORMAT(donation_date, '%Y-%m')
        ORDER BY month DESC
    """)
    dt = cursor.fetchall()

    # Recent donations
    cursor.execute("""
        SELECT full_name, email, amount, payment_status, donation_date 
        FROM donations 
        WHERE payment_status = 'PAID'
        ORDER BY donation_date DESC 
        LIMIT 3
    """)
    rd = cursor.fetchall()
    for donation in rd:
        try:
            donation['full_name'] = cipher_suite.decrypt(donation['full_name'].encode()).decode()
            donation['email'] = cipher_suite.decrypt(donation['email'].encode()).decode()
        except Exception as e:
            donation['full_name'] = "fail to decrypt"
            donation['email'] = "fail to decrypt"

    # Top donors
    cursor.execute("""
        SELECT full_name, SUM(amount) AS total 
        FROM donations 
        WHERE payment_status = 'PAID'
        GROUP BY full_name 
        ORDER BY total DESC 
        LIMIT 3
    """)
    tp = cursor.fetchall()
    for donor in tp:
        try:
            donor['full_name'] = cipher_suite.decrypt(donor['full_name'].encode()).decode()
        except Exception as e:
            donor['full_name'] = "fail to decrypt"

    #Donation trends
    cursor.execute("""
        SELECT DATE_FORMAT(donation_date, '%M') AS month, SUM(amount) AS total_donations
        FROM donations 
        WHERE payment_status = 'PAID'
        AND donation_date >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
        GROUP BY YEAR(donation_date), MONTH(donation_date)
        ORDER BY YEAR(donation_date), MONTH(donation_date)
    """)
    dtr = cursor.fetchall()

    #Pending
    cursor.execute("SELECT COUNT(*) AS total FROM donations WHERE payment_status = 'PENDING'")
    pending_total = cursor.fetchone()['total'] or 0

    #Expired
    cursor.execute("SELECT COUNT(*) AS total FROM donations WHERE payment_status = 'EXPIRED'")
    expired_total = cursor.fetchone()['total'] or 0

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
        'dtr': dtr,
        'pending_total': pending_total,
        'expired_total': expired_total
    })
