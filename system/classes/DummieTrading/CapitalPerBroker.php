<?php

namespace DummieTrading;

use HCStudio\Orm;

class CapitalPerBroker extends Orm {
    const DELETED = -1;
    const BROKER_GAIN = 1;
    const CAPITAL = 2;
	protected $tblName = 'capital_per_broker';
	public function __construct() {
		parent::__construct();
	}

	public function getAll(int $broker_id = null,string $day = null)
	{
        if(isset($broker_id) === true)
        {
            $last_minute = isset($day) ? strtotime(date("Y-m-d 23:59:59",strtotime($day))) : time();
            
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.capital,
                        {$this->tblName}.create_date,
                        FROM_UNIXTIME({$this->tblName}.create_date) as c
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.status = '1'
                    AND 
                        {$this->tblName}.broker_id = '{$broker_id}'
                    AND 
                        {$this->tblName}.create_date <= '{$last_minute}'
                    ORDER BY
                        {$this->tblName}.create_date 
                    DESC 
                    ";
            
            return $this->connection()->rows($sql);
        }

        return false;
	}
	
    public function getAllPerBroker(int $broker_id = null)
	{
        if(isset($broker_id) === true)
        {            
            $sql = "SELECT 
                        {$this->tblName}.capital
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.status = '1'
                    AND 
                        {$this->tblName}.broker_id = '{$broker_id}'
                    ORDER BY
                        {$this->tblName}.create_date 
                    DESC 
                    ";
            
            return $this->connection()->column($sql);
        }

        return false;
	}
	
    public static function addCapital(int $broker_id = null,float $capital = null,string $day = null,int $kind = null) : bool
    {
        $CapitalPerBroker = new CapitalPerBroker;
        
        if($capital_per_broker_id = $CapitalPerBroker->getTodayCapital($broker_id,$day,$kind))
        {
            $CapitalPerBroker->cargarDonde("capital_per_broker_id = ? AND kind = ?",[$capital_per_broker_id,$kind]);
        } else {
            $CapitalPerBroker->broker_id = $broker_id;
            $CapitalPerBroker->create_date = isset($day) ? strtotime($day) : time();
        }
        
        $CapitalPerBroker->kind = $kind;
        $CapitalPerBroker->capital = $capital;

        return $CapitalPerBroker->save();
    }

    public function getTodayCapital(int $broker_id = null,string $day = null,int $kind = null)
	{
        if(isset($broker_id) === true)
        {
            $begin_of_day = strtotime(date("Y-m-d 00:00:00",strtotime($day)));
            $end_of_day = strtotime(date("Y-m-d 23:59:59",strtotime($day)));

            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.broker_id = '{$broker_id}'
                    AND 
                        {$this->tblName}.status = '1'
                    AND 
                        {$this->tblName}.kind = '{$kind}'
                    AND 
                        {$this->tblName}.create_date
                    BETWEEN 
                        {$begin_of_day}
                    AND 
                        {$end_of_day}
                    ";
            
            return $this->connection()->field($sql);
        }

        return false;
	}
    
    public function getLastCapitals(int $broker_id = null,int $start_date = null,int $end_date = null)
    {
        if(isset($broker_id) === true)
        {
            $sql = "SELECT 
                        SUM({$this->tblName}.capital) as c
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.broker_id = '{$broker_id}'
                    AND 
                        {$this->tblName}.create_date 
                    BETWEEN
                        {$start_date}
                    AND 
                        {$end_date}
                    AND 
                        {$this->tblName}.status = '1'
                    ";

            return $this->connection()->field($sql);
        }

        return false;
    }

    public function getTotalCapital()
	{
        $sql = "SELECT 
                    SUM({$this->tblName}.capital) as c
                FROM 
                    {$this->tblName}
                WHERE 
                    {$this->tblName}.status = '1'
                ";
        
        return $this->connection()->field($sql);
	}
}