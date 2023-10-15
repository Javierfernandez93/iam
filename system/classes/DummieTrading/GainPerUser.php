<?php

namespace DummieTrading;

use HCStudio\Orm;

class GainPerUser extends Orm {
  protected $tblName  = 'gain_per_user';

  const PENDING = 0;
  const DEPOSITED = 1;
  public function __construct() {
    parent::__construct();
  }

  public static function add(array $data = null) : bool
  {
    if(isset($data) === true)
    {
      $GainPerUser = new GainPerUser;  
      $GainPerUser->amount = $data['amount'] ?? 0;  
      $GainPerUser->user_login_id = $data['user_login_id'] ?? 0;  
      $GainPerUser->transaction_per_wallet_id = $data['transaction_per_wallet_id'] ?? 0;  
      $GainPerUser->create_date = time();
      
      return $GainPerUser->save();
    }

    return false;
  }

  public static function getGainsChart(int $user_login_id = null) : array
  {
    if(isset($user_login_id) === true)
    {
        if($gains = (new GainPerUser)->getAll($user_login_id))
        {
            return $gains;
        }
    }

    return [];
  }

  public function hasGainOnWeek(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true)
    {
        $start = strtotime('monday this week');
        $end = strtotime(date("Y-m-d 23:59:59",strtotime('sunday this week')));
        
        $sql = "SELECT
                  {$this->tblName}.{$this->tblName}_id
                FROM
                  {$this->tblName}
                WHERE 
                  {$this->tblName}.user_login_id = '{$user_login_id}'
                AND 
                  {$this->tblName}.create_date
                BETWEEN 
                  '{$start}'
                AND 
                  '{$end}'
                ";
                
        return $this->connection()->field($sql) ? true : false;
    }

    return false;
  }
  
  public function getGainOnWeek(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true)
    {
        $start = strtotime('monday this week');
        $end = strtotime(date("Y-m-d 23:59:59",strtotime('sunday this week')));
        
        $sql = "SELECT
                  {$this->tblName}.amount
                FROM
                  {$this->tblName}
                WHERE 
                  {$this->tblName}.user_login_id = '{$user_login_id}'
                AND 
                  {$this->tblName}.create_date
                BETWEEN 
                  {$start}
                AND 
                  {$end}
                ";
                
        return $this->connection()->field($sql);
    }

    return false;
  }

  public function getAll(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true)
    {
        $sql = "SELECT
                  {$this->tblName}.{$this->tblName}_id,
                  WEEK(from_unixtime({$this->tblName}.create_date)) as week,
                  {$this->tblName}.status,
                  {$this->tblName}.create_date,
                  SUM({$this->tblName}.amount) as amount
                FROM
                  {$this->tblName}
                WHERE 
                  {$this->tblName}.user_login_id = '{$user_login_id}'
                GROUP BY 
                    week
                ";
                
        return $this->connection()->rows($sql);
    }

    return false;
  }
}