<?php

namespace DummieTrading;

use HCStudio\Orm;

class BeneficiaryPerUser extends Orm {
  protected $tblName  = 'beneficiary_per_user';
  public function __construct() {
    parent::__construct();
  }
  public function getBeneficiaries($user_login_id = null) 
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT 
                {$this->tblName}.user_login_beneficiary_id
              FROM 
                {$this->tblName}
              WHERE
                {$this->tblName}.user_login_id = '{$user_login_id}'
              AND 
                {$this->tblName}.status = '1'
              ";

      return $this->connection()->column($sql);
    } 

    return false;
  }
}