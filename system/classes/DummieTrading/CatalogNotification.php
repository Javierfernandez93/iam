<?php

namespace DummieTrading;

use HCStudio\Orm;

class CatalogNotification extends Orm {
	protected $tblName = 'catalog_notification';
	const REFERRAL = 1;
	const ACCOUNT = 2;
	const GAINS = 3;
	public function __construct() {
		parent::__construct();
	}
}