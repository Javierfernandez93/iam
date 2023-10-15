<?php

namespace DummieTrading;

use HCStudio\Orm;

class CatalogMultimedia extends Orm {
	protected $tblName = 'catalog_multimedia';
	public static $TEXT = 1;
	public static $VIDEO = 2;
	public static $AUDIO = 3;
	public function __construct() {
		parent::__construct();
	}

	public function getMultimedia($catalog_multimedia_id = null)
	{	
		if (isset($catalog_multimedia_id) === true) 
		{
			$sql = "SELECT
						{$this->tblName}.multimedia
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.{$this->tblName}_id = '{$catalog_multimedia_id}'
					";
			
			return $this->connection()->field($sql);
		}

		return false;
	}

	public function getAll()
	{
		$sql = "SELECT 
					{$this->tblName}.{$this->tblName}_id,
					{$this->tblName}.multimedia
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status = '1'
				";
		
		return $this->connection()->rows($sql);
	}
}