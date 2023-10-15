<?php

namespace DummieTrading;

use HCStudio\Orm;

class CatalogPriority extends Orm {
  protected $tblName  = 'catalog_priority';
  public function __construct() {
    parent::__construct();
  }
  
  public function getAll() 
  {
    $sql = "SELECT
              {$this->tblName}.{$this->tblName}_id,
              {$this->tblName}.priority
            FROM 
              {$this->tblName}
            WHERE 
              {$this->tblName}.status = '1'
            ";
            
    return $this->connection()->rows($sql);
  }
}