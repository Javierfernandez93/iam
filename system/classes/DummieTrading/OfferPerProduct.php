<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Util;

class OfferPerProduct extends Orm {
	protected $tblName = 'offer_per_product';
	public function __construct() {
		parent::__construct();
	}

	public function getAll($store_per_user_id = null)
	{
		if(isset($store_per_user_id) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.start_date,
						{$this->tblName}.end_date,
						{$this->tblName}.offer,
						product.product_id,
						product.sku,
						product.price,
						product.title,
						catalog_offer.catalog_offer_id,
						catalog_offer.offer as offer_name
					FROM 
						{$this->tblName}
					LEFT JOIN 
						product
					ON 
						product.product_id = {$this->tblName}.product_id
					LEFT JOIN 
						catalog_offer
					ON 
						catalog_offer.catalog_offer_id = {$this->tblName}.catalog_offer_id
					WHERE 
						product.store_per_user_id = '{$store_per_user_id}'
					";
			
			return $this->connection()->rows($sql);
		}
		
		return false;
	}
}