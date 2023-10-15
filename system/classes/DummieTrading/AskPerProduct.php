<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Util;

class AskPerProduct extends Orm {
	protected $tblName = 'ask_per_product';
	public function __construct() {
		parent::__construct();
	}

	public function getAll($product_id = null)
	{
		if (isset($product_id) === true) 
		{
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id,
						{$this->tblName}.ask,
						{$this->tblName}.create_date
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.product_id = '{$product_id}'
					AND  
						{$this->tblName}.status = '1'
					AND  
						{$this->tblName}.reply_ask_per_product_id = '0'
					ORDER BY 
						{$this->tblName}.create_date
					DESC 
					";
			
			return $this->connection()->rows($sql);
		}

		return false;
	}

	public function getReplys($reply_ask_per_product_id = null)
	{
		if (isset($reply_ask_per_product_id) === true) 
		{
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id,
						{$this->tblName}.ask,
						{$this->tblName}.create_date
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.reply_ask_per_product_id = '{$reply_ask_per_product_id}'
					AND  
						{$this->tblName}.status = '1'
					ORDER BY 
						{$this->tblName}.create_date
					DESC 
					";

			
			return $this->connection()->rows($sql);
		}

		return false;
	}
}