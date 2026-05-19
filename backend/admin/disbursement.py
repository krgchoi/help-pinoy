from flask import Blueprint, request, jsonify
from backend.utils import db_conn
from backend.admin.jwt_token import verify_token
import hashlib
from web3 import Web3
import os
import time
import datetime

disbursement_bp = Blueprint('disbursement_bp', __name__)

BLOCKCHAIN_RPC_URL = os.getenv('BLOCKCHAIN_RPC_URL')
CONTRACT_ADDRESS = os.getenv('DONATION_CONTRACT_ADDRESS')
PRIVATE_KEY = os.getenv('BLOCKCHAIN_PRIVATE_KEY')

web3 = Web3(Web3.HTTPProvider(BLOCKCHAIN_RPC_URL))

contract = web3.eth.contract(
    address=Web3.to_checksum_address(CONTRACT_ADDRESS),
    abi=[
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
    "name": "addDisbursement",
    "outputs": [],
    "stateMutability": "nonpayable",
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
    "name": "addDonation",
    "outputs": [],
    "stateMutability": "nonpayable",
    "type": "function"
  },
  {
    "inputs": [],
    "stateMutability": "nonpayable",
    "type": "constructor"
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
    "name": "DisbursementAdded",
    "type": "event"
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
    "name": "getDisbursement",
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
    "inputs": [],
    "name": "owner",
    "outputs": [
      {
        "internalType": "address",
        "name": "",
        "type": "address"
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
    "name": "verifyDisbursement",
    "outputs": [
      {
        "internalType": "bool",
        "name": "",
        "type": "bool"
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
)

def write_disbursement_to_blockchain(txid, record_hash):
    try:
        account = web3.eth.account.from_key(PRIVATE_KEY)

        print("CONTRACT OWNER:", contract.functions.owner().call())
        print("BACKEND WALLET:", account.address)


        nonce = web3.eth.get_transaction_count(account.address, 'pending')

        txn = contract.functions.addDisbursement(
            txid,
            record_hash
        ).build_transaction({
            'from': account.address,
            'nonce': nonce,
            'gas': 300000,
            'gasPrice': web3.eth.gas_price
        })

        signed_txn = web3.eth.account.sign_transaction(txn, PRIVATE_KEY)
        tx_hash = web3.eth.send_raw_transaction(signed_txn.raw_transaction)

        print("Sent TX:", web3.to_hex(tx_hash))

        receipt = web3.eth.wait_for_transaction_receipt(tx_hash, timeout=120)

        if receipt.status != 1:
            raise Exception("Disbursement failed on-chain")

        return web3.to_hex(tx_hash)

    except Exception as e:
        print("Disbursement blockchain error:", str(e))
        return None


@disbursement_bp.route('/create_disbursement', methods=['POST'])
@verify_token
def create_disbursement(current_user):

    data = request.get_json()

    if not data:
        return jsonify({'status': 'error', 'message': 'No data received'}), 400

    amount = data.get('amount')
    project_name = data.get('project_name')

    if not amount or not project_name:
        return jsonify({'status': 'error', 'message': 'Missing fields'}), 400

    conn = db_conn()
    cursor = conn.cursor(dictionary=True)

    try:
        cursor.execute("""
            SELECT SUM(remaining_amount) AS total_available
            FROM temp_donations
            WHERE donation_status = 'APPROVED'
            AND remaining_amount > 0
        """)
        result = cursor.fetchone()

        total_available = result['total_available'] or 0

        if float(total_available) < float(amount):
            return jsonify({
                'status': 'error',
                'message': 'Insufficient available funds'
            }), 400

        cursor.execute("""
            INSERT INTO disbursements (project_name, total_amount, created_by)
            VALUES (%s, %s, %s)
        """, (project_name, amount, current_user['id']))

        disbursement_id = cursor.lastrowid

        cursor.execute("""
            SELECT donation_id, donor_id, remaining_amount
            FROM temp_donations
            WHERE donation_status = 'APPROVED'
            AND remaining_amount > 0
            ORDER BY donation_date ASC
        """)
        donations = cursor.fetchall()

        remaining_to_allocate = float(amount)

        # IMPORTANT: nonce MUST be incremented per tx
        account = web3.eth.account.from_key(PRIVATE_KEY)
        nonce = web3.eth.get_transaction_count(account.address, 'pending')

        for donation in donations:

            if remaining_to_allocate <= 0:
                break

            donation_id = donation['donation_id']
            donor_id = donation['donor_id']
            available = float(donation['remaining_amount'])

            allocated = min(available, remaining_to_allocate)

            cursor.execute("""
                INSERT INTO disbursement_allocations
                (disbursement_id, donation_id, donor_id, allocated_amount)
                VALUES (%s, %s, %s, %s)
            """, (disbursement_id, donation_id, donor_id, allocated))

            cursor.execute("""
                UPDATE temp_donations
                SET remaining_amount = remaining_amount - %s
                WHERE donation_id = %s
            """, (allocated, donation_id))

            remaining_to_allocate -= allocated

            timestamp = datetime.datetime.utcnow().strftime("%Y-%m-%dT%H:%M:%S")

            data_string = f"{disbursement_id}|{project_name}|{donation_id}|{donor_id}|{allocated}|{timestamp}"
            record_hash = hashlib.sha256(data_string.encode()).hexdigest()

            txid = f"DISB-{disbursement_id}-{donation_id}-{int(time.time()*1000)}"

            try:
                txn = contract.functions.addDisbursement(
                    txid,
                    record_hash
                ).build_transaction({
                    'from': account.address,
                    'nonce': nonce,
                    'gas': 300000,
                    'gasPrice': web3.eth.gas_price
                })

                signed_txn = web3.eth.account.sign_transaction(txn, PRIVATE_KEY)
                tx_hash = web3.eth.send_raw_transaction(signed_txn.raw_transaction)

                receipt = web3.eth.wait_for_transaction_receipt(tx_hash, timeout=120)

                if receipt.status != 1:
                    raise Exception("Blockchain tx failed")

                blockchain_tx = web3.to_hex(tx_hash)

                nonce += 1

            except Exception as e:
                conn.rollback()
                print("Blockchain error:", str(e))
                return jsonify({
                    "status": "error",
                    "message": "Blockchain disbursement failed"
                }), 500

            cursor.execute("""
                UPDATE disbursement_allocations
                SET record_hash = %s,
                    blockchain_tx = %s
                WHERE disbursement_id = %s AND donation_id = %s
            """, (record_hash, blockchain_tx, disbursement_id, donation_id))

        if remaining_to_allocate > 0:
            conn.rollback()
            return jsonify({
                'status': 'error',
                'message': 'Allocation failed'
            }), 500

        conn.commit()

        return jsonify({
            'status': 'success',
            'message': 'Disbursement created successfully',
            'disbursement_id': disbursement_id
        })

    except Exception as e:
        conn.rollback()
        return jsonify({
            'status': 'error',
            'message': str(e)
        }), 500

    finally:
        cursor.close()
        conn.close()
        
# @disbursement_bp.route('/display_disbursements', methods=['GET'])
# @verify_token
# def get_disbursements(current_user):

#     conn = db_conn()
#     cursor = conn.cursor(dictionary=True)

#     cursor.execute("""
#         SELECT 
#             d.disbursement_id,
#             d.project_name,
#             d.total_amount,
#             d.created_at,
#             a.name AS created_by_name
#         FROM disbursements d
#         LEFT JOIN admin a ON d.created_by = a.id
#         ORDER BY d.created_at DESC
#     """)

#     disbursements = cursor.fetchall()

#     cursor.close()
#     conn.close()

#     return jsonify(disbursements)

@disbursement_bp.route('/disbursement_allocations/<int:disbursement_id>', methods=['GET'])
@verify_token
def get_disbursement_allocations(current_user, disbursement_id):

    conn = db_conn()
    cursor = conn.cursor(dictionary=True)

    cursor.execute("""
        SELECT 
            da.allocation_id,
            da.allocated_amount,
            td.public_id,
            td.amount AS original_amount,
            td.donation_date,
            u.name AS donor_name
        FROM disbursement_allocations da
        JOIN temp_donations td ON da.donation_id = td.donation_id
        LEFT JOIN users u ON da.donor_id = u.id
        WHERE da.disbursement_id = %s
    """, (disbursement_id,))

    allocations = cursor.fetchall()

    cursor.close()
    conn.close()

    return jsonify(allocations)

@disbursement_bp.route('/disbursements', methods=['GET'])
@verify_token
def get_disbursements(current_user):

    conn = db_conn()
    cursor = conn.cursor(dictionary=True)

    cursor.execute("""
        SELECT 
            d.disbursement_id,
            d.project_name,
            d.total_amount,
            d.created_at,
            a.name AS created_by_name,

            IFNULL(SUM(da.allocated_amount), 0) AS used_amount

        FROM disbursements d
        LEFT JOIN disbursement_allocations da 
            ON d.disbursement_id = da.disbursement_id
        LEFT JOIN admin a 
            ON d.created_by = a.id

        GROUP BY d.disbursement_id
        ORDER BY d.created_at DESC
    """)

    rows = cursor.fetchall()

    # compute remaining
    for r in rows:
        r['remaining_amount'] = float(r['total_amount']) - float(r['used_amount'])

    cursor.close()
    conn.close()

    return jsonify(rows)
  
@disbursement_bp.route('/available_funds', methods=['GET'])
@verify_token
def available_funds(current_user):

    conn = db_conn()
    cursor = conn.cursor(dictionary=True)

    try:
        cursor.execute("""
            SELECT SUM(remaining_amount) AS available_funds
            FROM temp_donations
            WHERE donation_status = 'APPROVED'
            AND remaining_amount > 0
        """)

        result = cursor.fetchone()

        return jsonify({
            "available_funds": float(result['available_funds'] or 0)
        })

    except Exception as e:
        return jsonify({
            "status": "error",
            "message": str(e)
        }), 500

    finally:
        cursor.close()
        conn.close()
  

@disbursement_bp.route('/dtest_blockchain', methods=['GET'])
def dtest_blockchain():
    tx = write_disbursement_to_blockchain("test123", "abc123")
    return jsonify({"tx_hash": tx})