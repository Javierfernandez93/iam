<?php

namespace DummieTrading;

use HCStudio\Orm;
use JFStudio\Constants;

class CatalogMessageWhatsAppType extends Orm {
	protected $tblName = 'catalog_message_whatsapp_type';
	public function __construct() {
		parent::__construct();
	}

	public function getAll()
	{
		$sql = "SELECT 
					{$this->tblName}.{$this->tblName}_id,
					{$this->tblName}.name
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status = '".Constants::AVIABLE."'
				";
		
		return $this->connection()->rows($sql);
	}
}