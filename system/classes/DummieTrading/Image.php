<?php

namespace DummieTrading;

use HCStudio\Orm;

class Image extends Orm {
  protected $tblName  = 'image';

  public function __construct() {
    parent::__construct();
  }
  
  public function getAll() 
  {
    $sql = "SELECT
                {$this->tblName}.title,
                {$this->tblName}.src,
                {$this->tblName}.tag,
                {$this->tblName}.create_date
            FROM 
                {$this->tblName}
            WHERE 
                {$this->tblName}.status = '1'
            ";
            
    return $this->connection()->rows($sql);
  }
}