<?php

namespace DummieTrading;

use HCStudio\Orm;

class CatalogProfit extends Orm {
	protected $tblName = 'catalog_profit';

	public function __construct() {
		parent::__construct();
	}
}