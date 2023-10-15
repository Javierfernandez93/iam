<?php

namespace DummieTrading;

use HCStudio\Orm;

class CatalogCommissionType extends Orm {
  protected $tblName  = 'catalog_commission_type';

  const NETWORK = 'network';
  const GROUP = 'group';
  
  const NETWORK_TYPE_ID = 1;
  const GROUP_TYPE_ID = 2;

  public function __construct() {
    parent::__construct();
  }
}