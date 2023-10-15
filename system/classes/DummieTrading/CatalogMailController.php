<?php

namespace DummieTrading;

use HCStudio\Orm;

class CatalogMailController extends Orm {
	protected $tblName = 'catalog_mail_controller';
	public function __construct() {
		parent::__construct();
	}

	public static function init(int $catalog_mail_controller_id = null)
	{
		if(isset($catalog_mail_controller_id) === true)
		{
			$CatalogMailController = new CatalogMailController;
			
			if($CatalogMailController->cargarDonde("catalog_mail_controller_id = ?",$catalog_mail_controller_id))
			{
				return $CatalogMailController;
			}
		}

        return false;
	}
}