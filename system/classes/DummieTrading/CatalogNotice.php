<?php

namespace DummieTrading;

use HCStudio\Orm;

class CatalogNotice extends Orm {
  protected $tblName  = 'catalog_notice';
  const POP_UP = 1;
  const NEW = 2;
  public function __construct() {
    parent::__construct();
  }
  
  public function getAll() 
  {
    $sql = "SELECT
              {$this->tblName}.{$this->tblName}_id,
              {$this->tblName}.notice
            FROM 
              {$this->tblName}
            WHERE 
              {$this->tblName}.status = '1'
            ";
            
    return $this->connection()->rows($sql);
  }
}