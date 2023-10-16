<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Util;
use JFStudio\Constants;

class CatalogPaymentMethod extends Orm {
	protected $tblName = 'catalog_payment_method';

	const COINPAYMENTS = 1;
	const STRIPE = 2;
	const EWALLET = 3;
	const PAYPAL = 4;
	const AIRTM = 5;
	const DEPOSIT = 6;
	const PAYMENT_GATEWAY = 7;
	const EXTERNAL = 8;
	const HOTMART = 9;
	const BANK_DATA = [
		'bank' => 'BBVA',
		'account' => '1290589058091',
		'clabe' => '123908451290589058091',
	];

	public function __construct() {
		parent::__construct();
	}

	public function getAll(string $filter = null)
	{
		$sql = "SELECT 
					{$this->tblName}.{$this->tblName}_id,
					{$this->tblName}.image,
					{$this->tblName}.description,
					{$this->tblName}.additional_info,
					{$this->tblName}.recomend,
					{$this->tblName}.additional_data,
					{$this->tblName}.status,
					{$this->tblName}.create_date,
					{$this->tblName}.catalog_currency_ids,
					{$this->tblName}.fee,
					{$this->tblName}.payment_method
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status = '".Constants::AVIABLE."'
					{$filter}
				";
				
		return $this->connection()->rows($sql);
	}
	
	public function getAllForAdmin(string $filter = null)
	{
		if($payment_methods = $this->_getAllForAdmin($filter))
		{
			return array_map(function($payment_method){
				if(Util::isJson($payment_method['additional_data']))
				{
					$payment_method['additional_data'] = json_decode($payment_method['additional_data'],true);
				}

				return $payment_method;
			},$payment_methods);
		}
	}
	
	public function _getAllForAdmin(string $filter = null)
	{
		$sql = "SELECT 
					{$this->tblName}.{$this->tblName}_id,
					{$this->tblName}.image,
					{$this->tblName}.description,
					{$this->tblName}.additional_info,
					{$this->tblName}.recomend,
					{$this->tblName}.additional_data,
					{$this->tblName}.status,
					{$this->tblName}.create_date,
					{$this->tblName}.catalog_currency_ids,
					{$this->tblName}.fee,
					{$this->tblName}.payment_method
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status != '".Constants::DELETE."'
					{$filter}
				";
				
		return $this->connection()->rows($sql);
	}

	public function getAdditionalPaymentMethodData(int $catalog_payment_method_id = null)
	{
		if(!isset($catalog_payment_method_id))
		{
			return false;
		}

		if($additional_data = $this->getAdditionalData($catalog_payment_method_id))
		{
			if(Util::isJson($additional_data))
			{
				$additional_data = json_decode($additional_data,true);
			}

			return $additional_data;
		}
	}

	public function get(int $catalog_payment_method_id = null)
	{
		if(isset($catalog_payment_method_id) == true)
		{
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id,
						{$this->tblName}.payment_method,
						{$this->tblName}.additional_data,
						{$this->tblName}.image
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.catalog_payment_method_id = '{$catalog_payment_method_id}'
					AND 
						{$this->tblName}.status != '".Constants::DELETE."'
					";
			
			return $this->connection()->row($sql);
		}

		return false;
	}

	public function getAdditionalData(int $catalog_payment_method_id = null)
	{
		if(isset($catalog_payment_method_id) == true)
		{
			$sql = "SELECT 
						{$this->tblName}.additional_data
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.catalog_payment_method_id = '{$catalog_payment_method_id}'
					AND 
						{$this->tblName}.status != '".Constants::DELETE."'
					";
			
			return $this->connection()->field($sql);
		}

		return false;
	}

	public function getFee(int $catalog_payment_method_id = null) : float
	{
		if(isset($catalog_payment_method_id) == true)
		{
			$sql = "SELECT 
						{$this->tblName}.fee
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.catalog_payment_method_id = '{$catalog_payment_method_id}'
					AND 
						{$this->tblName}.status = '1'
					";
			
			return $this->connection()->field($sql);
		}

		return 0;
	}
	
	public function getFeePaymentMethod(int $catalog_payment_method_id = null) 
	{
		if(isset($catalog_payment_method_id) == true)
		{
			$sql = "SELECT 
						{$this->tblName}.payment_method
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.catalog_payment_method_id = '{$catalog_payment_method_id}'
					AND 
						{$this->tblName}.status = '1'
					";
			
			return $this->connection()->field($sql);
		}

		return 0;
	}
}