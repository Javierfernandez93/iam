<?php

namespace DummieTrading;

use HCStudio\Orm;

use JFStudio\Constants;

class CatalogLandingAction extends Orm {
  protected $tblName  = 'catalog_landing_action';

  public function __construct() {
    parent::__construct();
  }

  public function getAll() 
  {
    $sql = "SELECT 
              {$this->tblName}.{$this->tblName}_id,
              {$this->tblName}.text 
            FROM 
              {$this->tblName} 
            WHERE
              {$this->tblName}.status IN (".Constants::AVIABLE.")
            ";

    return $this->connection()->rows($sql);
  }

}