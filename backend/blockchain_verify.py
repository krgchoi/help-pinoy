from web3 import Web3
import os
import hashlib
from backend.utils import db_conn

web3 = Web3(Web3.HTTPProvider(os.getenv('BLOCKCHAIN_RPC_URL')))

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
    address=Web3.to_checksum_address(os.getenv('DONATION_CONTRACT_ADDRESS')),
    abi=CONTRACT_ABI
)

def verify_donation_record(public_id):
    conn = db_conn()
    cursor = conn.cursor(dictionary=True)

    cursor.execute("""
        SELECT public_id, amount, donation_date, donation_status, record_hash
        FROM temp_donations
        WHERE public_id = %s
    """, (public_id,))
    
    donation = cursor.fetchone()
    cursor.close()
    conn.close()

    if not donation:
        return {"status": False, "message": "Not found"}

    # rebuild hash (MUST MATCH your approval logic exactly)
    amount_str = "{:.2f}".format(float(donation['amount']))
    date_str = donation['donation_date'].isoformat()
    status_str = donation['donation_status'].upper()

    data_string = f"{public_id}|{amount_str}|{date_str}|{status_str}"
    computed_hash = hashlib.sha256(data_string.encode()).hexdigest()

    # compare local first (basic integrity)
    if computed_hash != donation['record_hash']:
        return {"status": False, "message": "Local data tampered"}

    # check blockchain
    is_valid = contract.functions.verifyDonation(
        public_id,
        computed_hash
    ).call()

    return {
        "status": is_valid,
        "message": "Verified" if is_valid else "Blockchain mismatch"
    }