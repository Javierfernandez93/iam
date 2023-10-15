<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Util;

use DummieTrading\LoanPerUser;
use DummieTrading\Balance;

class PaymentPerLoan extends Orm {
	protected $tblName = 'payment_per_loan';
	public static $APPROVED = 1;
	public static $VERIFIED = 1;
	public static $PENDING_FOR_APPROVED = 0;
	public static $PAYMENT_DONE = 2;
	public function __construct() {
		parent::__construct();
	}

	public function getPaymentText($status = null)
	{
		if($status == self::$APPROVED)
		{
			return "Aprobado";
		} else if($status == self::$PENDING_FOR_APPROVED) {
			return "Pendiente de aprobación";
		}
	}

	public function getNextPayment($loan_per_user_id = null)
	{
		$loan = $this->getLastPayment($loan_per_user_id);

		if(!$loan)
		{
			$LoanPerUser = new LoanPerUser;

			$loan = $LoanPerUser->getLoan($loan_per_user_id);
		}

		if($loan)
		{
			if($this->hasPaymentDone($loan))
			{
				$loan['payment_done'] = self::$PAYMENT_DONE;

				return $loan;
			}

			if($this->_isAbleToNextPayment($loan['create_date'],$loan['period']))
			{
				return strtotime("next week next saturday",$loan['create_date']);
			}
		}

		return false;
	}

	public function getFirstPayments($loans = null)
	{	
		if($loans)
		{
			return array_filter($loans, function($loan) {
				return $loan['ammount_of_payments'] == 1 && $loan['payment_done'] == self::$PAYMENT_DONE;
			});
		}
	}
	
	public function _getFirstPayments($loans = null)
	{	
		if($loans)
		{
			return array_filter($loans, function($loan) {
				return $loan['period'] == 1;
			});
		}
	}

	public function hasPaymentDone($loan = null)
	{
		$first_day_of_week = strtotime("monday this week");
		$last_day_of_week = strtotime("sunday this week");
		
		return $loan['create_date'] >= $first_day_of_week && $loan['create_date'] <= $last_day_of_week;
	}

	public function _isAbleToNextPayment($create_date = null,$period = null)
	{
		return strtotime("+1".$period,$create_date) > time();
	}

	public function approbePayments($user_support_id = null,$loans = null) : bool
	{
		if(isset($loans) === true)
		{
			foreach($loans as $loan)
			{
				$PaymentPerLoan = new PaymentPerLoan;

				if($PaymentPerLoan->cargarDonde("payment_per_loan_id = ? AND status = ?",[$loan['payment_per_loan_id'],$loan['status']]))
				{
					$PaymentPerLoan->user_support_id = $user_support_id;
					$PaymentPerLoan->approved_date = time();
					$PaymentPerLoan->status = self::$APPROVED;
					$PaymentPerLoan->verified = self::$VERIFIED;
					
					if($PaymentPerLoan->save())
					{
						$Balance = new Balance;
			
						$Balance->add($user_support_id,$PaymentPerLoan->ammount,"PAGO #{$PaymentPerLoan->period} EXPEDIENTE #{$this->getUniqueId($PaymentPerLoan->getId())}",null,$PaymentPerLoan->getId());
					}
				}
			}

			return true;
		}

		return false;
	}

	public function getUniqueId($payment_per_loan_id = null)
	{
		if (isset($payment_per_loan_id) === true)  
		{
			$sql = "SELECT 
						loan_per_user.unique_id
					FROM 
						{$this->tblName}
					LEFT JOIN 	
						loan_per_user
					ON 
						loan_per_user.loan_per_user_id = {$this->tblName}.loan_per_user_id
					WHERE 
						{$this->tblName}.payment_per_loan_id = '{$payment_per_loan_id}'
					";
	
			return $this->connection()->field($sql);
		}

		return false;
	}

	public function getAmmountOfPayments($loan_per_user_id = null)
	{
		if (isset($loan_per_user_id) === true)  
		{
			$sql = "SELECT 
						COUNT({$this->tblName}.{$this->tblName}_id) as c
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.loan_per_user_id = '{$loan_per_user_id}'
					AND 
						{$this->tblName}.status = '1'
					";
			
			return $this->connection()->field($sql);
		}

		return false;
	}

