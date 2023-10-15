<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Util;

class CatalogRequirementFile extends Orm {
	protected $tblName = 'catalog_requirement_file';
	public static $NO_WRITE_DATA = 0;
	public static $SIGNING_DATA = -1;
	public static $WRITE_DATA = 1;
	public function __construct() {
		parent::__construct();
	}

	public function getAll($write_data = 0)
	{
		$sql = "SELECT 
					{$this->tblName}.{$this->tblName}_id,
					{$this->tblName}.required,
					{$this->tblName}.requirement_file
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status = '1'
				AND 
					{$this->tblName}.write_data = '{$write_data}'
				";
		
		return $this->connection()->rows($sql);
	}
}