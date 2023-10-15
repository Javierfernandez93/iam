<?php

namespace DummieTrading;

use HCStudio\Orm;

class CatalogWithdrawMethod extends Orm {
	protected $tblName = 'catalog_withdraw_method';
	public function __construct() {
		parent::__construct();
	}

	public function getAll()
	{
		$sql = "SELECT 
					{$this->tblName}.{$this->tblName}_id,
					{$this->tblName}.method,
					{$this->tblName}.catalog_currency_id,
					{$this->tblName}.image
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status = '1'
				";
		
		return $this->connection()->rows($sql);
	}
}