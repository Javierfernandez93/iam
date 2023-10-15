<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Util;

class ViewPerProduct extends Orm {
	protected $tblName = 'view_per_product';
	public function __construct() {
		parent::__construct();
	}
	public function getViewPerProduct($product_id = null)
	{
		if(isset($product_id) === true)
		{
			$sql = "SELECT 
						COUNT({$this->tblName}.{$this->tblName}_id) as c
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