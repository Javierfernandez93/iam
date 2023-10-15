<?php

namespace DummieTrading;

use HCStudio\Orm;

use JFStudio\Constants;
use DummieTrading\TronWallet;

class PaymentGateway extends Orm {
  protected $tblName  = 'payment_gateway';

  const DEFAULT_HOURS = 2;

  const PENDING = 1;
  const PAYED = 2;

  public function __construct() {
    parent::__construct();
  }

  public static function load(int $buy_per_user_login_id = null) 
  {
    if(isset($buy_per_user_login_id) === true) 
    {
      $PaymentGateway = new PaymentGateway;
      
      if($PaymentGateway->loadWhere('buy_per_user_login_id = ?',$buy_per_user_login_id))
      {
        $TronWallet = new TronWallet;
        
        if($wallet = $TronWallet->getWallet($PaymentGateway->tron_wallet_id))
        {
          return $wallet;
        }
      }
    }
  }

  public static function add(array $data = null) : bool
  {
    if($tron_wallet_id = TronWallet::add($data))
    {
      $PaymentGateway = new PaymentGateway;
      $PaymentGateway->buy_per_user_id = $data['buy_per_user_id'];
      $PaymentGateway->tron_wallet_id = (int)$tron_wallet_id;
      $PaymentGateway->create_date = time();
      $PaymentGateway->expiration_date = $data['expiration_date'];
      
      return $PaymentGateway->save();
    }

    return false;
  }

  public function getAllPending() 
  {
    $sql = "SELECT 
              {$this->tblName}.{$this->tblName}_id,
              buy_per_user.buy_per_user_id,
              buy_per_user.amount,
              tron_wallet.trx_balance,
              tron_wallet.tron_wallet_id,
              tron_wallet.address_base58 as address,
              tron_wallet.public_key,
              tron_wallet.private_key
            FROM 
              {$this->tblName} 
            LEFT JOIN 
              buy_per_user
            ON 
              buy_per_user.buy_per_user_id = {$this->tblName}.buy_per_user_id
            LEFT JOIN 
              tron_wallet
            ON 
              tron_wallet.tron_wallet_id = {$this->tblName}.tron_wallet_id
            WHERE
              {$this->tblName}.status IN (".self::PENDING.")
            ";

    return $this->connection()->rows($sql);
  }

  public function getAll() 
  {
    $sql = "SELECT 
              {$this->tblName}.{$this->tblName}_id,
              {$this->tblName}.text 
            FROM 
              {$this->tblName} 
            WHERE
              {$this->tblName}.status IN (".Constants::AVIABLE.")
            ";

    return $this->connection()->rows($sql);
  }

  public static function setStatusAs(int $payment_gateway_id = null,int $status = null) : bool
  {
    if(isset($payment_gateway_id) === true) 
    {
      $PaymentGateway = new PaymentGateway;
      
      if($PaymentGateway->loadWhere('payment_gateway_id = ?', $payment_gateway_id))
      {
        $PaymentGateway->status = $status;
        
        return $PaymentGateway->save();
      }
    }

    return false;
  }
}