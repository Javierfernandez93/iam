<?php

namespace DummieTrading;

use HCStudio\Orm;

class Balance extends Orm {
	protected $tblName = 'balance';
	public static $DELETED = -1;
	public static $ACTIVE = 1;

	public static $REST = -1;
	public static $ADD = 1;
	public function __construct() {
		parent::__construct();
	}

	public function getStats($filter = null) 
	{
		$sql = "SELECT 
					SUM({$this->tblName}.ammount) as balance,
					FROM_UNIXTIME({$this->tblName}.create_date, '%Y/%m/%d') as create_date
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status = '1'
					{$filter}
				GROUP BY 
					DAY(FROM_UNIXTIME({$this->tblName}.create_date))
				ORDER BY 
					{$this->tblName}.create_date
				";
				
			
		return $this->connection()->rows($sql);
	}

	public function getAllBalance()
	{
		$sql = "SELECT 
					{$this->tblName}.{$this->tblName}_id,
					{$this->tblName}.user_support_id,
					{$this->tblName}.create_date,
					{$this->tblName}.description,
					{$this->tblName}.ammount,
					LOWER(CONCAT_WS(' ',user_support.names,user_support.last_name,user_support.sur_name)) as names
				FROM 
					{$this->tblName}
				LEFT JOIN 
					user_support
				ON 
					user_support.user_support_id = {$this->tblName}.user_support_id
				WHERE 
					{$this->tblName}.status = '1'
				ORDER BY 
					{$this->tblName}.create_date
				DESC 
				";
		
		return $this->connection()->rows($sql);
	}

	public function getBalance($filter = "")
	{
		$sql = "SELECT 
					SUM({$this->tblName}.ammount) as balance
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status = '1'
					{$filter}
				";
		
		return $this->connection()->field($sql);
	}
	
	public function getInFlow($filter = "")
	{
		$sql = "SELECT 
					SUM({$this->tblName}.ammount) as balance
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.ammount > '0'
				AND 
					{$this->tblName}.status = '1'
					{$filter}
				";
		
		return $this->connection()->field($sql);
	}

	public function getOuts($filter = "")
	{
		$sql = "SELECT 
					SUM({$this->tblName}.ammount) as balance
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.ammount < '0'
				AND 
					{$this->tblName}.status = '1'
					{$filter}
				";
		
		return $this->connection()->field($sql);
	}

	public function addBalance($user_support_id = null,$ammount = null,$description = null,$loan_per_user_id = null,$payment_per_loan_id = null,$kind = null)
	{
		if($kind == self::$ADD) {
			$ammount = abs($ammount);
		} else if($kind == self::$REST) {
			$ammount = -abs($ammount);
		}

		$Balance = new Balance;
		$Balance->user_support_id = $user_support_id;
		$Balance->ammount = $ammount;
		$Balance->description = $description;
		$Balance->loan_per_user_id = isset($loan_per_user_id) ? $loan_per_user_id : 0;
		$Balance->payment_per_loan_id = isset($payment_per_loan_id) ? $payment_per_loan_id : 0;
		$Balance->create_date = time();
		
		return $Balance->save();
	}	

	public function rest($user_support_id = null,$ammount = null,$description = null,$loan_per_user_id = null,$payment_per_loan_id = null)
	{
		return $this->addBalance($user_support_id,$ammount,$description,$loan_per_user_id,$payment_per_loan_id,self::$REST);
	}

	public function add($user_support_id = null,$ammount = null,$description = null,$loan_per_user_id = null,$payment_per_loan_id = null)
	{
		return $this->addBalance($user_support_id,$ammount,$description,$loan_per_user_id,$payment_per_loan_id,self::$ADD);
	}
}