<?php

namespace DummieTrading;

use HCStudio\Orm;

class CatalogSignalProvider extends Orm {
    protected $tblName  = 'catalog_signal_provider';

    const SEMI_COPY = 1;
    const PAMMY = 2;
    public function __construct() {
        parent::__construct();
    }
}