<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Util;

class ImagePerProduct extends Orm {
	protected $tblName = 'image_per_product';
	public static $IMAGES_PER_PRODUCT = 5;
	public function __construct() {
		parent::__construct();
	}

	public function getMainImage($product_id = null)
	{
		if(isset($product_id) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.path
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.product_id = '{$product_id}'
					AND 
						{$this->tblName}.main = '1'
					";
			
			return $this->connection()->field($sql);
		}

		return false;
	}

	public function getImages($product_id = null)
	{
		if(isset($product_id) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id,
						{$this->tblName}.path
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.product_id = '{$product_id}'
					ORDER BY 
						{$this->tblName}.main 
					DESC
					";
			
			return $this->connection()->rows($sql);
		}

		return false;
	}
}