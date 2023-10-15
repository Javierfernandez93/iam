<?php

namespace DummieTrading;

use HCStudio\Orm;

class Broker extends Orm {
	const DELETE = -1;
	const INACTIVE = 0;
	const ACTIVE = 1;

	protected $tblName = 'broker';
	public function __construct() {
		parent::__construct();
	}

	public function getAll()
	{
		$sql = "SELECT 
					{$this->tblName}.{$this->tblName}_id,
					{$this->tblName}.name,
					{$this->tblName}.fee,
					{$this->tblName}.status
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status IN ('".self::ACTIVE."','".self::INACTIVE."')
				";
		
		return $this->connection()->rows($sql);
	}
	
	public function getActive()
	{
		$sql = "SELECT 
					{$this->tblName}.{$this->tblName}_id,
					{$this->tblName}.name,
					{$this->tblName}.color
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status IN ('".self::ACTIVE."')
				";
		
		return $this->connection()->rows($sql);
	}
}