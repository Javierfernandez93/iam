<?php

namespace DummieTrading;

use HCStudio\Orm;

use JFStudio\Constants;

class UserMetatrader extends Orm {
  protected $tblName  = 'user_metatrader';

  public function __construct() {
    parent::__construct();
  }

  public static function createUser(array $data = null) : bool
  {
    if(isset($data) === true)
    {
        $UserMetatrader = new self;
        
        if(!$UserMetatrader->exist($data['user_login_id']))
        {
            $UserMetatrader->user_login_id = $data['user_login_id'];
            
            $UserMetatrader->login = $data['login'];
            
            $UserMetatrader->create_date = time();
            
            return $UserMetatrader->save();
        }
    }

    return false;
  }

  public function getUserIdByLogin(string $login = null)
  {
    if(isset($login) === true)
    {
      $sql = "SELECT
                {$this->tblName}.user_login_id
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.login = '{$login}'
              AND
                {$this->tblName}.status = '".Constants::AVIABLE."'
              ";
              
      return $this->connection()->field($sql);
    }

    return false;
  }
  
  public function getIdByLogin(string $login = null)
  {
    if(isset($login) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.login = '{$login}'
              AND
                {$this->tblName}.status = '".Constants::AVIABLE."'
              ";
              
      return $this->connection()->field($sql);
    }

    return false;
  }

  public function getIdByUserId(string $user_login_id = null)
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
                {$this->tblName}.status = '".Constants::AVIABLE."'
              ";
              
      return $this->connection()->field($sql);
    }

    return false;
  }
    
  public function exist(int $user_login_id = null) : bool
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.user_login_id
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              AND
                {$this->tblName}.status = '".Constants::AVIABLE."'
              ";
              
      return $this->connection()->field($sql) ? true : false;
    }

    return false;
  }
}