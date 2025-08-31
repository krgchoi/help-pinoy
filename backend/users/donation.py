from flask import Blueprint, request, jsonify
import datetime
import requests
from backend.utils import db_conn, cipher_suite
import os
import hashlib
import json
from web3 import Web3

user_donation_bp = Blueprint('user_donation', __name__)

#START
SEPOLIA_RPC_URL = os.getenv('SEPOLIA_RPC_URL', 'https://sepolia.infura.io/v3/YOUR_INFURA_PROJECT_ID')
CONTRACT_ADDRESS = os.getenv('DONATION_CONTRACT_ADDRESS', '0xYourContractAddress')
CONTRACT_ABI = [
	{
		"inputs": [
			{
				"internalType": "string",
				"name": "txid",
				"type": "string"
			},
			{
				"internalType": "string",
				"name": "hash",
				"type": "string"
			}
		],
		"name": "addDonation",
		"outputs": [],
		"stateMutability": "nonpayable",
		"type": "function"
	},
	{
		"anonymous": False,
		"inputs": [
			{
				"indexed": False,
				"internalType": "string",
				"name": "txid",
				"type": "string"
			},
			{
				"indexed": False,
				"internalType": "string",
				"name": "hash",
				"type": "string"
			},
			{
				"indexed": False,
				"internalType": "uint256",
				"name": "timestamp",
				"type": "uint256"
			}
		],
		"name": "DonationAdded",
		"type": "event"
	},
	{
		"inputs": [
			{
				"internalType": "uint256",
				"name": "",
				"type": "uint256"
			}
		],
		"name": "donations",
		"outputs": [
			{
				"internalType": "string",
				"name": "txid",
				"type": "string"
			},
			{
				"internalType": "string",
				"name": "hash",
				"type": "string"
			},
			{
				"internalType": "uint256",
				"name": "timestamp",
				"type": "uint256"
			}
		],
		"stateMutability": "view",
		"type": "function"
	}
]

web3 = Web3(Web3.HTTPProvider(SEPOLIA_RPC_URL))
contract = web3.eth.contract(address=Web3.to_checksum_address(CONTRACT_ADDRESS), abi=CONTRACT_ABI)
BLOCKCHAIN_PRIVATE_KEY = os.getenv('BLOCKCHAIN_PRIVATE_KEY', '0xYourPrivateKey') 

def write_donation_to_blockchain(txid, record_hash):
    account = web3.eth.account.from_key(BLOCKCHAIN_PRIVATE_KEY)
    nonce = web3.eth.get_transaction_count(account.address)
    txn = contract.functions.addDonation(
        txid, record_hash
    ).build_transaction({
        'from': account.address,
        'nonce': nonce,
        'gas': 200000,
        'gasPrice': web3.to_wei('10', 'gwei')
    })
    signed_txn = web3.eth.account.sign_transaction(txn, private_key=BLOCKCHAIN_PRIVATE_KEY)
    tx_hash = web3.eth.send_raw_transaction(signed_txn.raw_transaction)
    return web3.to_hex(tx_hash)
# END


@user_donation_bp.route('/donation_form', methods=['POST'])
def create_donation():
    data = request.json

    full_name = data['full_name']
    email = data['email']
    contact_number = data['contact_number']
    birthday = data.get('birthday', None)
    amount = data['amount']
    donor_id = data.get('donor_id')

    invoice_payload = {
        "external_id": f"donation_{datetime.datetime.now().timestamp()}",
        "payer_email": email,
        "description": f"Donation from {full_name}",
        "amount": float(amount),
        "success_redirect_url": "http://localhost/help_pinoy/frontend/users/thank_you.php",
    }

    XENDIT_APIKEY = os.getenv('XENDIT_APIKEY')
    xendit_response = requests.post(
        "https://api.xendit.co/v2/invoices",
        auth=(XENDIT_APIKEY, ''),
        json=invoice_payload
    )

    if xendit_response.status_code != 200:
        print("Xendit error:", xendit_response.text)
        return jsonify({"error": "Xendit invoice failed"}), 400

    invoice = xendit_response.json()

    payment_status = invoice.get('status', 'PENDING')
    payment_method = invoice.get('payment_channel', 'Xendit')
    xendit_payment_id = invoice['id']
    donation_date = datetime.datetime.now()

    encrypted_full_name = cipher_suite.encrypt(full_name.encode()).decode()
    encrypted_email = cipher_suite.encrypt(email.encode()).decode()
    encrypted_contact_number = cipher_suite.encrypt(contact_number.encode()).decode()

    conn = db_conn()
    cursor = conn.cursor()
    if donor_id:
        cursor.execute(
            "INSERT INTO donations (donor_id, full_name, email, contact_number, birthday, amount, payment_status, payment_method, xendit_payment_id, donation_date) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            (donor_id, encrypted_full_name, encrypted_email, encrypted_contact_number, birthday, amount, payment_status, payment_method, xendit_payment_id, donation_date)
        )
    else:
        cursor.execute(
            "INSERT INTO donations (full_name, email, contact_number, birthday, amount, payment_status, payment_method, xendit_payment_id, donation_date) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
            (encrypted_full_name, encrypted_email, encrypted_contact_number, birthday, amount, payment_status, payment_method, xendit_payment_id, donation_date)
        )
    conn.commit()
    #START
    try:
        data_string = json.dumps({
			"full_name": full_name,
			"email": email,
			"contact_number": contact_number,
			"birthday": birthday,
			"amount": amount,
			"xendit_payment_id": xendit_payment_id,
			"payment_status": payment_status,
			"payment_method": payment_method,
			"donation_date": str(donation_date)
		}, sort_keys=True)
        
        record_hash = hashlib.sha256(data_string.encode()).hexdigest()
        tx_hash = write_donation_to_blockchain(xendit_payment_id, record_hash)
        receipt = web3.eth.wait_for_transaction_receipt(tx_hash, timeout=120)
        if receipt.status == 1:
            cursor.execute(
                "UPDATE donations SET blockchain_tx = %s WHERE xendit_payment_id = %s",
                (tx_hash, xendit_payment_id)
            )
            conn.commit()
        else:
            print("Blockchain transaction failed: Receipt status 0")
    except Exception as e:
        print("Blockchain write failed:", str(e))
    cursor.close()
    conn.close()
    # END

    return jsonify({
        "invoice_url": invoice['invoice_url']
    })

@user_donation_bp.route('/xendit_webhook', methods=['POST'])
def xendit_webhook():
    data = request.json

    xendit_payment_id = data.get('id')
    payment_status = data.get('status')
    payment_channel = data.get('payment_channel')
    paid_at = data.get('paid_at')
    receipt_url = data.get('receipt_url')

    if not xendit_payment_id:
        return jsonify({'status': 'error', 'message': 'Missing payment ID'}), 400

    conn = db_conn()
    cursor = conn.cursor()
    cursor.execute(
        "UPDATE donations SET payment_status = %s, payment_method = %s, paid_at = %s, receipt_url = %s WHERE xendit_payment_id = %s",
        (payment_status, payment_channel, paid_at, receipt_url, xendit_payment_id)
    )
    conn.commit()
    cursor.close()
    conn.close()

    return jsonify({'status': 'success', 'message': 'Donation updated successfully'})

@user_donation_bp.route('/user_data', methods=['GET'])
def user_data():
    conn = db_conn()
    cursor = conn.cursor(dictionary=True)
    cursor.execute("SELECT * FROM users")
    user_data = cursor.fetchall()
    cursor.close()
    conn.close()
    return jsonify(user_data)
