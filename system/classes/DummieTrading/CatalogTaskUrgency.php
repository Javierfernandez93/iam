<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Util;

class CatalogTaskUrgency extends Orm {
	protected $tblName = 'catalog_task_urgency';
	public static $LOW = 1;
	public static $MIDDLE = 2;
	public static $HIGH = 3;
	public function __construct() {
		parent::__construct();
	}

	public function getPriorityClass($catalog_task_urgency_id = null) {
		if($catalog_task_urgency_id == self::$LOW)
		{
			return "bg-success";
		} else if($catalog_task_urgency_id == self::$MIDDLE) {
			return "bg-warning";
		} else if($catalog_task_urgency_id == self::$HIGH) {
			return "bg-danger";
		}
	}
	
	public function getAll($catalog_signs = null,$gender = null) {
		if(isset($catalog_signs,$gender) === true)
		{
			foreach ($catalog_signs as $key => $catalog_sign) {
				$catalog_signs[$key]['skype'] = 0;
				$catalog_signs[$key]['value'] = 0;

				if($catalog_sign['gender'] != 0)
				{
					if($catalog_sign['gender'] === $gender) {
						$catalog_signs[$key]['skype'] = 0;
						$catalog_signs[$key]['value'] = 0;
					} else {
						$catalog_signs[$key]['skype'] = 1;
						$catalog_signs[$key]['value'] = 0;
					}
				}
			}
		}

		return $catalog_signs;
	}

	public function getAllList() {
		$sql = "SELECT 
					{$this->tblName}.{$this->tblName}_id,
					{$this->tblName}.{$this->tblName}
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status = '1'
					";

		return $this->connection()->rows($sql);
	}
}