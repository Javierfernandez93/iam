<?php

namespace DummieTrading;

use HCStudio\Orm;

class GainPerPartner extends Orm {
	protected $tblName = 'gain_per_partner';
	public function __construct() {
		parent::__construct();
	}

	public function getGains($user_login_id = null)
	{
        if(isset($user_login_id) === true)
        {
            $sql = "SELECT 
                        DATE(FROM_UNIXTIME({$this->tblName}.create_date)) as date,
                        SUM({$this->tblName}.ammount) as ammount
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.status = '1'
                    GROUP BY 
                        DATE(FROM_UNIXTIME({$this->tblName}.create_date)) 
                    ";
            
            return $this->connection()->rows($sql);
        }

        return false;
	}

    public function getGainsPerCatalogGain($catalog_gain_id = null)
	{
        if(isset($catalog_gain_id) === true)
        {
            $sql = "SELECT 
                        SUM({$this->tblName}.ammount) as ammount
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.status = '1'
                    AND
                        {$this->tblName}.catalog_gain_id = '{$catalog_gain_id}'
                    ";
            
            return $this->connection()->field($sql);
        }

        return false;
	}
}