<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Util;

use DummieTrading\ProfitPerUser;
use DummieTrading\Transaction;

class GainPerBroker extends Orm {
	protected $tblName = 'gain_per_broker';

    const LIMIT = 19;

	public function __construct() {
		parent::__construct();
	}

	public function getGainPerDay(int $broker_id = null,string $day = null)
	{
        if(isset($broker_id) === true)
        {
            $begin_of_day = strtotime(date("Y-m-d 00:00:00",strtotime($day)));
            $end_of_day = strtotime(date("Y-m-d 23:59:59",strtotime($day)));
            
            $sql = "SELECT 
                        {$this->tblName}.gain
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.status = '1'
                    AND 
                        {$this->tblName}.create_date
                    BETWEEN 
                        {$begin_of_day}
                    AND 
                        {$end_of_day}
                    AND 
                        {$this->tblName}.broker_id = '{$broker_id}'
                    ";
                    
            return $this->connection()->field($sql);
        }

        return false;
	}
	
    public function getGainsPerDays()
	{
        $first_day = strtotime("-".self::LIMIT." days");
        $ProfitPerUser = new ProfitPerUser;
        
        for($i = 1; $i <= self::LIMIT; $i++) 
        {
            $unix_day = strtotime("+{$i} days", $first_day);

            if(date('N',$unix_day) < 6)
            {
                $day = date("Y-m-d 00:00:00",$unix_day);

                $_gains = $this->addRealGain($this->_getGainsPerDay($day));

                $profit_investment = $ProfitPerUser->getAllProfitsPerDaySum($day,Transaction::INVESTMENT);
                $profit_referral_investment = $ProfitPerUser->getAllProfitsPerDaySum($day,Transaction::REFERRAL_INVESTMENT);
                $total_gain = $_gains ? array_sum(array_column($_gains,'gain')) : 0;
                $total_real_gain = $_gains ? array_sum(array_column($_gains,'real_gain')) : 0;
                $total_profit = $profit_investment + $profit_referral_investment;

                $gains[] = [
                    'day' => $day,
                    'unix_day' => $unix_day,
                    'profit_investment' => $profit_investment,
                    'profit_referral_investment' => $profit_referral_investment,
                    'total_profit' => $total_profit, 
                    'total_gain' => $total_gain,
                    'total_real_gain' => $total_real_gain,
                    'total_earn' => $total_real_gain - $total_profit,
                    'gains' => $_gains,
                    'detail' => true
                ];
            }
        }
        
        return $gains; 
	}

    public function addRealGain($gains = null)
    {
        if(isset($gains) === true && empty($gains) === false)
        {
            foreach($gains as $key => $gain)
            {
                $gains[$key]['real_gain'] = $gain['fee'] ? Util::getPercentaje($gain['gain'],$gain['fee']) * 100 : $gain['gain'];
            }
        }

        return $gains;
    }

    public function getGainsPerDay(string $day = null,bool $include_fee = true)
    {
        $result = 0;

        if($gains_per_day = $this->_getGainsPerDay($day))
        {
            $result = array_reduce($gains_per_day, function($carry,$item) use ($include_fee) {
                if($include_fee === true)
                {
                    $carry += $item['fee'] > 0 ? $item['gain'] * $item['fee'] : $item['gain']; 
                } else {
                    $carry += $item['gain']; 
                }

                return $carry;
            });
        }

        return $result;
    }

    public function _getGainsPerDay(string $day = null)
	{
        if(isset($day) === true)
        {
            $begin_of_day = strtotime(date("Y-m-d 00:00:00",strtotime($day)));
            $end_of_day = strtotime(date("Y-m-d 23:59:59",strtotime($day)));
            
            $sql = "SELECT
                        {$this->tblName}.broker_id, 
                        SUM({$this->tblName}.gain) as gain,
                        broker.name,
                        broker.fee
                    FROM 
                        {$this->tblName}
                    LEFT JOIN
                        broker 
                    ON 
                        broker.broker_id = {$this->tblName}.broker_id
                    WHERE 
                        {$this->tblName}.status = '1'
                    AND 
                        {$this->tblName}.create_date
                    BETWEEN 
                        {$begin_of_day}
                    AND 
                        {$end_of_day}
                    GROUP BY 
                        {$this->tblName}.broker_id
                    ";
                    
            if($gains = $this->connection()->rows($sql))
            {
                return $gains;
            }
        }

        return false;
	}
    
    public function getAllGains()
	{
        $result = 0;

        if($gains_per_day = $this->_getAllGains())
        {
            $result = array_reduce($gains_per_day, function($carry,$gain){
                $carry += $gain['fee'] > 0 ? $gain['gain'] * $gain['fee'] : $gain['gain']; 

                return $carry;
            });
        }

        return $result;
	}
    
    public function _getAllGains()
	{
        $sql = "SELECT 
                    SUM({$this->tblName}.gain) as gain,
                    broker.broker_id,
                    broker.fee
                FROM 
                    {$this->tblName}
                LEFT JOIN   
                    broker 
                ON 
                    broker.broker_id = {$this->tblName}.broker_id
                WHERE 
                    {$this->tblName}.status = '1'
                GROUP BY 
                    {$this->tblName}.broker_id
                ";
                
        return $this->connection()->rows($sql);
	}
	
    public static function addGain(int $broker_id = null,float $gain = null,string $day = null) : bool
    {
        $GainPerBroker = new GainPerBroker;
            
        if($gain_per_broker_id = $GainPerBroker->getGainPerDayId($broker_id,$day))
        {
            $GainPerBroker->cargarDonde("gain_per_broker_id = ?",$gain_per_broker_id);
        } else {
            $GainPerBroker->broker_id = $broker_id;
            $GainPerBroker->create_date = strtotime(date("Y-m-d 23:59:59",strtotime($day)));
        }

        $GainPerBroker->gain = $gain;

        return $GainPerBroker->save();
    }

    public function getGainPerDayId(int $broker_id = null,string $day = null)
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
                        {$this->tblName}.status = '1'
                    AND 
                        {$this->tblName}.create_date
                    BETWEEN 
                        {$begin_of_day}
                    AND 
                        {$end_of_day}

                    AND 
                        {$this->tblName}.broker_id = '{$broker_id}'
                    ";
            
            return $this->connection()->field($sql);
        }

        return false;
	}
    
    public function getAllGainsPerBroker(int $broker_id = null)
	{
        if(isset($broker_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.gain
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.status = '1'
                    AND 
                        {$this->tblName}.broker_id = '{$broker_id}'
                    ";
            
            return $this->connection()->column($sql);
        }

        return false;
	}
}