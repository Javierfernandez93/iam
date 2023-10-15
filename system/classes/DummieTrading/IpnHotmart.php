<?php

namespace DummieTrading;

use HCStudio\Orm;

class IpnHotmart extends Orm {
  protected $tblName  = 'ipn_hotmart';

  public function __construct() {
    parent::__construct();
  }
  
  public static function add(array $data = null) : bool
  {
    $WebHook = new self;
    $WebHook->data = json_encode($data);
    $WebHook->create_date = time();

    return $WebHook->save();
  }
}