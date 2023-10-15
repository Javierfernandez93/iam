<?php

namespace DummieTrading;

use HCStudio\Orm;
use JFStudio\Constants;

class CatalogCurrency extends Orm {
	protected $tblName = 'catalog_currency';

	const USD = 8;
	public function __construct() {
		parent::__construct();
	}

	public function getAll()
	{
		$sql = "SELECT 
					{$this->tblName}.{$this->tblName}_id,
					{$this->tblName}.currency,
					{$this->tblName}.description,
					{$this->tblName}.code
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status = '1'
				";
		
		return $this->connection()->rows($sql);
	}
	
	public function getCurrency(int $catalog_currency_id = null)
	{
		$sql = "SELECT 
					{$this->tblName}.currency
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.catalog_currency_id = '{$catalog_currency_id}'
				AND  
					{$this->tblName}.status = '1'
				";
		
		return $this->connection()->field($sql);
	}
	
	public function getFullCurrency(int $catalog_currency_id = null)
	{
		$sql = "SELECT 
					{$this->tblName}.description,
					{$this->tblName}.image,
					{$this->tblName}.currency
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.catalog_currency_id = '{$catalog_currency_id}'
				AND  
					{$this->tblName}.status = '1'
				";
		
		return $this->connection()->row($sql);
	}

	public function getIn(string $catalog_currency_ids = null)
	{
		if(isset($catalog_currency_ids) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id,
						{$this->tblName}.currency,
						{$this->tblName}.description
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.catalog_currency_id IN ({$catalog_currency_ids})
					AND  
						{$this->tblName}.status = '".Constants::AVIABLE."'
					";
			
			return $this->connection()->rows($sql);
		}

		return false;
	}
}