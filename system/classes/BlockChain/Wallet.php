<?php

namespace BlockChain;

use HCStudio\Orm;
use HCStudio\Util;
use HCStudio\Token;

use BlockChain\Transaction;

use DummieTrading\ShortUrl;

use JFStudio\Constants;

class Wallet extends Orm
{
	protected $tblName = 'wallet';

	public $BlockChain = null;
	public $balance = null;

	const INITIAL_BALANCE = 0;
	const ADDRESS_LENGTH = 66;
	const TXN_PAYMENT_ID = 32;
	const EWALLET_FORMAT = 'secp256k1';

	/* LINKING */
	const MAIN_URL = 'iam.com.mx/';
    const PROTOCOL = 'https://';
    const EWALLET_URL = 'apps/ewallet/send?address=';
    const EWALLET_CHECKOUT_URL = 'apps/ewallet/checkout';
    
    const MAIN_EWALLET = 1;

	public function __construct() 
	{
		parent::__construct('blockchain');
	}

	public static function getTxnIdPayment() 
	{
		return strtoupper(Token::__randomKey(self::TXN_PAYMENT_ID));
	}
	/* typical func */

	public function existWallet(int $user_login_id = null) 
	{
		if(isset($user_login_id) === true)
		{
			$sql = "SELECT
						{$this->tblName}.{$this->tblName}_id
					FROM 
						{$this->tblName}
					WHERE
						{$this->tblName}.user_login_id = '{$user_login_id}'
					AND 
						{$this->tblName}.status = '".Constants::AVIABLE."'
					";

			return $this->connection()->field($sql);
		}

		return false;
	}

	public function getMainPublicKey() 
	{
		return $this->getPublicKey(self::MAIN_EWALLET);
	}
	
	public function getPublicKey(int $wallet_id = null) 
	{
		if(isset($wallet_id) === true)
		{
			$sql = "SELECT
						{$this->tblName}.public_key
					FROM 
						{$this->tblName}
					WHERE
						{$this->tblName}.wallet_id = '{$wallet_id}'
					AND 
						{$this->tblName}.status = '".Constants::AVIABLE."'
					";

			return $this->connection()->field($sql);
		}

		return false;
	}

	public function existPublicKey(string $public_key = null) 
	{
		if(isset($public_key) === true)
		{
			$sql = "SELECT
						{$this->tblName}.{$this->tblName}_id
					FROM 
						{$this->tblName}
					WHERE
						{$this->tblName}.public_key = '{$public_key}'
					AND 
						{$this->tblName}.status = '1'
					";

			return $this->connection()->field($sql);
		}

		return false;
	}
	

	public function getUserIdByPublicKey(string $public_key = null) 
	{
		if(isset($public_key) === true)
		{
			$sql = "SELECT
						{$this->tblName}.user_login_id
					FROM 
						{$this->tblName}
					WHERE
						{$this->tblName}.public_key = '{$public_key}'
					AND 
						{$this->tblName}.status = '1'
					";

			return $this->connection()->field($sql);
		}

		return false;
	}
	
	public function getWalletIdByPublicKey(string $public_key = null) 
	{
		if(isset($public_key) === true)
		{
			return $this->existPublicKey($public_key);
		}

		return false;
	}

	public static function getWallet(int $user_login_id = null) 
	{
		if(isset($user_login_id) === true)
		{
			$Wallet = new Wallet;

			$wallet_id = $Wallet->existWallet($user_login_id);

			if($wallet_id == false)
			{
				return self::createWallet($user_login_id);
			} else {
				if($Wallet->loadWhere("wallet_id = ?",$wallet_id))
				{
					return $Wallet;
				}
			}
		}

		return false;
	}
	
	public static function getWalletByPublicKey(string $public_key = null) 
	{
		if(isset($public_key) === true)
		{
			$Wallet = new Wallet;

			$wallet_id = $Wallet->existPublicKey($public_key);
			
			if($Wallet->loadWhere("wallet_id = ?",$wallet_id))
			{
				return $Wallet;
			}
		}

		return false;
	}

	public static function getKeyPair() 
	{
		require_once TO_ROOT . "/system/vendor2/autoload.php";

		$EC = new \Elliptic\EC(self::EWALLET_FORMAT);
		
		if($key = $EC->genKeyPair())
		{
			return [
				'public_key' => $key->getPublic(true,'hex'),
				'key_pair' => $key->getPrivate('hex')
			];
		}
	}

	public static function createWallet(int $user_login_id = null) 
	{
		if($key = self::getKeyPair())
		{
			$Wallet = new Wallet;
			$Wallet->key_pair = $key['key_pair'];
			$Wallet->public_key = $key['public_key'];
			$Wallet->user_login_id = $user_login_id;
			$Wallet->create_date = time();
			$Wallet->format = self::EWALLET_FORMAT;
			
			if($Wallet->save())
			{
				return self::constructWalletShortLink($Wallet);
			}
		}
	}

	public static function constructWalletShortLink(Wallet $Wallet = null)
	{
		$Wallet->short_url_id = $Wallet->_constructWalletShortLink();

		if($Wallet->save())
		{
			return $Wallet;
		}
	}

	public function init(BlockChain $BlockChain,float $initial_balance = NULL) 
	{
		$initial_balance = isset($initial_balance) ? $initial_balance : self::INITIAL_BALANCE;
		
		$this->setBalance($initial_balance);
		// $this->setPublicKey(Token::__randomKey(self::$ADDRESS_LENGTH));

		$this->BlockChain = $BlockChain;
	}
	
