from flask import Flask
from flask_mail import Mail
from dotenv import load_dotenv
import os
from flask_cors import CORS

load_dotenv()

app = Flask(__name__)
CORS(app)  # Enable CORS for all routes

#utils bp
from backend.utils import db_conn

#admin blueprints
from backend.admin.dashboard import dashboard_bp
from backend.admin.donation import donation_bp
from backend.admin.news import news_bp
from backend.admin.centers import centers_bp
from backend.admin.users import users_bp
from backend.admin.auth import admin_auth_bp

app.register_blueprint(dashboard_bp, url_prefix='/admin')
app.register_blueprint(donation_bp, url_prefix='/admin')
app.register_blueprint(news_bp, url_prefix='/admin')
app.register_blueprint(centers_bp, url_prefix='/admin')
app.register_blueprint(users_bp, url_prefix='/admin')
app.register_blueprint(admin_auth_bp, url_prefix='/admin')


#users blueprints
from backend.users.register import register_bp
from backend.users.donation import user_donation_bp
from backend.users.profile import profile_bp  
from backend.users.auth import auth_bp  
from backend.users.news import news_users_bp
from backend.users.centers import user_centers_bp

app.register_blueprint(register_bp, url_prefix='/user')
app.register_blueprint(user_donation_bp, url_prefix='/user')
app.register_blueprint(profile_bp, url_prefix='/user') 
app.register_blueprint(auth_bp, url_prefix='/user')  
app.register_blueprint(news_users_bp, url_prefix='/user') 
app.register_blueprint(user_centers_bp, url_prefix='/user')


#Keys
app.config['SECRET_KEY'] = os.getenv('SECRET_KEY')
XENDIT_APIKEY = os.getenv('XENDIT_APIKEY')
app.config['MAIL_USERNAME'] = os.getenv('MAIL_USERNAME')
app.config['MAIL_PASSWORD'] = os.getenv('MAIL_PASSWORD')

# # ===== OTP CRED=====
app.config['MAIL_SERVER'] = 'smtp.gmail.com'
app.config['MAIL_PORT'] = 587
app.config['MAIL_USE_TLS'] = True
mail = Mail(app)

if __name__ == '__main__':
    app.run(debug=True)
