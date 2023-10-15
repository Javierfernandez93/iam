<?php

namespace DummieTrading;

use HCStudio\Orm;

class ClientPerSeller extends Orm {
  protected $tblName  = 'client_per_seller';

  public function __construct() {
    parent::__construct();
  }

  public function getSellerName($user_login_id = null) 
  {
    if (isset($user_login_id) === true) 
    {
      $sql = "SELECT 
                LOWER(CONCAT_WS(' ',user_support.names,user_support.last_name,user_support.sur_name)) as names
              FROM 
                {$this->tblName}
              LEFT JOIN 
                user_support
              ON 
                user_support.user_support_id = {$this->tblName}.user_support_id
              WHERE
                {$this->tblName}.user_login_id = '{$user_login_id}'
              AND 
                {$this->tblName}.status = '1'
              ";

      return $this->connection()->field($sql);
    }
  }

  public function getSellerId($user_login_id = null) 
  {
    if (isset($user_login_id) === true) 
    {
      $sql = "SELECT 
                user_support.user_support_id
              FROM 
                {$this->tblName}
              LEFT JOIN 
                user_support
              ON 
                user_support.user_support_id = {$this->tblName}.user_support_id
              WHERE
                {$this->tblName}.user_login_id = '{$user_login_id}'
              AND 
                {$this->tblName}.status = '1'
              ";

      return $this->connection()->field($sql);
    }
  }

  public function getSellerShortName($user_login_id = null) 
  {
    if (isset($user_login_id) === true) 
    {
      $sql = "SELECT 
                user_support.names
              FROM 
                {$this->tblName}
              LEFT JOIN 
                user_support
              ON 
                user_support.user_support_id = {$this->tblName}.user_support_id
              WHERE
                {$this->tblName}.user_login_id = '{$user_login_id}'
              ";

      return $this->connection()->field($sql);
    }
  }
}