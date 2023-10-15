<?php

namespace DummieTrading;

use HCStudio\Orm;

use JFStudio\Constants;

class TronWallet extends Orm {
  protected $tblName  = 'tron_wallet';

  const WAITING_FOR_GAS = 1;
  
  public function __construct() {
    parent::__construct();
  }

  public static function add(array $data = null) 
  {
    $TronWallet = new TronWallet;
    $TronWallet->private_key = $data['private_key'];
    $TronWallet->public_key = $data['public_key'];
    $TronWallet->address_hex = $data['address_hex'];
    $TronWallet->address_base58 = $data['address_base58'];
    $TronWallet->create_date = time();
    
    return ($TronWallet->save() )? $TronWallet->getId() : false;
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
  
  public function getWallet(int $tron_wallet_id = null) 
  {
    if(isset($tron_wallet_id) === true)
    {   
        $sql = "SELECT 
                  {$this->tblName}.address_base58 as address 
                FROM 
                  {$this->tblName} 
                WHERE
                  {$this->tblName}.tron_wallet_id = '{$tron_wallet_id}'
                AND 
                  {$this->tblName}.status IN (".Constants::AVIABLE.")
                ";
    
        return $this->connection()->field($sql);
    }

    return false;
  }
  
  public static function updateTrxBalance(int $tron_wallet_id = null,float $trx_balance = null) 
  {
    $TronWallet = new TronWallet;
    
    if($TronWallet->loadWhere('tron_wallet_id = ?',$tron_wallet_id))
    {
      $TronWallet->trx_balance = $trx_balance;
      
      return $TronWallet->save() ? $trx_balance : 0;
    }
  }
  
  public function getMainWalletAddress() 
  {
      $sql = "SELECT 
                {$this->tblName}.address_base58 as address 
              FROM 
                {$this->tblName} 
              WHERE
                {$this->tblName}.main = '1'
              AND 
                {$this->tblName}.status IN (".Constants::AVIABLE.")
              ";
  
      return $this->connection()->field($sql);
  }
  
  public function getMainWalletData() 
  {
      $sql = "SELECT 
                {$this->tblName}.address_base58 as address,
                {$this->tblName}.private_key
              FROM 
                {$this->tblName} 
              WHERE
                {$this->tblName}.main = '1'
              AND 
                {$this->tblName}.status IN (".Constants::AVIABLE.")
              ";
  
      return $this->connection()->row($sql);
  }

  public function _isWaitingForGas(int $tron_wallet_id = null) 
  {
    if(isset($tron_wallet_id) === true)
    {
      $sql = "SELECT 
                {$this->tblName}.waiting_for_gas
              FROM 
                {$this->tblName} 
              WHERE
                {$this->tblName}.tron_wallet_id = '{$tron_wallet_id}'
              AND 
                {$this->tblName}.status IN (".Constants::AVIABLE.")
              ";
  
      return $this->connection()->field($sql) ? true : false;
    }

    return false;
  }

  public static function isWaitingForGas(int $tron_wallet_id = null) : bool
  {
    return (new TronWallet)->_isWaitingForGas($tron_wallet_id);
  }
  
  public static function setWaitingForGas(int $tron_wallet_id = null) 
  {
    $TronWallet = new TronWallet;

    if($TronWallet->loadWhere('tron_wallet_id = ?',$tron_wallet_id))
    {
      $TronWallet->waiting_for_gas = self::WAITING_FOR_GAS;
      
      return $TronWallet->save();
    }

    return false;
  }
}