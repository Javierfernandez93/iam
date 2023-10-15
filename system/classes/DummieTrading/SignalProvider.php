<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Util;

use DummieTrading\TelegramChannel;
use DummieTrading\UserData;

use Api\MT4;

use BlockChain\Wallet;

class SignalProvider extends Orm
{
  protected $tblName = 'signal_provider';

  const TEST_SIGNAL_PROVIDER_ID = 11;
  public function __construct()
  {
    parent::__construct();
  }

  public static function isAbleToEnableCopy(int $user_login_id = null,int $user_trading_account_id = null)
  {
    if($Wallet = Wallet::getWallet($user_login_id))
    {
        return $Wallet->getBalance() >= Util::getPercentaje((new UserTradingAccount)->getBalance($user_trading_account_id),10);
    }

    return false;
  }

  public function get(int $signal_provider_id = null)
  {
    if (isset($signal_provider_id) === true) 
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.name
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.signal_provider_id = '{$signal_provider_id}'
              ";

      return $this->connection()->row($sql);
    }

    return false;
  }

  public function getByName(string $name = null)
  {
    if (isset($name) === true) {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.name
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.name = '{$name}'
              ";

      return $this->connection()->row($sql);
    }

    return false;
  }

  public function getAllWithChannels()
  {
    $TelegramChannel = new TelegramChannel;

    return array_map(function ($api) use ($TelegramChannel) {
      $api['channels'] = $TelegramChannel->getAllFromApi($api['telegram_api_id']);

      return $api;
    }, $this->getAll());
  }

  public function getAll()
  {
    $sql = "SELECT
              {$this->tblName}.{$this->tblName}_id,
              {$this->tblName}.name
            FROM 
              {$this->tblName}
            WHERE 
              {$this->tblName}.status = '1'
            ";

    return $this->connection()->rows($sql);
  }
  
  public function getName(int $signal_provider_id = null)
  {
    if(isset($signal_provider_id))
    {
      $sql = "SELECT
                {$this->tblName}.name
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.signal_provider_id = '{$signal_provider_id}'
              AND 
                {$this->tblName}.status = '1'
              ";
  
      return $this->connection()->field($sql);
    }

    return false;
  }

  public function getAllList(int $catalog_signal_provider_id = null)
  {
    $filter = isset($catalog_signal_provider_id) ? "AND {$this->tblName}.catalog_signal_provider_id = '{$catalog_signal_provider_id}'" : "";
    
    $sql = "SELECT
              {$this->tblName}.{$this->tblName}_id,
              {$this->tblName}.copy,
              {$this->tblName}.description,
              {$this->tblName}.image,
              {$this->tblName}.name,
              {$this->tblName}.catalog_campaign_ids,
              {$this->tblName}.user_trading_account_id,
              {$this->tblName}.catalog_trading_account_id,
              catalog_trading_account.type
            FROM 
              {$this->tblName}
            LEFT JOIN 
              catalog_trading_account
            ON
              catalog_trading_account.catalog_trading_account_id = {$this->tblName}.catalog_trading_account_id
            WHERE 
              {$this->tblName}.status = '1'
            AND 
              {$this->tblName}.visible = '1'
              {$filter}
            ";

    return $this->connection()->rows($sql);
  }

  public function getUserTradingAccountById(int $signal_provider_id = null) : int|bool
  {
    if(isset($signal_provider_id))
    {
      $sql = "SELECT
                {$this->tblName}.user_trading_account_id
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.status = '1'
              AND
                {$this->tblName}.signal_provider_id = '{$signal_provider_id}'
              ";
  
      return $this->connection()->field($sql);
    }

    return false;
  }

  public function getSignalProviderId(int $user_trading_account_id = null) : int|bool
  {
    if(isset($user_trading_account_id))
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.status = '1'
              AND
                {$this->tblName}.user_trading_account_id = '{$user_trading_account_id}'
              ";
  
      return $this->connection()->field($sql);
    }

    return false;
  }

  public static function enableCopy(array $data = null) : array|bool
  {
    if(isset($data))
    {
      $UserTradingAccount = new UserTradingAccount;
      
      if($providerAccountId = $UserTradingAccount->getIdById($data['user_trading_account_provider_id']))
      {
        if($subscriberAccountId = $UserTradingAccount->getIdById($data['user_trading_account_id']))
        {
          if($strategyId = $UserTradingAccount->getStrategyId($data['user_trading_account_provider_id']))
          {
            $user_login_id = $UserTradingAccount->getUserIdById($data['user_trading_account_provider_id']);

            return MT4::addSuscriber([
              'subscriberAccountId' => $subscriberAccountId,
              'name' => (new UserData)->getName($user_login_id),
              'providerAccountId' => $providerAccountId,
              'strategyId' => $strategyId
            ]);
          }
        }
      }
    } 

    return false;
  }
}
