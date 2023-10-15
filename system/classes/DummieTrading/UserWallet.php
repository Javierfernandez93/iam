<?php

namespace DummieTrading;

use HCStudio\Orm;

use DummieTrading\TransactionPerWallet;
use DummieTrading\WithdrawPerUser;

class UserWallet extends Orm
{
  protected $tblName  = 'user_wallet';

  public function __construct()
  {
    parent::__construct();
  }

  public function getSafeWallet(int $company_id = null): bool
  {
    if (!$this->cargarDonde("user_login_id = ?", $company_id)) {
      $this->user_login_id = $company_id;
      $this->create_date = time();
      return $this->save();
    }

    return true;
  }
  
  public function getCompanyId(int $user_wallet_id = null)
  {
    if (isset($user_wallet_id) === true) 
    {
      $sql = "SELECT
                {$this->tblName}.user_login_id
              FROM 
                {$this->tblName}
              WHERE
                {$this->tblName}.user_wallet_id = '{$user_wallet_id}'
              AND 
                {$this->tblName}.status = '1'
              ";

      return $this->connection()->field($sql);
    }

    return true;
  }

  public function doTransaction(float $ammount, int $transaction_id = null, int $profit_per_user_id = null,int $withdraw_method_id = null,$load_previus = true)
  {
    if ($this->getId()) {
      $TransacionPerWallet = new TransactionPerWallet;

      switch ($transaction_id) {
        case Transaction::DEPOSIT:
          return $TransacionPerWallet->doTransaction($this->getId(), $ammount, $transaction_id, $profit_per_user_id, $load_previus);
        case Transaction::INVESTMENT:
          return $TransacionPerWallet->doTransaction($this->getId(), $ammount, $transaction_id, $profit_per_user_id);
        case Transaction::REFERRAL_INVESTMENT:
          return $TransacionPerWallet->doTransaction($this->getId(), $ammount, $transaction_id, $profit_per_user_id);
        case Transaction::WITHDRAW:
          if($transaction_per_wallet_id = $TransacionPerWallet->doTransaction($this->getId(), -$ammount, $transaction_id, $profit_per_user_id))
          {
            return (new WithdrawPerUser)->doWithdraw($transaction_per_wallet_id,$withdraw_method_id);
          }
        default:
          return false;
      }
    }

    return false;
  }

  public function depositGains(float $ammount = null)
  {
    if(isset($ammount) === true) 
    {
      if ($this->getId()) 
      {
        $TransacionPerWallet = new TransactionPerWallet;

          // if($transaction_per_wallet_id = $TransacionPerWallet->doTransaction($this->getId(), -$ammount, Transaction::WITHDRAW))
          // {
          //   if((new WithdrawPerUser)->doWithdraw($transaction_per_wallet_id,0,true)) // forcing withdraw
          //   {
              return $TransacionPerWallet->doTransaction($this->getId(), $ammount, Transaction::DEPOSIT, null, false);
          //   }
          // }
      }
    }

    return false;
  }

  public function getWithdraws(string $filter = null)
  {
    if ($this->getId()) {
      return (new TransactionPerWallet)->getWithdraws($this->getId(), $filter);
    }

    return false;
  }

  public function getDepositsByUser()
  {
    if ($this->getId()) {
      return (new TransactionPerWallet)->getDepositsByUser($this->getId());
    }

    return false;
  }

  public function getBalance(string $filter = '',string $status = null): float
  {
    if ($this->getId()) {
      return (new TransactionPerWallet)->getBalance($this->getId(), $filter, $status);
    }

    return false;
  }
}
