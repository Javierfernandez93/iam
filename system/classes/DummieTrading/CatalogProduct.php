<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Util;

class CatalogProduct extends Orm {
	protected $tblName = 'catalog_product';
	public function __construct() {
		parent::__construct();
	}

	public function getAll()
	{
		$sql = "SELECT 
					{$this->tblName}.{$this->tblName}_id,
					{$this->tblName}.description,
					{$this->tblName}.catalog_product,
					{$this->tblName}.create_date
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status = '1'
				";
		
		return $this->connection()->rows($sql);
	}

	public function getCatalogProduct($catalog_product_id = null)
	{
		if(isset($catalog_product_id) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.catalog_product
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.catalog_product_id = '{$catalog_product_id}'
					AND 
						{$this->tblName}.status = '1'
					";
			
			return $this->connection()->field($sql);
		}

		return false;
	}
}