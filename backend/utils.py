import mysql.connector
from cryptography.fernet import Fernet
import os

KEY_FILE = os.path.join(os.path.dirname(__file__), 'encryption_key.key')

def create_key():
    try:
        with open(KEY_FILE, 'rb') as key_file:
            return key_file.read()
    except FileNotFoundError:
        key = Fernet.generate_key()
        with open(KEY_FILE, 'wb') as key_file:
            key_file.write(key)
        return key

key = create_key()
cipher_suite = Fernet(key)

def db_conn():
    return mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="help_pinoy"
    )
