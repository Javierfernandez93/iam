<?php

namespace DummieTrading;

use HCStudio\Orm;
use JFStudio\Constants;

class UserReferral extends Orm {
  protected $tblName  = 'user_referral';

  const WAITING_FOR_PAYMENT = 0;
  const DEFAULT_COMMISSION = 0;
  
  public function __construct() {
    parent::__construct();
  }
  
  public static function addCommission(int $user_login_id = null,float $commission = 0) 
  {
    if(isset($user_login_id) === true)
    {
      $UserReferral = new UserReferral;
      
      if($UserReferral->loadWhere('user_login_id = ?', $user_login_id))
      {
        $UserReferral->commission = $commission;

        return $UserReferral->save();
      }
    }

    return false;
  }
  
  public function getLastReferrals(int $referral_id = null) 
  {
    return $this->getReferrals($referral_id," ORDER BY {$this->tblName}.create_date DESC LIMIT 5 ");
  }

  public function getReferralCount(int $referral_id = null,string $filter = '') 
  {
    if(isset($referral_id) === true) 
    {
      $sql = "SELECT 
                COUNT({$this->tblName}.user_login_id) as c
              FROM 
                {$this->tblName} 
              WHERE 
                {$this->tblName}.referral_id = '{$referral_id}' 
              AND 
                {$this->tblName}.status != '".Constants::DELETE."'
                {$filter}
              ";
              
      return $this->connection()->field($sql);
    }
  }
  
  public function getReferralId(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true) 
    {
      $sql = "SELECT 
                {$this->tblName}.referral_id
              FROM 
                {$this->tblName} 
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}' 
              AND 
                {$this->tblName}.status IN (".Constants::AVIABLE.",".self::WAITING_FOR_PAYMENT.")
              ";

      return $this->connection()->field($sql);
    }

    return false;
  }

  public function getNextPosition(int $referral_id = null) 
  {
    return $this->getLastPosition($referral_id) + 1;
  }

  public function getLastPosition(int $referral_id = null) : int 
  {
    if(isset($referral_id) === true) 
    {
      $sql = "SELECT 
                {$this->tblName}.position
              FROM 
                {$this->tblName} 
              WHERE 
                {$this->tblName}.referral_id = '{$referral_id}' 
              AND 
                {$this->tblName}.status IN (".Constants::AVIABLE.")
              ORDER BY 
                {$this->tblName}.position
              DESC 
              LIMIT 1
              ";

      if($last_position = $this->connection()->field($sql))
      {
        return $last_position;
      }
    }

    return 0;
  }

  public function getUserReferralId(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true) 
    {
      $sql = "SELECT 
                {$this->tblName}.referral_id
              FROM 
                {$this->tblName} 
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}' 
              AND 
                {$this->tblName}.status != '".Constants::DELETE."'
              ";

      return $this->connection()->field($sql);
    }
  }

  public function getCommission(int $user_login_id = null) : float 
  {
    if(isset($user_login_id) === true) 
    {
      $sql = "SELECT 
                {$this->tblName}.commission
              FROM 
                {$this->tblName} 
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}' 
              AND 
                {$this->tblName}.status != '".Constants::DELETE."'
              ";

      return $this->connection()->field($sql);
    }

    return 0;
  }
  
  public function getInfo(int $user_login_id = null)  
  {
    if(isset($user_login_id) === true) 
    {
      $sql = "SELECT 
                {$this->tblName}.referral_id,
                {$this->tblName}.commission
              FROM 
                {$this->tblName} 
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}' 
              AND 
                {$this->tblName}.status != '".Constants::DELETE."'
              ";

      return $this->connection()->row($sql);
    }

    return false;
  }

  public function getReferrals(int $referral_id = null,string $filter = '') 
  {
    if(isset($referral_id) === true) 
    {
      $sql = "SELECT 
                {$this->tblName}.user_login_id,
                user_data.names,
                user_address.country_id,
                user_account.image,
                user_login.signup_date,
                user_login.company_id,
                user_login.email,
                user_contact.phone
              FROM 
                {$this->tblName} 
              LEFT JOIN 
                user_data
              ON 
                user_data.user_login_id = {$this->tblName}.user_login_id
              LEFT JOIN 
                user_account
              ON 
                user_account.user_login_id = {$this->tblName}.user_login_id
              LEFT JOIN 
                user_login
              ON 
                user_login.user_login_id = {$this->tblName}.user_login_id
              LEFT JOIN 
                user_address
              ON 
                user_address.user_login_id = {$this->tblName}.user_login_id
              LEFT JOIN 
                user_contact
              ON 
                user_contact.user_login_id = {$this->tblName}.user_login_id
              WHERE 
                {$this->tblName}.referral_id = '{$referral_id}' 
              AND 
                {$this->tblName}.status IN (".self::WAITING_FOR_PAYMENT.",".Constants::AVIABLE.")
                {$filter}
              ";

      return $this->connection()->rows($sql);
    }
  }
  
  public function getReferralsIds(int $referral_id = null) 
  {
    if(isset($referral_id) === true) 
    {
      $sql = "SELECT 
                {$this->tblName}.user_login_id
              FROM 
                {$this->tblName} 
              WHERE 
                {$this->tblName}.referral_id = '{$referral_id}' 
              AND 
                {$this->tblName}.status IN (".self::WAITING_FOR_PAYMENT.",".Constants::AVIABLE.")
              ";

      return $this->connection()->column($sql);
    }
  }
  
  public function getReferral(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true) 
    {
      $sql = "SELECT 
                user_login.user_login_id,
                user_data.names,
                user_account.image,
                user_login.signup_date,
                user_login.email
              FROM 
                {$this->tblName} 
              LEFT JOIN 
                user_data
              ON 
                user_data.user_login_id = {$this->tblName}.referral_id
              LEFT JOIN 
                user_account
              ON 
                user_account.user_login_id = {$this->tblName}.referral_id
              LEFT JOIN 
                user_login
              ON 
                user_login.user_login_id = {$this->tblName}.referral_id
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}' 
              AND 
                {$this->tblName}.status != '".Constants::DELETE."'
              GROUP BY 
                {$this->tblName}.user_login_id
              ";
              
      return $this->connection()->row($sql);
    }
  }
}