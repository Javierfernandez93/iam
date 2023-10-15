<?php

namespace DummieTrading;

use HCStudio\Orm;

use JFStudio\Constants;

use DummieTrading\Product;
use DummieTrading\BuyPerUser;

class Package extends Orm {
	protected $tblName = 'package';
	
	const INITIAL_PACKAGE = 1;
	const MONTHLY_PACKAGE = 5;
	const ACELERATED_ACCOUNTS_PACKAGES = [1,2,3,4,5];

	const PREMIUM_ONE_MONTH_OZ_CAMPAIGN = 13;
	const MEMBERSHIP_SINGLE = 1;
	const MEMBERSHIP_COPY = 2;
	const MEMBERSHIPS = [self::MEMBERSHIP_SINGLE,self::MEMBERSHIP_COPY];
	const TRIAL = 7;

	public function __construct() {
		parent::__construct();
	}

	public static function isAceleratedAccountPackage(int $package_id = null) : bool
	{
		if(isset($package_id) === true)
		{
			return in_array($package_id,self::ACELERATED_ACCOUNTS_PACKAGES);
		}

		return false;
	}

	public static function getMonthlyPackage(int $user_login_id = null)
	{
		if((new BuyPerUser)->hasPackageBuy($user_login_id,self::INITIAL_PACKAGE))
		{
			return self::MONTHLY_PACKAGE;
		} else {
			return self::INITIAL_PACKAGE;
		}
	}

	public static function getProducts(array $products = null) : array 
	{
		$Product = new Product; 
		return array_map(function($product) use($Product) {
			$product['product'] = $Product->getProduct($product['product_id']);
			return $product;
		},$products);	
	}

	public function getPackage(int $package_id = null)
	{
		if(isset($package_id) === true)
		{
			if($package = $this->_getPackage($package_id))
			{
				$package['products'] = json_decode($package['product_ids'],true);
				$package['products'] = self::getProducts($package['products']);

				return $package;
			}
		}

		return false;
	}
	
	public function _getPackage(int $package_id = null)
	{
		if(isset($package_id) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id,
						{$this->tblName}.title,
						{$this->tblName}.product_ids,
						{$this->tblName}.catalog_package_type_id,
						{$this->tblName}.amount
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.status = '".Constants::AVIABLE."'
					AND 
						{$this->tblName}.package_id = '{$package_id}'
					";
			
			return $this->connection()->row($sql);
		}

		return false;
	}
	
	public function getPremiumPackageIds()
	{
		$sql = "SELECT 
					{$this->tblName}.{$this->tblName}_id
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status = '".Constants::AVIABLE."'
				AND 
					{$this->tblName}.trial = '0'
				";
		
		return $this->connection()->column($sql);
	}

	public function getPackageIds()
	{
		$sql = "SELECT 
					{$this->tblName}.{$this->tblName}_id
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status = '".Constants::AVIABLE."'
				";
		
		return $this->connection()->column($sql);
	}

	public static function applyFilterByCatalogCampaignId(array $packages = null,int $catalog_campaign_id = null) : array
	{
		if(isset($packages) && is_array($packages) && !empty($packages))
		{
			return array_values(array_filter($packages, function($package) use($catalog_campaign_id) {
				$catalog_campaign_ids = json_decode($package['catalog_campaign_ids'],true);

				return in_array($catalog_campaign_id,$catalog_campaign_ids);
			}));
		}

		return [];
	}

	public function getAll(string $filter = '')
	{
		if($packages = $this->_getAll($filter))
		{
			return array_map(function($package){
				$package['products'] = json_decode($package['product_ids'],true);
				$package['products'] = self::getProducts($package['products']);

				return $package;
			},$packages);
		}
	}
	
	public function _getAll(string $filter = '')
	{
		$sql = "SELECT 
					{$this->tblName}.{$this->tblName}_id,
					{$this->tblName}.title,
					{$this->tblName}.catalog_campaign_ids,
					{$this->tblName}.description,
					{$this->tblName}.product_ids,
					{$this->tblName}.image,
					{$this->tblName}.amount,
					{$this->tblName}.status
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status != '".Constants::DELETE."'
					{$filter}
				";
		
		return $this->connection()->rows($sql);
	}
}