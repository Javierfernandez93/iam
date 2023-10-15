<?php

namespace BlockChain;

use BlockChain\Transaction;
use BlockChain\TransactionPerWallet;

class BlockChain {
	public $MemoryPool = null;
	public $Blocks = [];

	public function __construct() 
	{
		$this->Blocks[] = (new Block)->getPreviousBlock();
		$this->MemoryPool = new MemoryPool;
	}

	public function saveBlocks()
	{
		foreach($this->Blocks as $key => $Block)
		{
			if($Block->previous_hash == false)
				$Block->previous_hash = 0;

			if($Block->nonce == false)
				$Block->nonce = 0;

			if($Block->difficulty == false)
				$Block->difficulty = 0;

			if($Block->save())
			{
				$this->Blocks[$key] = $Block;
			}
		}

		foreach($this->Blocks as $Block)
		{
			if(is_array($Block->data) == true)
			{
				$this->saveTransactions($Block);
			}
		}
	}

	public function saveTransactions($Block = null)
	{
		foreach($Block->data as $Transaction)
		{
			$Transaction->saveTransaction($Block->getId());
		}
	}

	public function getLasBlock()
	{
		return $this->Blocks[sizeof($this->Blocks)-1];
	}

	public function addBlock($data = null)
	{
		$PreviousBlock = $this->getLasBlock();

		$Block = Block::mine($PreviousBlock,$data);

		$this->Blocks[] = $Block;

		return $Block;
	}

	public function validate($Blocks = []) : bool
	{
		$validate = true;

		foreach($Blocks as $key => $Block)
		{
			if($key > 0)
			{
				$LastBlock = $Blocks[$key-1];

				if($LastBlock->getHash() != $Block->getPreviousHash())
				{
					$validate = false;
				}
			}
		}

		return $validate;
	}
	public function replace($Blocks = [])
	{
		if(sizeof($Block) == sizeof($this->Blocks))
		{
			if($this->validate($blocks) === true)
			{
				return true;
			} else {
				echo "TNE bloques mal";
			}
		} else {
			echo "TNE NO iguales";
		}
	}

	public static function unCompressTransactions(array $transactions = null)
	{
		return Transaction::unCompressTransactions($transactions);
	}

	public function getLastBlocks()
	{
		return (new Block)->getLastBlocks();
	}
	
	public function getLastTransactions(string $limit = '')
	{
		return (new Transaction)->getLastTransactions($limit);
	}
	
	public function getLastTransactionsWallet(int $wallet_id = null,string $limit = '')
	{
		return (new Transaction)->getLastTransactionsWallet($wallet_id,$limit);
	}
	
	public function getLastAddress(int $wallet_id = null,string $limit = '')
	{
		return (new Transaction)->getLastAddress($wallet_id,$limit);
	}
	
	public static function getCountTransactions(int $wallet_id = null)
	{
		return (new TransactionPerWallet)->getCountTransactions($wallet_id);
	}
}