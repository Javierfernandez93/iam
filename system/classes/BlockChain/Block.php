<?php

namespace BlockChain;

use HCStudio\Orm;

use BlockChain\Transaction;

class Block extends Orm {
	protected $tblName = 'block';

	const MINE_RATE = 3000;
	
	const GENESIS_DIFFICULTY = 0;
	const GENESIS_NONCE = 0;
	const GENESIS_HASH = '';

	const DIFFICULTY = 2;
	const TRANSACTIONS_PER_BLOCK = 5;
	const HASH_LENGTH = 64;

	/* states */
	const DELETED = -1;
	const AVIABLE = 1;
	const UNAVIABLE = 2;

	public function __construct(int $time_stamp = null,string $previous_hash = null,string $hash = null,$data = null,int $nonce = null,int $difficulty = null) 
	{
		parent::__construct('blockchain');

		$this->time_stamp = $time_stamp;
		$this->hash = $hash;
		$this->data = $data;

		$this->previous_hash = isset($previous_hash) ? $previous_hash : self::GENESIS_HASH;
		$this->difficulty = isset($difficulty) ? $difficulty : self::GENESIS_DIFFICULTY;
		$this->nonce = isset($nonce) ? $nonce : self::GENESIS_NONCE;
		$this->max_block = self::TRANSACTIONS_PER_BLOCK;

		$this->difficulty = $difficulty;
	}

	public static function adjustDifficulty(Block $PreviousBlock = null,int $time_stamp = null)
	{
		return $PreviousBlock->time_stamp + self::MINE_RATE > $time_stamp ? $PreviousBlock->difficulty + 1 : $PreviousBlock->difficulty - 1; 
	}

	public static function mine(Block $PreviousBlock = null,$data = null)
	{
		if(self::isAviableToMine($PreviousBlock) === true)
		{
			$nonce = 0;

			$PreviousBlock->difficulty = $PreviousBlock->difficulty ? $PreviousBlock->difficulty : self::DIFFICULTY;
			$previous_hash = $PreviousBlock->hash;
			
			do {
				$time_stamp = time();
				$nonce++;
				$difficulty = self::adjustDifficulty($PreviousBlock,$time_stamp);			
				$hash = self::hash($time_stamp,$PreviousBlock->hash,$data,$nonce,$difficulty);
			} while (substr($hash,0,$difficulty) != str_repeat(0,$difficulty));

			return new self($time_stamp,$previous_hash,$hash,$data,$nonce,$difficulty);
		}
	}

	public static function isAviableToMine(Block $PreviousBlock) : bool
	{
		if(!$PreviousBlock->getId() == 0)
		{	
			if($PreviousBlock->data)
			{
				$data = json_decode($PreviousBlock->data,true);
	
				return sizeof($data) == $PreviousBlock->max_block;
			}
		}

		return true;
	}
	
	public function getLastTransactionHash() : string
	{
		if($transaction_id = $this->getLastTransactionId())
		{
			return (new Transaction)->getHash($transaction_id);
		}

		return Transaction::hash(time(),'none');
	}

	public function getLastTransactionId() 
	{
		if($this->data)
		{
			if($data = json_decode($this->data,true))
			{
				return $data[sizeof($data)-1];
			}
		}

		return false;
	}
	
	public static function isAviableToAddTransaction(Block $PreviousBlock) : bool
	{
		if($PreviousBlock->data)
		{
			$data = json_decode($PreviousBlock->data,true);

			return sizeof($data) < $PreviousBlock->max_block;
		}
		
		return false;
	}

	public static function hash($time_stamp = null,$previous_hash = null,$data = null,$nonce = null,$difficulty = null)
	{
		return hash("sha256",serialize([$time_stamp,$previous_hash,$data,$nonce,$difficulty]));
	}

	public function getPreviousBlock(int $block_id = null,int $status = null)
	{
		if($this->hasBlocks() == true)
		{
			return $this->getLastBlock($block_id,$status);
		} else {
			$Block = self::genesisBlock();
			
			if($Block->save())
			{
				return $Block;
			}
		}
	}

