<?php

namespace DummieTrading;

use HCStudio\Orm;

class CatalogTagValue extends Orm {
    protected $tblName  = 'catalog_tag_value';
    const TEXT = 1;
    const PHONE = 2;
    const IMAGE = 3;
    const EDITOR = 4;
    public function __construct() {
        parent::__construct();
    }
}