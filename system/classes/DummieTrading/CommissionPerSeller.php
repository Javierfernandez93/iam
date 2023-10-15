<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Util;

use DummieTrading\CommissionAmmountPerSeller;
use DummieTrading\CatalogCommission;
use DummieTrading\LoanPerUser;

class CommissionPerSeller extends Orm {
	protected $tblName = 'commission_per_seller';
    
	public function __construct() {
		parent::__construct();
	}
	
	public function calculateCommsissions($user_support_id = null,$start_date = null,$end_date = null,$save = false)  
	{
		if(isset($user_support_id,$save) === true)
		{
			$LoanPerUser = new LoanPerUser;
			
			if($loans = $LoanPerUser->getAllLoansSellers($start_date,$end_date))
			{
				$CommissionAmmountPerSeller = new CommissionAmmountPerSeller;

				$_loans = array_filter($loans, function($loan) {
					return $loan['period'] == 2;
				});

				if($colocation_commission = $CommissionAmmountPerSeller->get($user_support_id,CatalogCommission::$COLOCATION))
				{
					if($this->setCommissionsForColocation($user_support_id,$_loans,$colocation_commission,$start_date,$end_date))
					{

					}
				}

				/* commissions by colocation (5%) [OK] */
				if($colocation_commission = $CommissionAmmountPerSeller->get($user_support_id,CatalogCommission::$AMMOUNT))
				{
					if($this->setCommissionsForAmmount($user_support_id,$_loans,$colocation_commission,$start_date,$end_date))
					{

					}
				}

				if($seller_loans = $LoanPerUser->getAllLoansBySeller($user_support_id,$start_date,$end_date))
				{
					if($week_commission = $CommissionAmmountPerSeller->get($user_support_id,CatalogCommission::$WEEK))
					{
						$week_loans = array_filter($seller_loans, function($loan) {
							return in_array($loan['period'],[2,7,14]);
						});

						if($this->setCommissionsForWeek($user_support_id,$week_loans,$week_commission,$start_date,$end_date))
						{

						}
					}
				}
			}
		}	
	}

	public function setCommissionsForWeek(int $user_support_id = null,array $loans = null,array $colocation_commission = null,$start_date = null,$end_date = null)
	{
		if(isset($loans) === true && empty($loans) == false)
		{
			foreach($loans as $loan)
			{
				$loan['commissionable_week'] = $loan['commissionable_week'] == 1 ? 2 : $loan['commissionable_week'];
				
				if($loan['commissionable_week'] == $loan['period'])
				{
					if($this->existCommission($user_support_id,$loan['payment_per_loan_id'],$start_date,$end_date,CatalogCommission::$WEEK) == false)
					{
						$CommissionPerSeller = new CommissionPerSeller;

						$CommissionPerSeller->user_support_id = $user_support_id;
						$CommissionPerSeller->payment_per_loan_id = $loan['payment_per_loan_id'];
						$CommissionPerSeller->catalog_commission_id = CatalogCommission::$WEEK;
						$CommissionPerSeller->ammount = $loan['ammount'];
						$CommissionPerSeller->create_date = strtotime("this saturday");
						
						$CommissionPerSeller->save();
					}
				}
			}
		}
	}

	public function setCommissionsForAmmount(int $user_support_id = null,array $loans = null,array $colocation_commission = null,$start_date = null,$end_date = null)
	{
		$total = array_sum(array_column($loans,"capital"));
		$total = (int)($total / 1000);
		
		if($total > 0) 
		{
			$total = $total * 1000;
			
			$commission = Util::getPercentaje($total,$colocation_commission['ammount']);

			$CommissionPerSeller = new CommissionPerSeller;

			if($CommissionPerSeller->existCommission($user_support_id,0,$start_date,$end_date,CatalogCommission::$AMMOUNT) == false)
			{
				$CommissionPerSeller->user_support_id = $user_support_id;
				$CommissionPerSeller->payment_per_loan_id = 0;
				$CommissionPerSeller->catalog_commission_id = CatalogCommission::$AMMOUNT;
				$CommissionPerSeller->ammount = $commission;
				$CommissionPerSeller->create_date = strtotime("this saturday");
				
				return $CommissionPerSeller->save();
			}
		}

		return false;
	}

