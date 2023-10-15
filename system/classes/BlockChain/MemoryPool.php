<?php

namespace BlockChain;

class MemoryPool {
	public $Transactions = [];

	public function __construct() 
	{

	}
	
	public function getTransactions()
	{
		return $this->Transactions;
	} 

	public function find($address = null) 
	{
		$_Transaction = false;

		if(empty($this->Transactions) == false)
		{	
			foreach($this->Transactions as $Transaction)
			{
				if($Transaction->input['address'] == $address)
				{
					$_Transaction = $Transaction;
				}
			}
		}

		return $_Transaction;
	}

	public function wipe() 
	{
		$this->Transactions = [];
	}

	public function addOrUpdate(Transaction $_Transaction = null) 
	{
		$index = false;

		if(isset($this->Transactions) === true && empty($this->Transactions) === false)
		{
			foreach($this->Transactions as $key => $Transaction)
			{
				if($_Transaction->control_id == $Transaction->control_id)
				{
					$index = $key;
				}
			}
		}

		if($index === false)
		{
			$this->Transactions[] = $_Transaction;
		} else {
			$this->Transactions[$index] = $_Transaction;
		}
	}
}