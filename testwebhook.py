from flask import Flask, request, jsonify

app = Flask(__name__)

@app.route("/user/xendit_webhook", methods=["POST"])
def test_webhook():
    data = request.json
    print("Webhook received:", data)
    return jsonify({"status": "success", "message": "Webhook received!"})

@app.route("/api/test", methods=["GET"])
def api_test():
    return {"status": "success", "message": "Flask backend is working!"}

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000, debug=True)
