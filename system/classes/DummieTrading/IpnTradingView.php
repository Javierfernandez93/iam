<?php

namespace DummieTrading;

use HCStudio\Orm;

class IpnTradingView extends Orm {
  protected $tblName  = 'ipn_trading_view';

  public function __construct() {
    parent::__construct();
  }

  public static function add(array $data = null)
  {
    if(isset($data) === true)
    {
        $IpnTradingView = new self;
        $IpnTradingView->data = json_encode($data);
        $IpnTradingView->create_date = time();
        $IpnTradingView->save();
    }
  }
}