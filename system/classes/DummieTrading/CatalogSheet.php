<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Util;

class CatalogSheet extends Orm {
	protected $tblName = 'catalog_sheet';
	public function __construct() {
		parent::__construct();
	}
}