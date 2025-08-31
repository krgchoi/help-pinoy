import smtplib

smtp_server = "smtp.gmail.com"
port = 587
login = "kida.guillem.ui@phinmaed.com"
password = "fnut gvrv uets tuyi" 

try:
    server = smtplib.SMTP(smtp_server, port)
    server.starttls()
    server.login(login, password)
    print(" Login successful")
except Exception as e:
    print(" Login failed:", e)
finally:
    server.quit()
