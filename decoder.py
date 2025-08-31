from web3 import Web3

# Connect to a local Ethereum node (update the provider as needed)
web3 = Web3(Web3.HTTPProvider('http://127.0.0.1:8545'))

decode = bytes.fromhex("4b696d20526f6f6c6e64").decode('utf-8')
print(decode)

# Define the account address (replace with a valid address)
account_address = '0x0000000000000000000000000000000000000000'  # Replace with your Ethereum address
print("Balance:", web3.eth.get_balance(account_address))