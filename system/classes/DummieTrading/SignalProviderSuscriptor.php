<?php

namespace DummieTrading;

use HCStudio\Orm;

class SignalProviderSuscriptor extends Orm
{
  protected $tblName = 'signal_provider_suscriptor';

  const SUSCRIPTIONS_PERMITTED = 1;

  public function __construct()
  {
    parent::__construct();
  }

  public static function attachSuscriptor(array $data = null) : array|bool
  {
    if(isset($data))
    {
      $SignalProviderSuscriptor = new self;
      $SignalProviderSuscriptor->user_trading_account_id = $data['user_trading_account_id'];
      $SignalProviderSuscriptor->signal_provider_id = $data['signal_provider_id'];
      $SignalProviderSuscriptor->create_date = time();
      
      return $SignalProviderSuscriptor->save();
    } 

    return false;
  }

  public function getCount(int $user_trading_account_id = null)
  {
    if (isset($user_trading_account_id) === true) 
    {
      $sql = "SELECT
                COUNT({$this->tblName}.{$this->tblName}_id) as c
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_trading_account_id = '{$user_trading_account_id}'
              ";

      return $this->connection()->field($sql);
    }

    return false;
  }
  
  public function isSuscribedIn(string $user_trading_account_ids = null,int $signal_provider_id = null) : bool
  {
    if (isset($user_trading_account_ids) === true) 
    {
      $sql = "SELECT
                COUNT({$this->tblName}.{$this->tblName}_id) as c
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_trading_account_id IN({$user_trading_account_ids})
              AND
                {$this->tblName}.signal_provider_id = '{$signal_provider_id}'
              AND 
                {$this->tblName}.status = '1'
              ";

      return $this->connection()->field($sql);
    }

    return false;
  }

  public function isAviableToSuscribe(int $user_trading_account_id = null) : bool
  {
    if (isset($user_trading_account_id) === true) 
    {
      return $this->getCount($user_trading_account_id) < self::SUSCRIPTIONS_PERMITTED;
    }

    return false;
  }
}