	public function hasPaymentPendingForVerified($loan_per_user_id = null)
	{
		if (isset($loan_per_user_id) === true)  
		{
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.loan_per_user_id = '{$loan_per_user_id}'
					AND 
						{$this->tblName}.status = '0'
					AND 
						{$this->tblName}.verified = '0'
					ORDER BY 
						{$this->tblName}.create_date
					DESC
					";
	
			return $this->connection()->field($sql) ? true : false;
		}

		return false;
	}
	
	public function getLastPayment($loan_per_user_id = null)
	{
		if (isset($loan_per_user_id) === true)  
		{
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id,
						{$this->tblName}.ammount,
						{$this->tblName}.period as payments,
						{$this->tblName}.verified,
						{$this->tblName}.create_date,
						FROM_UNIXTIME({$this->tblName}.create_date) as cd,
						catalog_loan.number_of_period,
						catalog_period.period
					FROM 
						{$this->tblName}
					LEFT JOIN 
						loan_per_user
					ON 
						loan_per_user.loan_per_user_id = {$this->tblName}.loan_per_user_id
					LEFT JOIN 
						catalog_loan
					ON 
						catalog_loan.catalog_loan_id = loan_per_user.catalog_loan_id
					LEFT JOIN 
						catalog_period
					ON 
						catalog_period.catalog_period_id = catalog_loan.catalog_period_id
					WHERE 
						{$this->tblName}.loan_per_user_id = '{$loan_per_user_id}'
					AND 
						{$this->tblName}.status = '1'
					ORDER BY 
						{$this->tblName}.create_date 
					DESC 
					";
			
			return $this->connection()->row($sql);
		}

		return false;
	}

	public function getDue($loan_per_user_id = null,$catalog_loan_id = null)
	{
		if (isset($loan_per_user_id,$catalog_loan_id) === true) 
		{
			$CatalogLoan = new CatalogLoan;
			$loan_ammount = $CatalogLoan->getLoan($catalog_loan_id);
			$payments = $this->getAllPaymentsAmmount($loan_per_user_id);

			return $payments > 0 ? $loan_ammount - $payments : $loan_ammount;
		}

		return false;
	}

	public function getAllPaymentsAmmount($loan_per_user_id = null)
	{
		if (isset($loan_per_user_id) === true)  
		{
			$sql = "SELECT 
						SUM({$this->tblName}.ammount) as ammount
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.loan_per_user_id = '{$loan_per_user_id}'
					AND 
						{$this->tblName}.status = '".self::$APPROVED."'
					";

			return $this->connection()->field($sql);
		}

		return false;
	}

	public function getAll($loan_per_user_id = null)
	{
		if (isset($loan_per_user_id) === true)  
		{
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id,
						{$this->tblName}.ammount,
						{$this->tblName}.period as payments,
						{$this->tblName}.payment_reference,
						{$this->tblName}.image,
						{$this->tblName}.status,
						{$this->tblName}.create_date,
						catalog_payment_method.payment_method,
						catalog_loan.number_of_period,
						catalog_period.period
					FROM 
						{$this->tblName}
					LEFT JOIN 
						loan_per_user
					ON 
						loan_per_user.loan_per_user_id = {$this->tblName}.loan_per_user_id
					LEFT JOIN 
						catalog_loan
					ON 
						catalog_loan.catalog_loan_id = loan_per_user.catalog_loan_id
					LEFT JOIN 
						catalog_period
					ON 
						catalog_period.catalog_period_id = catalog_loan.catalog_period_id
					LEFT JOIN 
						catalog_payment_method
					ON 
						catalog_payment_method.catalog_payment_method_id = {$this->tblName}.catalog_payment_method_id
					WHERE 
						{$this->tblName}.loan_per_user_id = '{$loan_per_user_id}'
					AND 
						{$this->tblName}.status != -1
					";

			return $this->connection()->rows($sql);
		}

		return false;
	}
}