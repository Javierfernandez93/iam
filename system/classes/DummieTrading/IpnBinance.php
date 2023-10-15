<?php

namespace DummieTrading;

use HCStudio\Orm;

class IpnBinance extends Orm {
  protected $tblName  = 'ipn_binance';

  public function __construct() {
    parent::__construct();
  }

  public static function add(array $data = null)
  {
    if(isset($data) === true)
    {
        $IpnBinance = new self;
        $IpnBinance->data = json_encode($data);
        $IpnBinance->create_date = time();
        $IpnBinance->save();
    }
  }
}