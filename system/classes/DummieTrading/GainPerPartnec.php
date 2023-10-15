<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Util;

class CatalogBrand extends Orm {
	protected $tblName = 'catalog_brand';
	public function __construct() {
		parent::__construct();
	}

	public function getCount()
	{
		$sql = "SELECT 
					COUNT({$this->tblName}.{$this->tblName}_id) as c
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status = '1'
				";
		
		return $this->connection()->field($sql);
	}

	public function getAll()
	{
		$sql = "SELECT 
					{$this->tblName}.{$this->tblName}_id,
					{$this->tblName}.brand,
					{$this->tblName}.create_date
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status = '1'
				";
		
		return $this->connection()->rows($sql);
	}

	public function _getAll()
	{
		$sql = "SELECT 
					{$this->tblName}.brand
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status = '1'
				";
		
		return $this->connection()->column($sql);
	}

	public function getBrand($catalog_brand_id = null)
	{
		if(isset($catalog_brand_id) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.brand
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.catalog_brand_id = '{$catalog_brand_id}'
					AND 
						{$this->tblName}.status = '1'
					";
			
			return $this->connection()->field($sql);
		}

		return false;
	}

	public function getCatalogBrandId($brand = null)
	{
		if(isset($brand) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.brand LIKE '%{$brand}%'
					AND 
						{$this->tblName}.status = '1'
					";
			
			return $this->connection()->field($sql);
		}

		return false;
	}
}