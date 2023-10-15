<?php

namespace DummieTrading;

use HCStudio\Orm;

class UserEducator extends Orm {
  protected $tblName  = 'user_educator';

  public function __construct() {
    parent::__construct();
  }
  
  public function isEducator(int $user_login_id = null) : bool
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

      if($this->connection()->field($sql))
      {
        return true;
      }
    }

    return false;
  }
}