<?php

namespace DummieTrading;

use HCStudio\Orm;

class CatalogTimezone extends Orm {
  protected $tblName  = 'catalog_timezone';
  public function __construct() {
    parent::__construct();
  }

  public static function convertTimeZone(string $time = null,string $currentTimeZone = null,string $timeZone = null) 
  {
    $date = date_create($time, timezone_open($currentTimeZone));
    
    date_timezone_set($date, timezone_open($timeZone));

    return $date->format('H:i a');
  }

  public function getAll() 
  {
    $sql = "SELECT
              {$this->tblName}.{$this->tblName}_id,
              {$this->tblName}.timezone
            FROM 
              {$this->tblName}
            WHERE 
              {$this->tblName}.status = '1'
            ";
            
    return $this->connection()->rows($sql);
  }
}