<?php

namespace DummieTrading;

use HCStudio\Orm;

class CatalogZuumTool extends Orm {
  protected $tblName  = 'catalog_zuum_tool';
  public function __construct() {
    parent::__construct();
  }

  public function getAll() 
  {
    $sql = "SELECT
              {$this->tblName}.{$this->tblName}_id,
              {$this->tblName}.name,
              {$this->tblName}.link,
              {$this->tblName}.description,
              {$this->tblName}.icon
            FROM 
              {$this->tblName}
            WHERE 
              {$this->tblName}.status = '1'
            ORDER BY 
              {$this->tblName}.order_id 
            ASC
            ";
            
    return $this->connection()->rows($sql);
  }
}