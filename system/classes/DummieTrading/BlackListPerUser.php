<?php

namespace DummieTrading;

use HCStudio\Orm;

class BlackListPerUser extends Orm {
  protected $tblName  = 'black_list_per_user';
  public static $ACTIVE = 1;
  public static $DELETED = -1;
  public function __construct() {
    parent::__construct();
  }

  public function isInBlackList($user_login_id = null) 
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
                {$this->tblName}.status = '".self::$ACTIVE."'
              ";

      return $this->connection()->field($sql) ? true : false;
    }

    return false;
  }
}