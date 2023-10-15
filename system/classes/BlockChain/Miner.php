<?php

namespace BlockChain;

use Exception;

use BlockChain\Transaction;
use BlockChain\BlockChain;
use BlockChain\Wallet;

class Miner {
	private $BlockChain = null;
	private $Wallet = null;
	public function __construct(BlockChain $BlockChain = null,Wallet $Wallet = null) 
	{
		$this->BlockChain = $BlockChain;
		$this->Wallet = $Wallet;
	}

	public function mine()
	{
		if(sizeof($this->BlockChain->MemoryPool->getTransactions()) == 0)
		{
			echo "TNE not unconfirmed transactions";
			die();
		}

		$BlockChainWallet = new Wallet($this->BlockChain);

		$this->BlockChain->MemoryPool->getTransactions()[] = Transaction::reward($this->Wallet,$BlockChainWallet);

		$Block = $this->BlockChain->addBlock($this->BlockChain->MemoryPool->getTransactions());

		$this->BlockChain->MemoryPool->wipe();

		return $Block;
	}
}