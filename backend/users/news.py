from flask import Blueprint, request, jsonify
from backend.utils import db_conn, cipher_suite

news_users_bp = Blueprint('news_users', __name__)

@news_users_bp.route('/news_users', methods=['POST'])
def news_users():
    conn = db_conn()
    cursor = conn.cursor(dictionary=True)
    cursor.execute("""
        SELECT news_id, title, content, author, 
               DATE_FORMAT(published_at, '%Y-%m-%d') AS published_at,
               category, tags, image_url, summary, slug
        FROM news
        WHERE status = 'published'
        ORDER BY published_at DESC
    """)
    news = cursor.fetchall()
    cursor.close()
    conn.close()
    return jsonify(news)

@news_users_bp.route('/get_news', methods=['GET'])
def get_news():
    conn = db_conn()
    cursor = conn.cursor(dictionary=True)
    cursor.execute("""
        SELECT news_id, title, content, author, 
               DATE_FORMAT(published_at, '%Y-%m-%d') AS published_at,
               category, tags, image_url, summary, slug, is_featured
        FROM news
        WHERE status = 'published'
        ORDER BY published_at DESC
        LIMIT 6
    """)
    news = cursor.fetchall()
    cursor.close()
    conn.close()
    return jsonify(news)


