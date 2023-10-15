<?php

namespace DummieTrading;

use HCStudio\Orm;

class CatalogUserType extends Orm {
  protected $tblName  = 'catalog_user_type';
  public static $CLIENT = 1;
  public static $BENEFICIARY = 2;
  public static $SELLER = 3;
  public static $ADMIN = 4;
  public static $AVAL = 5;
  public function __construct() {
    parent::__construct();
  }
  public function getUserType($catalog_user_type_id = null) 
  {
    if(isset($catalog_user_type_id) === true) 
    {
      $sql = "SELECT
                {$this->tblName}.description
              FROM 
                {$this->tblName}
              WHERE
                {$this->tblName}.catalog_user_type_id = '{$catalog_user_type_id}'
              AND 
                {$this->tblName}.status = '1'
              ";

      return $this->connection()->field($sql);
    }

    return false;
  }
}