from flask import Blueprint, jsonify, request
from backend.admin.jwt_token import verify_token
from backend.utils import db_conn, cipher_suite
import hashlib
from web3 import Web3
import os

donation_bp = Blueprint('donation', __name__)

# @donation_bp.route('/donations', methods=['GET'])
# @verify_token
# def get_donations(current_user):
#     conn = db_conn()
#     cursor = conn.cursor(dictionary=True)
#     cursor.execute("SELECT donation_id, donor_id, full_name, email, contact_number, birthday, amount, payment_status, payment_method, xendit_payment_id, paid_at, receipt_url, donation_date FROM donations ORDER BY donation_date DESC")
#     donations = cursor.fetchall()
#     for donation in donations:
#         try:
#             donation['full_name'] = cipher_suite.decrypt(donation['full_name'].encode()).decode()
#             donation['email'] = cipher_suite.decrypt(donation['email'].encode()).decode()
#             donation['contact_number'] = cipher_suite.decrypt(donation['contact_number'].encode()).decode()
#         except Exception:
#             donation['full_name'] = "fail to decrypt"
#             donation['email'] = "fail to decrypt"
#             donation['contact_number'] = "fail to decrypt"
#     cursor.close()
#     conn.close()
#     return jsonify(donations)

BLOCKCHAIN_RPC_URL = os.getenv('BLOCKCHAIN_RPC_URL')
CONTRACT_ADDRESS = os.getenv('DONATION_CONTRACT_ADDRESS')
PRIVATE_KEY = os.getenv('BLOCKCHAIN_PRIVATE_KEY')

web3 = Web3(Web3.HTTPProvider(BLOCKCHAIN_RPC_URL))

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
				"internalType": "string",
				"name": "txid",
				"type": "string"
			}
		],
		"name": "getDonation",
		"outputs": [
			{
				"internalType": "string",
				"name": "",
				"type": "string"
			},
			{
				"internalType": "string",
				"name": "",
				"type": "string"
			},
			{
				"internalType": "uint256",
				"name": "",
				"type": "uint256"
			}
		],
		"stateMutability": "view",
		"type": "function"
	},
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
		"name": "verifyDonation",
		"outputs": [
			{
				"internalType": "bool",
				"name": "",
				"type": "bool"
			}
		],
		"stateMutability": "view",
		"type": "function"
	}
]

contract = web3.eth.contract(
    address=Web3.to_checksum_address(CONTRACT_ADDRESS),
    abi=CONTRACT_ABI
)

if not web3.is_connected():
    print("❌ Blockchain connection failed")
else:
    print("✅ Connected to blockchain")

@donation_bp.route('/donations', methods=['GET'])
@verify_token
def get_temp_donations(current_user):
    conn = db_conn()
    cursor = conn.cursor(dictionary=True)

    cursor.execute("""
        SELECT 
            donation_id,
            public_id,
            donor_id,
            full_name,
            email,
            contact_number,
            birthday,
            amount,
            donation_status,
            proof_image,receipt_reference,
            donation_date
        FROM temp_donations
        ORDER BY donation_date DESC
    """)

    donations = cursor.fetchall()

    for donation in donations:
        try:
            donation['full_name'] = cipher_suite.decrypt(donation['full_name'].encode()).decode()
            donation['email'] = cipher_suite.decrypt(donation['email'].encode()).decode()
            donation['contact_number'] = cipher_suite.decrypt(donation['contact_number'].encode()).decode()
        except:
            donation['full_name'] = "decrypt error"

    cursor.close()
    conn.close()

    return jsonify(donations)

@donation_bp.route('/update_donation_status', methods=['POST'])
@verify_token
def update_donation_status(current_user):

    data = request.get_json()
    if not data:
        return jsonify({"status": "error", "message": "No DATA received"}), 400

    donation_id = data.get('donation_id')
    status = data.get('donation_status')

    conn = db_conn()
    cursor = conn.cursor(dictionary=True)

    cursor.execute("SELECT donation_status FROM temp_donations WHERE donation_id = %s", (donation_id,))
    row = cursor.fetchone()

    if not row:
        return jsonify({"status": "error", "message": "Donation not found"}), 404

    current_status = row['donation_status']

    if current_status in ["APPROVED", "REJECTED"]:
        return jsonify({
            "status": "error",
            "message": "Finalized donations cannot be modified"
        }), 403

    if status == "APPROVED":

        cursor.execute("""
            SELECT amount, donation_date, public_id, blockchain_tx
            FROM temp_donations 
            WHERE donation_id = %s
        """, (donation_id,))
        result = cursor.fetchone()

        if result and result.get("blockchain_tx"):
            return jsonify({
                "status": "error",
                "message": "Already recorded on blockchain"
            }), 400
            
        if not result:
            return jsonify({"status": "error", "message": "Donation not found"}), 404

        amount = result['amount']
        donation_date = result['donation_date']
        public_id = result['public_id']

        # ✅ deterministic hash
        amount_str = "{:.2f}".format(float(amount))
        date_str = donation_date.strftime("%Y-%m-%dT%H:%M:%S")
        status_str = status.upper()

        data_string = f"{public_id}|{amount_str}|{date_str}|{status_str}"
        record_hash = hashlib.sha256(data_string.encode()).hexdigest()

        # 🔗 write to blockchain
        blockchain_tx = write_to_blockchain(public_id, record_hash)

        if not blockchain_tx:
            conn.rollback()
            return jsonify({
                "status": "error",
                "message": "Blockchain transaction failed"
            }), 500

        # ✅ update DB ONLY after success
        cursor.execute("""
            UPDATE temp_donations
            SET donation_status = %s,
                verified_at = NOW(),
                reviewed_by = %s,
                record_hash = %s,
                blockchain_tx = %s
            WHERE donation_id = %s
        """, (status, current_user['id'], record_hash, blockchain_tx, donation_id))

    elif status == "REJECTED":
        cursor.execute("""
            UPDATE temp_donations
            SET donation_status = %s,
                verified_at = NOW(),
                reviewed_by = %s
            WHERE donation_id = %s
        """, (status, current_user['id'], donation_id))

    else:
        cursor.execute("""
            UPDATE temp_donations
            SET donation_status = %s
            WHERE donation_id = %s
        """, (status, donation_id))

    conn.commit()
    cursor.close()
    conn.close()

    return jsonify({"status": "success"})

def write_to_blockchain(public_id, record_hash):
    try:
        account = web3.eth.account.from_key(PRIVATE_KEY)
        nonce = web3.eth.get_transaction_count(account.address, 'pending')
        
        gas_price = web3.eth.gas_price

        txn = contract.functions.addDonation(
            public_id,
            record_hash
        ).build_transaction({
            'from': account.address,
            'nonce': nonce,
            'gas': 200000,
            'gasPrice': gas_price
        })

        signed_txn = web3.eth.account.sign_transaction(txn, PRIVATE_KEY)
        tx_hash = web3.eth.send_raw_transaction(signed_txn.raw_transaction)

        # ✅ WAIT FOR CONFIRMATION (important)
        receipt = web3.eth.wait_for_transaction_receipt(tx_hash, timeout=120)

        if receipt.status != 1:
            raise Exception("Transaction failed on-chain")

        return web3.to_hex(tx_hash)

    except Exception as e:
        print("Blockchain error:", str(e))
        return None
    
@donation_bp.route('/test_blockchain', methods=['GET'])
def test_blockchain():
    tx = write_to_blockchain("test123", "abc123")
    return jsonify({"tx_hash": tx})