<?php

namespace DummieTrading;

use HCStudio\Orm;

use DummieTrading\CatalogCurrency;

use JFStudio\Constants;

class CommissionPerUser extends Orm {
	protected $tblName = 'commission_per_user';
    
	//** status */
	const PENDING_FOR_DISPERSION = 1;
	const COMPLETED = 2;

	public function __construct() {
		parent::__construct();
	}

	public static function addCommission(array $data = null) : bool
	{
		if((new CommissionPerUser)->existCommission($data['buy_per_user_id']) == false)
		{
			return self::add($data);
		}

		return false;
	}

	public static function add(array $data = null) : bool
	{
		$CommissionPerUser = new CommissionPerUser;
		$CommissionPerUser->user_login_id = $data['user_login_id'];
		$CommissionPerUser->buy_per_user_id = $data['buy_per_user_id'];
		$CommissionPerUser->catalog_commission_type_id = $data['catalog_commission_type_id'];
		$CommissionPerUser->user_login_id_from = $data['user_login_id_from'];
		$CommissionPerUser->amount = $data['amount'];
		$CommissionPerUser->catalog_currency_id = CatalogCurrency::USD;
		$CommissionPerUser->package_id = $data['package_id'];
		$CommissionPerUser->status = self::PENDING_FOR_DISPERSION;
		$CommissionPerUser->create_date = time();
		
		return $CommissionPerUser->save();
	}

	
	public function existCommission(int $buy_per_user_id = null) : bool
	{
		if(isset($buy_per_user_id) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.buy_per_user_id = '{$buy_per_user_id}'
					AND 
						{$this->tblName}.status != '".Constants::DELETE."'
					";

			return $this->connection()->field($sql) ? true : false;
		}

		return false;
	}
	
	public static function setCommissionAsDispersed(int $commission_per_user_id,int $transaction_per_wallet_id = null)
	{
		if(isset($commission_per_user_id,$transaction_per_wallet_id) === true)
		{
			$CommissionPerUser = new CommissionPerUser;
			
			if($CommissionPerUser->loadWhere('commission_per_user_id = ?',$commission_per_user_id))
			{
				$CommissionPerUser->deposit_date = time();
				$CommissionPerUser->status = self::COMPLETED;
				$CommissionPerUser->transaction_per_wallet_id = $transaction_per_wallet_id;
				
				return $CommissionPerUser->save();
			}
		}

		return false;
	}

	public function getPendingCommissions() 
	{
		$sql = "SELECT 
					{$this->tblName}.{$this->tblName}_id,
					{$this->tblName}.user_login_id,
					{$this->tblName}.user_login_id_from,
					{$this->tblName}.amount
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status = '".self::PENDING_FOR_DISPERSION."'
				";

		return $this->connection()->rows($sql);
	}
	
	public function getAll(int $user_login_id = null)  
	{
		if(isset($user_login_id) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id,
						{$this->tblName}.user_login_id,
						{$this->tblName}.buy_per_user_id,
						{$this->tblName}.package_id,
						{$this->tblName}.catalog_currency_id,
						{$this->tblName}.deposit_date,
						{$this->tblName}.transaction_per_wallet_id,
						{$this->tblName}.user_login_id_from,
						{$this->tblName}.create_date,
						catalog_currency.currency,
						catalog_commission.name,
						catalog_commission_type.commission_type,
						user_data.names,
						{$this->tblName}.status,
						{$this->tblName}.amount
					FROM 
						{$this->tblName}
					LEFT JOIN
						catalog_currency 
					ON 
						catalog_currency.catalog_currency_id = {$this->tblName}.catalog_currency_id 
					LEFT JOIN
						catalog_commission 
					ON 
						catalog_commission.catalog_commission_id = {$this->tblName}.catalog_commission_id 
					LEFT JOIN
						catalog_commission_type 
					ON 
						catalog_commission_type.catalog_commission_type_id = catalog_commission.catalog_commission_type_id 
					LEFT JOIN
						user_data 
					ON 
						user_data.user_login_id = {$this->tblName}.user_login_id_from 
					WHERE 
						{$this->tblName}.user_login_id = '{$user_login_id}'
					AND
						{$this->tblName}.status IN (".self::PENDING_FOR_DISPERSION.",".self::COMPLETED.")
					";

			return $this->connection()->rows($sql);
		}

		return false;
	}

	public function getSum(int $user_login_id = null,string $filter = null)  
	{
		if(isset($user_login_id) === true)
		{
			$sql = "SELECT 
						SUM({$this->tblName}.amount) as amount
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.user_login_id = '{$user_login_id}'
					AND
						{$this->tblName}.status IN (".self::PENDING_FOR_DISPERSION.",".self::COMPLETED.")
						{$filter}
					";

			return $this->connection()->field($sql);
		}

		return false;
	}
	
	public function getSumFull(int $user_login_id = null,int $user_login_id_from = null)  
	{
		if(isset($user_login_id,$user_login_id_from) === true)
		{
			$sql = "SELECT 
						SUM({$this->tblName}.amount) as amount
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.user_login_id = '{$user_login_id}'
					AND
						{$this->tblName}.user_login_id_from = '{$user_login_id_from}'
					AND
						{$this->tblName}.status IN (".self::PENDING_FOR_DISPERSION.",".self::COMPLETED.")
					";

			return $this->connection()->field($sql);
		}

		return false;
	}
}