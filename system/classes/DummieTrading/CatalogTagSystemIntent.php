<?php

namespace DummieTrading;

use HCStudio\Orm;

class CatalogTagSystemIntent extends Orm {
    protected $tblName  = 'catalog_tag_system_intent';
    public function __construct() {
        parent::__construct();
    }
}