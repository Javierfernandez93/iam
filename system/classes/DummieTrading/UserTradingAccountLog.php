<?php

namespace DummieTrading;

use HCStudio\Orm;

class UserTradingAccountLog extends Orm {
  protected $tblName  = 'user_trading_account_log';
  public function __construct() {
    parent::__construct();
  }

  public static function add(int $user_trading_account_id = null) : bool
  {
    if(isset($user_trading_account_id) === true)
    {
        $UserTradingAccountLog = new self;
        $UserTradingAccountLog->user_trading_account_id = $user_trading_account_id;
        $UserTradingAccountLog->create_date = time();
    
        return $UserTradingAccountLog->save();
    }

    return false;
  }
}