<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Session;
use JFStudio\Cookie;

class ProspectData extends Orm {
    protected $tblName  = 'prospect_data';
    public function __construct() {
        parent::__construct();
    }
}