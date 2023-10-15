<?php

namespace BlockChain;

use HCStudio\Orm;

use JFStudio\Constants;

class Transaction extends Orm 
{
	protected $tblName = 'transaction';

	public static $REWARD = 1;

	const INPUT = 1;
	const OUTPUT = 2;

	const HASH_LENGTH = 64;
	
	/* withdraw fees */
	const DEFAULT_FEE = 0;
	const TRANSACTION_FEE = 2;
	const WITHDRAW_FEE = 0;

	const AVIABLE_FIELDS_DATA = ['@optMessage','@sysFee','@sysFund'];

	public function __construct() 
	{
		parent::__construct('blockchain');
	}
	
	public static function create(Wallet $SenderWallet = null,string $recipient_address = null,float $amount = null,string $previous_hash = null,string $data = '',float $fee = null) 
	{
		if(isset($SenderWallet,$recipient_address,$amount) === true)
		{
			if($SenderWallet->existPublicKey($recipient_address))
			{
				$Transaction = new Transaction;

				$unix_date = time();

				$Transaction->previous_hash = $previous_hash;
				$Transaction->hash = self::hash($unix_date,$previous_hash,$data);
				$Transaction->output = json_encode([
					0 => [
						'amount' => $SenderWallet->getBalance() - $amount,
						'address' => $SenderWallet->public_key,
					], 
					1 => [
						'amount' => $amount,
						'address' => $recipient_address,
					], 
				]);

				$Transaction->fee = $fee ? $fee : self::DEFAULT_FEE;
				$Transaction->data = $data;
				$Transaction->unix_date = $unix_date;
				$Transaction->input = json_encode(self::sign($Transaction,$SenderWallet));

				if($Transaction->save())
				{
					return $Transaction;
				}
			} else {
				// die("TNE NOT public key");
			}
		} else {
			// die("TNE NOT DATA");
		}
	}

	public static function prepareData(array $data = null)
	{
		$result = array_filter($data,function($field){
			return in_array($field,self::AVIABLE_FIELDS_DATA);
		}, ARRAY_FILTER_USE_KEY);

		return sizeof($result) > 0 ? json_encode($result) : '';
	}

	public static function hash(int $time_stamp = null,string $previous_hash = null,string $data = null)
	{
		return hash("sha256",serialize([$time_stamp,$previous_hash,$data]));
	}

	public function _saveTransaction($block_id = null) 
	{
		$this->input = json_encode($this->input);
		$this->outputs = json_encode($this->outputs);
		$this->block_id = $block_id;

		if($this->save())
		{
			$this->input = json_decode($this->input,true);
			$this->outputs = json_decode($this->outputs,true);

			return $this;
		}

		return false;
	}

	public function saveTransaction($block_id = null) 
	{			
		if($this->control_id)
		{
			$transaction_id = $this->existId($this->control_id);

			if($transaction_id)
			{
				$this->loadWhere("transaction_id = ?",$transaction_id);
			}

			return $this->_saveTransaction($block_id);
		}

		return false;
	}

	public function existId($control_id = null) 
	{
		$sql = "SELECT
					{$this->tblName}.{$this->tblName}_id
				FROM 
					{$this->tblName}
				WHERE
					{$this->tblName}.control_id = '{$control_id}'
				AND 
					{$this->tblName}.status = '1'
				";

		return $this->connection()->field($sql);
	}

	public function toString() {
		return [
			// 'balance' => $this->getBalance(),
			// 'key_pair' => $this->getKeypair(),
			// 'public_key' => $this->getPublicKey()
		];
	}

	public static function reward(Wallet $MineWallet = null,$BlockChainWallet = null)
	{
		return self::create($BlockChainWallet,$MineWallet->getPublicKey(),self::$REWARD);
	}

	public static function sign($Transaction = null,$SenderWallet = null)
	{
		return [
			'time_stamp' => time(),
			'amount' => $SenderWallet->getBalance(),
			'address' => $SenderWallet->public_key,
			'signature' => $SenderWallet->sign($Transaction->outputs),
		];
	}

	public static function unCompressTransaction(array $transaction = null,bool $encoding = false)
	{
		if($transaction['input'])
		{
			$transaction['input'] = json_decode($transaction['input'],$encoding);
		}

		if($transaction['output'])
		{
			$transaction['output'] = json_decode($transaction['output'],$encoding);
		}

		return $transaction;
	}

	public static function unCompressTransactions(array $transactions = null)
	{
		return isset($transactions) ? array_map(function($transaction) {
			return self::unCompressTransaction($transaction);
		},$transactions) : [];
	}
	
	public function update($SenderWallet = null,$recipient_address = null,$ammount = null)
	{
		$public_key = $SenderWallet->getPublicKey();

		$sender_output = array_filter($this->outputs,function($output) use ($public_key) { 
			return $output['address'] == $public_key;
		})[0];

		$sender_output['ammount'] -= $ammount;
		
		$this->outputs[] = [
			'ammount' => $ammount,
			'address' => $recipient_address
		];

		$this->input = $this->sign($this, $SenderWallet);

		return $this;
	}

