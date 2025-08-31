from werkzeug.security import generate_password_hash
from cryptography.fernet import Fernet

password = "pass"  # Your desired password
hashed_password = generate_password_hash(password)

print("Hashed Password:", hashed_password)
print(Fernet.generate_key())

