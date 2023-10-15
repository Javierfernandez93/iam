<?php

namespace DummieTrading;

use HCStudio\Orm;

class CatalogSpeaker extends Orm {
  protected $tblName  = 'catalog_speaker';
  
  public function __construct() {
    parent::__construct();
  }
}
