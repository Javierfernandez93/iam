<?php

namespace DummieTrading;

use HCStudio\Orm;

class IpnMt5 extends Orm {
  protected $tblName  = 'ipn_mt5';

  public function __construct() {
    parent::__construct();
  }

  public static function add(array $data = null)
  {
    $IpnMt5 = new self;
    $IpnMt5->data = json_encode($data);
    $IpnMt5->create_date = time();
    
    return $IpnMt5->save();
  }
}