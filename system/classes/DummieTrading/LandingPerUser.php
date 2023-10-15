<?php

namespace DummieTrading;

use HCStudio\Orm;
use JFStudio\Constants;

class LandingPerUser extends Orm {
  protected $tblName  = 'landing_per_user';

  public function __construct() {
    parent::__construct();
  }
  
  public function getReferralIdByRoute(string $route = null) 
  {
    if(isset($route) === true) 
    {
      $sql = "SELECT 
                {$this->tblName}.user_login_id
              FROM 
                {$this->tblName} 
              WHERE 
                {$this->tblName}.route = '{$route}' 
              AND 
                {$this->tblName}.status IN (".Constants::AVIABLE.")
              ";

      return $this->connection()->field($sql);
    }

    return false;
  }

  public function getLandingIdByRoute(string $route = null,int $catalog_landing_id = null) 
  {
    if(isset($route) === true) 
    {
      $sql = "SELECT 
                {$this->tblName}.{$this->tblName}_id
              FROM 
                {$this->tblName} 
              WHERE 
                {$this->tblName}.route = '{$route}' 
              AND 
                {$this->tblName}.catalog_landing_id = '{$catalog_landing_id}' 
              AND 
                {$this->tblName}.status IN (".Constants::AVIABLE.")
              ";

      return $this->connection()->field($sql);
    }

    return false;
  }

  public function existRoute(int $user_login_id = null,int $catalog_landing_id = null,string $route = null) : bool
  {
    if(isset($user_login_id,$catalog_landing_id,$route) === true) 
    {
      $sql = "SELECT 
                {$this->tblName}.route
              FROM 
                {$this->tblName} 
              WHERE 
                {$this->tblName}.user_login_id != '{$user_login_id}' 
              AND 
                {$this->tblName}.catalog_landing_id = '{$catalog_landing_id}' 
              AND 
                {$this->tblName}.route = '{$route}' 
              AND 
                {$this->tblName}.status IN (".Constants::AVIABLE.")
              ";

      return $this->connection()->field($sql) ? true : false;
    }

    return false;
  }

  public function getLanding(int $user_login_id = null,int $catalog_landing_id = null) 
  {
    if(isset($user_login_id,$catalog_landing_id) === true) 
    {
      $sql = "SELECT 
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.route,
                {$this->tblName}.catalog_landing_id
              FROM 
                {$this->tblName} 
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}' 
              AND 
                {$this->tblName}.catalog_landing_id = '{$catalog_landing_id}' 
              AND 
                {$this->tblName}.status IN (".Constants::AVIABLE.")
              ";

      return $this->connection()->row($sql);
    }

    return false;
  }
}