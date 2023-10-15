<?php

namespace DummieTrading;

use HCStudio\Orm;

class UserType extends Orm {
  protected $tblName  = 'user_type';

  public function __construct() {
    parent::__construct();
  }

  public function getByType($user_login_id = null,$catalog_user_type_id = null) 
  {
    if(isset($user_login_id,$catalog_user_type_id) === true)
    {
      $sql = "SELECT 
                {$this->tblName}.user_login_id
              FROM 
                {$this->tblName}
              WHERE
                {$this->tblName}.master_user_login_id = '{$user_login_id}'
              AND 
                {$this->tblName}.catalog_user_type_id = '{$catalog_user_type_id}'
              AND 
                {$this->tblName}.status = '1'
              ";

      return $this->connection()->column($sql);
    } 

    return false;
  }
}