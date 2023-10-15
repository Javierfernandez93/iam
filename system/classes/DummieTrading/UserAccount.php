<?php

namespace DummieTrading;

use HCStudio\Orm;

class UserAccount extends Orm {
  protected $tblName  = 'user_account';
  const DEFAULT_IMAGE = '';
  const DEFAULT_TIME_ZONE  = 'America/Mexico_City';

  public function __construct() {
    parent::__construct();
  }
  
  public function getTimeZone(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT 
                catalog_timezone.timezone
              FROM
                {$this->tblName}
              LEFT JOIN 
                catalog_timezone
              ON 
                catalog_timezone.catalog_timezone_id = {$this->tblName}.catalog_timezone_id
              WHERE
                {$this->tblName}.user_login_id = '{$user_login_id}'
              ";
      return $this->connection()->field($sql);
    }
  }
  public function existLanding(int $user_login_id = null,string $landing = null) 
  {
    if(isset($user_login_id,$landing) === true)
    {
      $sql = "SELECT 
                {$this->tblName}.{$this->tblName}_id
              FROM
                {$this->tblName}
              WHERE
                {$this->tblName}.user_login_id != '{$user_login_id}'
              AND 
                {$this->tblName}.landing = '{$landing}'
              ";

      return $this->connection()->field($sql) ? true : false;
    }

    return false;
  }
  
  public function getIdByLanding(string $landing = null) 
  {
    if(isset($landing) === true)
    {
      $sql = "SELECT 
                {$this->tblName}.user_login_id
              FROM
                {$this->tblName}
              WHERE
                {$this->tblName}.landing = '{$landing}'
              ";
              
      return $this->connection()->field($sql);
    }

    return false;
  }
  
  public function getLandingById(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT 
                {$this->tblName}.landing
              FROM
                {$this->tblName}
              WHERE
                {$this->tblName}.user_login_id = '{$user_login_id}'
              ";

      return $this->connection()->field($sql);
    }

    return false;
  }
}