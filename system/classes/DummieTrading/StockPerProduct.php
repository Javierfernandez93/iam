<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Util;

class StockPerProduct extends Orm {
	protected $tblName = 'stock_per_product';
	public static $VIEW_LIMIT = 5;
	public function __construct() {
		parent::__construct();
	}
	public function getStockPerProduct($product_id = null)
	{
		if(isset($product_id) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.ammount
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.product_id = '{$product_id}'
						";
			
			return $this->connection()->field($sql) ?? 0;
		}

		return false;
	}
}