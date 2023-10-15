<?php

namespace DummieTrading;

use HCStudio\Orm;

class UserSupportAddress extends Orm {
  protected $tblName  = 'user_support_address';

  public function __construct() {
    parent::__construct();
  }
}