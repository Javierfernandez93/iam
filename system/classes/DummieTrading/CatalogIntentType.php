<?php

namespace DummieTrading;

use HCStudio\Orm;

class CatalogIntentType extends Orm {
    protected $tblName  = 'catalog_intent_type';
    public static $MESSAGE = 1;
    public static $KEYWORDS = 2;
    public function __construct() {
        parent::__construct();
    }
}