	public static function genesisBlock()
	{
		return new self(time(),self::GENESIS_HASH,"genesis-hash",json_encode([]),self::GENESIS_NONCE,self::GENESIS_DIFFICULTY);
	}

	public function toString() {
		return [
			'time_stamp' => $this->time_stamp,
			'previous_hash' => $this->previous_hash,
			'hash' => $this->hash,
			'data' => $this->data,
			'nonce' => $this->nonce,
			'difficulty' => $this->difficulty,
		];
	}

	/* functions v2 */ 
	public static function addBlock(Block $Block = null) : bool
	{
		return $Block->save();
	}

	public static function addTransaction(Block $Block = null,Transaction $Transaction = null)
	{
		if(self::isAviableToAddTransaction($Block) === true)
		{
			$data = json_decode($Block->data,true);
			$data[] = $Transaction->getId();

			$Block->data = json_encode($data);

			if($Block->save())
			{
				return $Block;
			}
		}

		return false;
	}
	
	public static function saveBlockIntegrity(Block $Block) 
	{
		$transactions = json_decode($Block->data,true);

		if(sizeof($transactions) == self::TRANSACTIONS_PER_BLOCK)
		{
			$Block->status = self::UNAVIABLE;
			$Block->save();
		}
	}

	public function hasBlocks() : bool
	{
		$sql = "SELECT
					{$this->tblName}.{$this->tblName}_id 
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status IN ('".self::AVIABLE."','".self::UNAVIABLE."')
				";

		return $this->connection()->field($sql) ? true : false;
	}

	public function getLastBlock(int $block_id = null,int $status = null)
	{
		$mine = false;

		if($last_block_id = $this->_getLastBlock($block_id,$status))
		{
		} else if($last_block_id = $this->_getLastBlock($block_id,self::UNAVIABLE)) {
			$mine = true;			
		}

		$PreviousBlock = new Block;

		if($PreviousBlock->loadWhere("block_id = ?",$last_block_id))
		{
			if($mine === false)
			{
				return $PreviousBlock;
			} else {
				return self::mine($PreviousBlock,json_encode([]));
			}
		}
	}

	/* functions */ 
	public function _getLastBlock(int $block_id = null,int $status = null)
	{
		$filter = isset($status) ? " WHERE block.status = '{$status}'" : " WHERE block.status != '".self::DELETED."'";
		$filter .= isset($block_id) ? " AND block.block_id < {$block_id}" : "";

		$sql = "SELECT
					{$this->tblName}.{$this->tblName}_id
				FROM 
					{$this->tblName}
					{$filter}
				ORDER BY  
					{$this->tblName}.time_stamp 
				DESC
				";

		return $this->connection()->field($sql);
	}
	
	public function getLastBlocks()
	{
		$sql = "SELECT
					{$this->tblName}.{$this->tblName}_id,
					{$this->tblName}.hash,
					{$this->tblName}.previous_hash,
					{$this->tblName}.confirmation,
					{$this->tblName}.difficulty,
					{$this->tblName}.nonce,
					{$this->tblName}.max_block,
					{$this->tblName}.time_stamp
				FROM 
					{$this->tblName}
				WHERE
					{$this->tblName}.status != '".self::DELETED."'
				ORDER BY  
					{$this->tblName}.time_stamp 
				DESC
				";

		return $this->connection()->rows($sql);
	}

	public function isLastAviableForAddTransactions($block_id = null)
	{
		return $this->_isAviableForAddTransactions($block_id) < self::TRANSACTIONS_PER_BLOCK;
	}

	public function _isAviableForAddTransactions($block_id = null)
	{
		if(isset($block_id) === true)
		{
			$sql = "SELECT
						COUNT(transaction.transaction_id) as c
					WHERE 
						transaction
					WHERE 
						transaction.block_id = '{$block_id}'
					";

			return $this->connection()->field($sql);
		}
		
		return false;
	}
}