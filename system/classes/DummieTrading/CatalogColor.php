<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Util;

class CatalogColor extends Orm {
	protected $tblName = 'catalog_color';
	public function __construct() {
		parent::__construct();
	}

	public function getColors($colors_serialized = null)
	{
		if(isset($colors_serialized) === true)
		{
			$catalog_color_ids = unserialize($colors_serialized);

			return $this->getIn(implode(",", $catalog_color_ids));
		}
	}

	public function getIn($catalog_color_ids = null)
	{
		if(isset($catalog_color_ids) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.color,
						{$this->tblName}.hex
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.catalog_color_id IN ({$catalog_color_ids})
					AND 
						{$this->tblName}.status = '1'
					";
			
			return $this->connection()->rows($sql);
		}

		return false;
	}

	public function getAll()
	{
		$sql = "SELECT 
					{$this->tblName}.{$this->tblName}_id,
					{$this->tblName}.color,
					{$this->tblName}.hex
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status = '1'
				ORDER BY 
					{$this->tblName}.color 
				ASC
				";
		
		return $this->connection()->rows($sql);
	}
}