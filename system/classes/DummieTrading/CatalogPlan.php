<?php

namespace DummieTrading;

use HCStudio\Orm;

class CatalogPlan extends Orm {
	const MAX_PROFIT = 12;
	protected $tblName = 'catalog_plan';
	public function __construct() {
		parent::__construct();
	}

	public function getAll()
	{
		$sql = "SELECT 
					{$this->tblName}.{$this->tblName}_id,
					{$this->tblName}.name,
					{$this->tblName}.profit
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status = '1'
				";
		
		return $this->connection()->rows($sql);
	}
	
	public function isAviableProfit(int $catalog_plan_id = null,$additional_profit = null,$sponsor_profit = null)
	{
		$profit = $this->getProfit($catalog_plan_id);

		return $profit + $additional_profit + $sponsor_profit <= self::MAX_PROFIT;
	}

	public function getProfit(int $catalog_plan_id = null)
	{
		if(isset($catalog_plan_id) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.profit
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.catalog_plan_id = '{$catalog_plan_id}'
					AND 
						{$this->tblName}.status = '1'
					";
			
			return $this->connection()->field($sql);
		}

		return false;
	}
	
	public function getCatalogPlanIdBetween(int $ammount = null)
	{
		if(isset($ammount) === true)
		{
			$catalog_plan_id = 0;

			if($catalog_plans = $this->getAll())
			{
				if($ammount >= $catalog_plans[0]['name'])
				{
					foreach($catalog_plans as $key => $catalog_plan)
					{
						$nextVal = $$catalog_plans[$key+1] != null ? $catalog_plans[$key+1]['name'] : INF;
		
						if ($ammount >= $catalog_plan['name'] && $ammount < $nextVal) 
						{
							$catalog_plan_id = $catalog_plan['catalog_plan_id'];
						}
					}
				} else {
					$catalog_plan_id = $catalog_plans[0]['catalog_plan_id'];
				}
			}

			return $catalog_plan_id;
		}

		return false;
	}
}