	public function saveTransaction(Block $Block = null,Transaction $Transaction = null,string $recipient_adress = null) 
	{
		return TransactionPerWallet::create(time(),$this->getId(),$this->getWalletIdByPublicKey($recipient_adress),$Block->getId(),$Transaction->getId());
	}

	public function createTransaction(string $recipient_adress = null,float $amount = null,string $data = '',bool $get_txid = false,float $fee = 0) 
	{
		if($this->getBalance() >= $amount)
		{
			$feeAmout = 0;

			if($fee != Transaction::DEFAULT_FEE)
			{
				$feeAmout = Util::getPercentaje($amount,$fee);
				$amount += $feeAmout;

				$dataTemp = json_encode([
					'@sysFee' => [
						'feeAmout' => $feeAmout,
						'amount' => $amount,
						'recipient_adress' => $recipient_adress,
					]
				]);

				$this->_createTransaction($this->getMainPublicKey(),$feeAmout,$dataTemp);
			}

			$amount -= $feeAmout;
			
			return $this->_createTransaction($recipient_adress,$amount,$data,$get_txid,$feeAmout);
		} 
	}

	public function _createTransaction(string $recipient_adress = null,float $amount = null,string $data = '',bool $get_txid = false,float $feeAmout = 0) 
	{
		$Block = new Block;
		
		// search the public key if are they in the last block then'll update, instead we'll create the transaction 
		if($Block = $Block->getPreviousBlock(null,Block::AVIABLE))
		{
			$Transaction = Transaction::create($this,$recipient_adress,$amount,$Block->getLastTransactionHash(),$data,$feeAmout);

			$Block = Block::addTransaction($Block,$Transaction);

			if($transaction_per_wallet_id = $this->saveTransaction($Block,$Transaction,$recipient_adress))
			{
				Block::saveBlockIntegrity($Block);

				return $get_txid ? $transaction_per_wallet_id : true;
			}
		}
	}

	public function sign(array $data = null) 
	{
		return hash("sha256",serialize($data));
	}

	public function getBalance() : float
	{
		$balance = 0;

		if($data = $this->getTransactionsBalance($this->getId()))
		{
			$balance = $this->_getBalance($data);
		}

		return $balance + self::INITIAL_BALANCE;
	}
	
	public function getBalanceInfo() 
	{
		if($data = $this->getTransactionsBalance($this->getId()))
		{
			$data = [
				'amount' => $this->_getBalance($data) + self::INITIAL_BALANCE,
				'totalSent' => isset($data['outputs']) ? array_sum($data['outputs']) : 0,
				'totalReceived' => isset($data['inputs']) ? array_sum($data['inputs']) + self::INITIAL_BALANCE : 0 + self::INITIAL_BALANCE
			];

			return $data;
		}

		return false;
	}

	public function getTransactionsBalance(int $wallet_id = null)
	{
		if($transactions = (new TransactionPerWallet)->geTransactions($wallet_id))
		{
			$transactions = Transaction::unCompressTransactions($transactions);

			foreach($transactions as $transaction) 
			{
				$mode = $transaction['input']->address == $this->public_key ? Transaction::OUTPUT : Transaction::INPUT;

				foreach($transaction['output'] as $output)
				{
					if($mode == Transaction::OUTPUT)
					{
						if($output->address != $this->public_key)
						{
							$data['outputs'][] = $output->amount;
						}
					} else if($mode == Transaction::INPUT) {
						if($output->address == $this->public_key)
						{
							$data['inputs'][] = $output->amount;
						}
					}
				}
			}

			return $data;
		}

		return false;
	}

	public function _constructWalletShortLink() 
	{
		if($url = $this->constructFullEwalletLink())
		{
			$ShortUrl = new ShortUrl;
			
			if($short_url_id = $ShortUrl->getShortUrlEWallet($this->user_login_id,$url,"Recibir fondos a Ewallet"))
			{
				return $short_url_id;
			}
		}

		return false;
	}

	public function _getBalance(array $data = null) : float
	{
		$inputs = isset($data['inputs']) ? array_sum($data['inputs']) : 0 ;
		$outputs = isset($data['outputs']) ? array_sum($data['outputs']) : 0 ;

		return $inputs - $outputs;
	}	
	
	public function toString() {
		return [
			'balance' => $this->getBalance(),
			// 'key_pair' => $this->getKeypair(),
			'public_key' => $this->getPublicKey()
		];
	}

	public function setBalance($balance = null)
	{
		$this->balance = $balance;
	}

	public function constructFullEwalletLink() 
	{
		return $this->_constructFullEwalletLink($this->public_key);
	}

	public function _constructFullEwalletLink(string $public_key = null,bool $include_protocol = true) 
    {
        return $this->constructEwalletLink($include_protocol).$public_key;
    }

	public function constructEwalletLink(bool $include_protocol = null) 
    {
        $PROTOCOL = $include_protocol === true ? self::PROTOCOL : "";

        return $PROTOCOL.self::MAIN_URL.self::EWALLET_URL;
    }
	
	public static function constructEwalletPaymentLink(float $amount = null,int $main_buy_per_user_id = null,string $currency = 'USD') 
    {
        return self::PROTOCOL.self::MAIN_URL.self::EWALLET_CHECKOUT_URL."?".http_build_query([
			'amount' => $amount,
			'currency' => $currency,
			'mbpuid' => $main_buy_per_user_id,
		]);
    }

	public static function getCurrentBalance(int $user_login_id = null) : float
	{
		if($Wallet = Wallet::getWallet($user_login_id))
		{
			return $Wallet->getBalance();
		}

		return 0;
	}
}