	public function setCommissionsForColocation(int $user_support_id = null,array $loans = null,array $colocation_commission = null,$start_date = null,$end_date = null)
	{
		if(isset($user_support_id,$loans,$colocation_commission) === true)
		{
			foreach($loans as $loan)
			{
				$CommissionPerSeller = new CommissionPerSeller;

				if($CommissionPerSeller->existCommission($user_support_id,$loan['payment_per_loan_id'],$start_date,$end_date,CatalogCommission::$COLOCATION) == false)
				{
					$CommissionPerSeller->user_support_id = $user_support_id;
					$CommissionPerSeller->payment_per_loan_id = $loan['payment_per_loan_id'];
					$CommissionPerSeller->ammount = $colocation_commission['ammount'];
					$CommissionPerSeller->catalog_commission_id = CatalogCommission::$COLOCATION;
					$CommissionPerSeller->create_date = strtotime("this saturday");
					
					$CommissionPerSeller->save();
				}
			}

			return true;
		}

		return false;
	}

	public function existCommission(int $user_support_id = null,int $payment_per_loan_id = null,$start_date = null,$end_date = null,$catalog_commission_id = null)
	{
		$sql = "SELECT
					{$this->tblName}.{$this->tblName}_id
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.user_support_id = '{$user_support_id}'
				AND 
					{$this->tblName}.payment_per_loan_id = '{$payment_per_loan_id}'
				AND 
					{$this->tblName}.catalog_commission_id = '{$catalog_commission_id}'
				AND 
					{$this->tblName}.status = '1'
				AND 
					{$this->tblName}.create_date
				BETWEEN
					'{$start_date}'
				AND 
					'{$end_date}'
				";
				
		return $this->connection()->field($sql) ? true : false;
	}

	public function getCommissionsBySeller(int $user_support_id = null,$start_date = null,$end_date = null)
	{
		if(isset($user_support_id,$start_date,$end_date) === true)
		{
			$sql = "SELECT
						SUM({$this->tblName}.ammount) as ammount
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.user_support_id = '{$user_support_id}'
					AND 
						{$this->tblName}.status = '1'
					AND 
						{$this->tblName}.create_date
					BETWEEN
						'{$start_date}'
					AND 
						'{$end_date}'
					";
					
			return $this->connection()->field($sql);
		}

		return false;
	}

	public function getAllCommissionsBySellers($start_date = null,$end_date = null)
	{
		if(isset($start_date,$end_date) === true)
		{
			$sql = "SELECT
						SUM({$this->tblName}.ammount) as ammount
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.status = '1'
					AND 
						{$this->tblName}.create_date
					BETWEEN
						'{$start_date}'
					AND 
						'{$end_date}'
					";
					
			return $this->connection()->field($sql);
		}

		return false;
	}

	public function getAll($start_date = null,$end_date = null) 
	{
		$filter = isset($start_date,$end_date) === true ? "AND {$this->tblName}.create_date BETWEEN '{$start_date}' AND '{$end_date}'" : "";

		$sql = "SELECT
					{$this->tblName}.{$this->tblName}_id,
					{$this->tblName}.ammount,
					{$this->tblName}.create_date,
					payment_per_loan.payment_per_loan_id,
					loan_per_user.unique_id,
					catalog_commission.commission,
					LOWER(CONCAT_WS(' ',user_support.names,user_support.last_name,user_support.sur_name)) as names
				FROM 
					{$this->tblName}
				LEFT JOIN 
					payment_per_loan
				ON 
					payment_per_loan.payment_per_loan_id = {$this->tblName}.payment_per_loan_id
				LEFT JOIN 
					catalog_commission
				ON 
					catalog_commission.catalog_commission_id = {$this->tblName}.catalog_commission_id
				LEFT JOIN 
					loan_per_user
				ON 
					loan_per_user.loan_per_user_id = payment_per_loan.loan_per_user_id
				LEFT JOIN 
					user_support
				ON 
					user_support.user_support_id = {$this->tblName}.user_support_id
				WHERE 
					{$this->tblName}.status = '1'
					{$filter}
				ORDER BY 
					loan_per_user.unique_id
				DESC 
				";
				
		return $this->connection()->rows($sql);
	}
}