	public function getHash(int $transaction_id = null) 
	{
		if(isset($transaction_id) === true)
		{
			$sql = "SELECT
						{$this->tblName}.hash
					FROM 
						{$this->tblName}
					WHERE
						{$this->tblName}.transaction_id = '{$transaction_id}'
					AND 
						{$this->tblName}.status = '1'
					";

			return $this->connection()->field($sql);
		}

		return false;
	}
	
	public function getLastTransactions(string $limit = '') 
	{
		$sql = "SELECT
					{$this->tblName}.{$this->tblName}_id,
					{$this->tblName}.hash,
					{$this->tblName}.input,
					{$this->tblName}.output,
					{$this->tblName}.previous_hash,
					{$this->tblName}.data,
					{$this->tblName}.unix_date
				FROM 
					{$this->tblName}
				WHERE
					{$this->tblName}.status = '".Constants::AVIABLE."'
					{$limit}
				";

		return $this->connection()->rows($sql);
	}
	
	public function getLastTransactionsWallet(int $wallet_id = null,string $limit = '') 
	{
		if(isset($wallet_id) === true)
		{
			$sql = "SELECT
						{$this->tblName}.{$this->tblName}_id,
						{$this->tblName}.hash,
						{$this->tblName}.input,
						{$this->tblName}.output,
						{$this->tblName}.previous_hash,
						{$this->tblName}.data,
						{$this->tblName}.unix_date
					FROM 
						transaction_per_wallet
					LEFT JOIN 
						{$this->tblName}
					ON 
						{$this->tblName}.{$this->tblName}_id = transaction_per_wallet.transaction_id
					WHERE
						(
							transaction_per_wallet.to_wallet_id = '{$wallet_id}'
							OR 
							transaction_per_wallet.wallet_id = '{$wallet_id}'
						)
					AND 
						{$this->tblName}.status = '".Constants::AVIABLE."'
					ORDER BY 
						transaction_per_wallet.transaction_per_wallet_id 
					DESC 
						{$limit}
					";

			return $this->connection()->rows($sql);
		}

		return false;
	}
	
	public static function getWeight(array $data = null) : float
	{
		return strlen(json_encode($data));
	}

	public static function getTotalOutput(array $outputs = null) : float
	{
		$total = 0;

		if(is_array($outputs['output']))
		{
			foreach($outputs['output'] as $output)
			{
				$total += $output->amount;
			}
		}

		return $total;
	}

	public static function getTotalInput(array $inputs = null) : float
	{	
		$total = 0;

		if(is_array($inputs['input']))
		{
			foreach($inputs as $input)
			{
				$total += $input->amount;
			}
		} else {
			$total += $inputs['input']->amount;
		}

		return $total;
	}

	public static function uncompressData(string $data = null) 
	{
		return isset($data) ? json_decode($data) : '';
	}
	
	public static function getSize(array $data = null) : float
	{
		return mb_strlen(json_encode($data), '8bit');
	}
	
	public function getTransactionInfo(string $hash = null) 
	{
		if($transaction = $this->getTransaction($hash))
		{
			$transaction = self::unCompressTransaction($transaction);
			$transaction['size'] = self::getSize($transaction);
			$transaction['weight'] = self::getWeight($transaction);
			$transaction['totalInput'] = self::getTotalInput($transaction);
			$transaction['totalOutput'] = self::getTotalOutput($transaction);
			$transaction['data'] = self::uncompressData($transaction['data']);
			$transaction['fees'] = 0;

			return $transaction;
		}
	}

	public function getTransaction(string $hash = null) 
	{
		if(isset($hash) === true)
		{
			$sql = "SELECT
						{$this->tblName}.{$this->tblName}_id,
						{$this->tblName}.hash,
						{$this->tblName}.input,
						{$this->tblName}.output,
						{$this->tblName}.previous_hash,
						{$this->tblName}.data,
						{$this->tblName}.unix_date
					FROM 
						{$this->tblName}
					WHERE
						{$this->tblName}.hash = '{$hash}'
					AND 
						{$this->tblName}.status = '".Constants::AVIABLE."'
					";

			return $this->connection()->row($sql);
		}

		return false;
	}
	
	public function getLastAddress(int $wallet_id = null,string $limit = '') 
	{
		if(isset($wallet_id) === true)
		{
			$sql = "SELECT
						(SELECT wallet.public_key FROM wallet WHERE wallet.wallet_id = transaction_per_wallet.to_wallet_id) as to_public_key,
						(SELECT wallet.public_key FROM wallet WHERE wallet.wallet_id = transaction_per_wallet.wallet_id) as public_key
					FROM 
						transaction_per_wallet
					WHERE 
						(
							transaction_per_wallet.to_wallet_id = '{$wallet_id}'
							OR 
							transaction_per_wallet.wallet_id = '{$wallet_id}'
						)
					AND 
						transaction_per_wallet.status = '".Constants::AVIABLE."'
					ORDER BY 
						transaction_per_wallet.transaction_per_wallet_id 
					DESC 
						{$limit}
					";

			return $this->connection()->rows($sql);
		}

		return false;
	}
}