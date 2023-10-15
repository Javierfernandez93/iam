<?php

namespace DummieTrading;

use HCStudio\Orm;

class UserCard extends Orm {
  protected $tblName  = 'user_card';

  public function __construct() {
    parent::__construct();
  }
  
  public function hasCard($user_login_id = null) : bool
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              AND 
                {$this->tblName}.status = '1'
              ";

      return $this->connection()->field($sql) ? true : false;
    }

    return false;
  }
}