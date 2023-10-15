<?php

namespace DummieTrading;

use HCStudio\Orm;

class UserAddressWork extends Orm {
  protected $tblName  = 'user_address_work';

  public function __construct() {
    parent::__construct();
  }

  public function getAddress($user_login_id = null)
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.address,
                {$this->tblName}.city,
                {$this->tblName}.colony,
                {$this->tblName}.zip_code,
                {$this->tblName}.state,
                {$this->tblName}.country_id
              FROM 
                {$this->tblName}
              WHERE
                {$this->tblName}.user_login_id = '{$user_login_id}'
              ";
                
      return $this->connection()->row($sql);
    }

    return false;
  }
}