<?php

namespace DummieTrading;

use HCStudio\Orm;

class CatalogGain extends Orm {
	protected $tblName = 'catalog_gain';
    public static $REFERREAL = 1;
    public static $COURSE = 2;
	public function __construct() {
		parent::__construct();
	}
}