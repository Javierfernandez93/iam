<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Token;

use JFStudio\Constants;

use DummieTrading\UserData;

class CreditPerUser extends Orm {
  protected $tblName  = 'credit_per_user';

  /* constants */
  const AVIABLE = 1;
  const USED = 2;
  const UNAVIABLE = 0;
  const DELETED = -1;
  const LICENCE_KEY_LENGTH = 10;

  const LICENCE_DURATION_DAYS = 30;

  public function __construct() {
    parent::__construct();
  }
  
  public static function restCredits(int $user_login_id = null,int $quantity = null) : bool
  {
    $CreditPerUser = new CreditPerUser;

    if(!$CreditPerUser->loadWhere('user_login_id = ?',$user_login_id))
    {
        $CreditPerUser->user_login_id = $user_login_id;
        $CreditPerUser->create_date = time();
    }
    
    $CreditPerUser->credit -= $quantity;

    return $CreditPerUser->save();
  }

  public static function addCredits(int $user_login_id = null,int $quantity = null) : bool
  {
    $CreditPerUser = new CreditPerUser;

    if(!$CreditPerUser->loadWhere('user_login_id = ?',$user_login_id))
    {
        $CreditPerUser->user_login_id = $user_login_id;
        $CreditPerUser->create_date = time();
    }
    
    $CreditPerUser->credit += $quantity;

    return $CreditPerUser->save();
  }

  public function exist(string $code = null) : bool
  {
    if(isset($code) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.code = '{$code}'
              AND 
                {$this->tblName}.code != '".Constants::DELETE."'
              ";
              
      return $this->connection()->field($sql) ? true : false;
    }

    return false;
  }

  public function getAll(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.code,
                {$this->tblName}.active_date,
                {$this->tblName}.create_date,
                {$this->tblName}.user_login_id_to,
                {$this->tblName}.status
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
            AND 
                {$this->tblName}.code != '".Constants::DELETE."'
              ";
              
      return $this->connection()->rows($sql);
    }

    return false;
  }
  
  public static function calculateLeftDays(int $active_date = null) 
  {
    $endSuscription = strtotime("+".self::LICENCE_DURATION_DAYS." days", $active_date);

    return round(($endSuscription- time()) / (60 * 60 * 24));
  }

  public function _getAll(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true)
    {
      if($licences = $this->getAll($user_login_id))
      {
        $UserData = new UserData;
        return array_map(function($licence) use($UserData) {
          if($licence['status'] == self::USED) 
          {
            $days = self::calculateLeftDays($licence['active_date']);
            $licence['left'] = [
              'days' => $days,
              'percentaje' => $days * 100 / 30
            ];
            $licence['names'] = $UserData->getNames($licence['user_login_id_to']);
          }

          return $licence;
        },$licences);
      }
    }

    return false;
  }
  
  public function hasAviableLicences(int $user_login_id = null) : bool
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT
                COUNT({$this->tblName}.{$this->tblName}_id) as c
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
            AND 
                {$this->tblName}.status = '".self::AVIABLE."'
              ";
              
      return $this->connection()->field($sql) ? true : false;
    }

    return false;
  }

  public function getAviableLicenceId(int $user_login_id = null) 
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
                {$this->tblName}.status = '".self::AVIABLE."'
              ";
              
      return $this->connection()->row($sql);
    }

    return false;
  }
  
  public function getCreditsAmount(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT
                    {$this->tblName}.credit
                FROM 
                    {$this->tblName}
                WHERE 
                    {$this->tblName}.user_login_id = '{$user_login_id}'
                AND 
                    {$this->tblName}.status = '".self::AVIABLE."'
                ";

      return $this->connection()->field($sql);
    }

    return false;
  }
}