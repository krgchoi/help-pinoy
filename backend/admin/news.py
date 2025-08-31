from flask import Blueprint, jsonify, request, current_app
import os
from werkzeug.utils import secure_filename
from backend.admin.jwt_token import verify_token
from backend.utils import db_conn

news_bp = Blueprint('news', __name__)

ALLOWED_EXTENSIONS = {'png', 'jpg', 'jpeg', 'gif'}

def allowed_file(filename):
    return '.' in filename and filename.rsplit('.', 1)[1].lower() in ALLOWED_EXTENSIONS

@news_bp.route('/upload_news_image', methods=['POST'])
@verify_token
def upload_news_image(current_user):
    if 'image' not in request.files:
        return jsonify({'status': 'fail', 'message': 'No file part'}), 400
    file = request.files['image']
    if file.filename == '':
        return jsonify({'status': 'fail', 'message': 'No selected file'}), 400
    if file and allowed_file(file.filename):
        filename = secure_filename(file.filename)
        static_folder = os.path.abspath(os.path.join(current_app.root_path, '.', 'static', 'news_img'))
        os.makedirs(static_folder, exist_ok=True)
        save_path = os.path.join(static_folder, filename)
        print(f"Saving image to: {save_path}")
        file.save(save_path)
        return jsonify({'status': 'success', 'filename': filename})
    return jsonify({'status': 'fail', 'message': 'Invalid file type'}), 400

@news_bp.route('/news', methods=['GET'])
@verify_token
def get_news(current_user):
    conn = db_conn()
    cursor = conn.cursor(dictionary=True)
    cursor.execute("""
        SELECT news_id, title, content, author, 
               DATE_FORMAT(published_at, '%Y-%m-%d') AS published_at,
               category, tags, image_url, summary, meta_title, meta_description, slug, 
               views_count, status, 
               DATE_FORMAT(updated_at, '%Y-%m-%d') AS updated_at,
               related_disaster_id, is_featured
        FROM news
    """)
    news = cursor.fetchall()
    cursor.close()
    conn.close()
    return jsonify(news)

@news_bp.route('/add_news', methods=['POST'])
@verify_token
def add_news(current_user):
    data = request.get_json()
    title = data['title'] #
    content = data['content'] 
    author = data['author'] #
    published_at = data['published_at']
    category = data['category'] #
    summary = data['summary']
    meta_title = data['meta_title']
    meta_description = data['meta_description']
    slug = data['slug']
 
    tags = data.get('tags')
    image_url = data.get('image_url')
    status = data.get('status', 'draft')
    related_disaster_id = data.get('related_disaster_id')
    is_featured = data.get('is_featured', False)

    conn = db_conn()
    cursor = conn.cursor(dictionary=True)
    cursor.execute("""
        INSERT INTO news 
        (title, content, author, published_at, category, tags, image_url, summary, meta_title, meta_description, slug, status, related_disaster_id, is_featured)
        VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
    """, (
        title, content, author, published_at, category, tags, image_url, summary, meta_title, meta_description, slug, status, related_disaster_id, is_featured
    ))
    conn.commit()
    cursor.close()
    conn.close()
    return jsonify({'status': 'success', 'message': 'News added successfully'})

@news_bp.route('/delete_news', methods=['POST'])
@verify_token
def delete_news(current_user):
    data = request.get_json()
    news_id = data['news_id']
    conn = db_conn()
    cursor = conn.cursor(dictionary=True)
    cursor.execute('DELETE FROM news WHERE news_id = %s', (news_id,))
    conn.commit()
    cursor.close()
    conn.close()
    return jsonify({'status': 'success', 'message': 'News deleted successfully'})

@news_bp.route('/edit_news', methods=['POST'])
@verify_token
def edit_news(current_user):
    data = request.get_json()
    news_id = data['news_id']
    title = data['title']
    content = data['content']
    author = data['author']
    category = data['category']
    summary = data['summary']
    meta_title = data['meta_title']
    meta_description = data['meta_description']
    slug = data['slug']
    tags = data.get('tags')
    image_url = data.get('image_url')
    status = data.get('status', 'draft')
    related_disaster_id = data.get('related_disaster_id')
    is_featured = data.get('is_featured', False)

    conn = db_conn()
    cursor = conn.cursor(dictionary=True)
    cursor.execute("""
        UPDATE news 
        SET title=%s, content=%s, author=%s, category=%s, summary=%s, meta_title=%s, meta_description=%s, slug=%s, tags=%s, image_url=%s, status=%s, related_disaster_id=%s, is_featured=%s
        WHERE news_id=%s
    """, (
        title, content, author, category, summary, meta_title, meta_description, slug, tags, image_url, status, related_disaster_id, is_featured, news_id
    ))
    conn.commit()
    cursor.close()
    conn.close()
    return jsonify({'status': 'success', 'message': 'News updated successfully'})