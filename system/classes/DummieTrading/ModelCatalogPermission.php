<?php

namespace DummieTrading;

class ModelCatalogPermission  {
	
	public function __construct() {
	
	}

	public static function getDefaulSellerPermission()
	{
		return [
			// 'add_client',
			'add_loan',
			'list_client',
			'list_loan',
			'report_per_client',
		];
	}
}