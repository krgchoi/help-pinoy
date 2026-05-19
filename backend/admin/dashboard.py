from flask import Blueprint, jsonify
from backend.admin.jwt_token import verify_token
from backend.utils import db_conn, cipher_suite

dashboard_bp = Blueprint('dashboard', __name__)

@dashboard_bp.route('/dashboard_data', methods=['GET'])
@verify_token
def dashboard_data(current_user):
    conn = db_conn()
    cursor = conn.cursor(dictionary=True)

    cursor.execute("SELECT SUM(amount) AS total FROM temp_donations WHERE donation_status = 'APPROVED'")
    sd = cursor.fetchone()['total'] or 0

    cursor.execute("""
        SELECT SUM(amount) AS total 
        FROM temp_donations 
        WHERE donation_status = 'APPROVED'
        AND MONTH(donation_date) = MONTH(CURDATE()) 
        AND YEAR(donation_date) = YEAR(CURDATE())
    """)
    sd_month = cursor.fetchone()['total'] or 0

    cursor.execute("""
        SELECT COUNT(DISTINCT donor_id) AS dontotal 
        FROM temp_donations 
        WHERE donor_id IS NOT NULL 
        AND donation_status = 'APPROVED'
    """)
    td = cursor.fetchone()['dontotal'] or 0

    cursor.execute("SELECT COUNT(*) AS total FROM users")
    tu = cursor.fetchone()['total'] or 0

    # cursor.execute("""
    #     SELECT payment_method, COUNT(*) AS total 
    #     FROM donations 
    #     WHERE payment_status = 'PAID'
    #     GROUP BY payment_method
    # """)
    # dm = cursor.fetchall()

    dm = [] 

    cursor.execute("""
        SELECT donation_status, COUNT(*) AS count 
        FROM temp_donations 
        GROUP BY donation_status
    """)
    dr = cursor.fetchall()

    cursor.execute("""
        SELECT DATE_FORMAT(donation_date, '%Y-%m') AS month, SUM(amount) AS total_donations
        FROM temp_donations 
        WHERE donation_status = 'APPROVED'
        AND donation_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01') - INTERVAL 2 MONTH
        GROUP BY DATE_FORMAT(donation_date, '%Y-%m')
        ORDER BY month DESC
    """)
    dt = cursor.fetchall()

    cursor.execute("""
        SELECT full_name, email, amount, donation_status, donation_date 
        FROM temp_donations 
        ORDER BY donation_date DESC 
        LIMIT 3
    """)
    rd = cursor.fetchall()

    for donation in rd:
        try:
            donation['full_name'] = cipher_suite.decrypt(donation['full_name'].encode()).decode()
            donation['email'] = cipher_suite.decrypt(donation['email'].encode()).decode()
        except:
            donation['full_name'] = donation['full_name'] or "N/A"
            donation['email'] = donation['email'] or "N/A"


    cursor.execute("""
        SELECT full_name, SUM(amount) AS total 
        FROM temp_donations 
        WHERE donation_status = 'APPROVED'
        GROUP BY full_name 
        ORDER BY total DESC 
        LIMIT 3
    """)
    tp = cursor.fetchall()

    for donor in tp:
        try:
            donor['full_name'] = cipher_suite.decrypt(donor['full_name'].encode()).decode()
        except:
            donor['full_name'] = donor['full_name'] or "N/A"

    cursor.execute("""
        SELECT DATE_FORMAT(donation_date, '%M') AS month, SUM(amount) AS total_donations
        FROM temp_donations 
        WHERE donation_status = 'APPROVED'
        AND donation_date >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
        GROUP BY YEAR(donation_date), MONTH(donation_date)
        ORDER BY YEAR(donation_date), MONTH(donation_date)
    """)
    dtr = cursor.fetchall()
    
    cursor.execute("SELECT COUNT(*) AS total FROM temp_donations WHERE donation_status = 'PENDING'")
    pending_total = cursor.fetchone()['total'] or 0

    cursor.execute("SELECT COUNT(*) AS total FROM temp_donations WHERE donation_status = 'UNDER_REVIEW'")
    review_total = cursor.fetchone()['total'] or 0

    cursor.execute("SELECT COUNT(*) AS total FROM temp_donations WHERE donation_status = 'REJECTED'")
    rejected_total = cursor.fetchone()['total'] or 0
    
    #remaining_funds
    cursor.execute("SELECT SUM(remaining_amount) AS total FROM temp_donations WHERE donation_status = 'APPROVED'")
    total_approved = cursor.fetchone()['total'] or 0
    
    #GET ALL APPROVE STATUS DEVIDE TOTAL COUNT OF DONATIONS
    cursor.execute("SELECT COUNT(*) AS total FROM temp_donations")
    count_donations = cursor.fetchone()['total'] or 0
    
    cursor.execute("SELECT COUNT(*) AS total FROM temp_donations WHERE donation_status = 'APPROVED'")
    count_approve = cursor.fetchone()['total'] or 0

    if count_donations > 0:
        approval_rate = (count_approve / count_donations) * 100
    else:
        approval_rate = 0

    cursor.close()
    conn.close()

    return jsonify({
        'sd': sd,
        'sd_month': sd_month,
        'td': td,
        'tu': tu,
        'dm': dm,  #empty
        'dr': dr,
        'dt': dt,
        'rd': rd,
        'tp': tp,
        'dtr': dtr,
        'review_total': review_total,
        'rejected_total': rejected_total,
        'pending_total': pending_total,
        'remaining_funds': total_approved,
        'completion_rate': approval_rate
    })