<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Util;

use DummieTrading\UserPlan;
use DummieTrading\UserWallet;

class ProfitPerUser extends Orm
{
  protected $tblName  = 'profit_per_user';

  public function __construct()
  {
    parent::__construct();
  }

  public function getProfits($user_login_id = null)
  {
    if (isset($user_login_id) === true) {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.create_date,
                {$this->tblName}.description,
                {$this->tblName}.profit,
                catalog_profit.name
              FROM 
                {$this->tblName}
              LEFT JOIN
                user_plan 
              ON 
                user_plan.user_plan_id = {$this->tblName}.user_plan_id
              LEFT JOIN
                catalog_profit 
              ON 
                catalog_profit.catalog_profit_id = {$this->tblName}.catalog_profit_id
              WHERE 
                user_plan.user_login_id = '{$user_login_id}'
              AND 
                {$this->tblName}.status = '1'
              ";

      return $this->connection()->rows($sql);
    }

    return false;
  }
  
  public function getProfitsPerDay(string $day = null)
  {
    if (isset($day) === true) {

      $begin_of_day = strtotime(date("Y-m-d 00:00:00",strtotime($day)));
      $end_of_day = strtotime(date("Y-m-d 23:59:59",strtotime($day)));
      
      $sql = "SELECT
                SUM({$this->tblName}.profit) as profit
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
              ";

      if($profit = $this->connection()->field($sql))
      {
        return $profit;
      }
    }

    return 0;
  }
  
  public function getProfitToday(int $user_plan_id = null,int $catalog_profit_id = null)
  {
    if (isset($user_plan_id) === true) 
    {
      $begin_of_day = strtotime(date("Y-m-d 00:00:00"));
      $end_of_day = strtotime(date("Y-m-d 23:59:59"));
      
      $sql = "SELECT
                SUM({$this->tblName}.profit) as profit
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.status = '1'
              AND 
                {$this->tblName}.user_plan_id = '{$user_plan_id}'
              AND 
                {$this->tblName}.catalog_profit_id = '{$catalog_profit_id}'
              AND 
                {$this->tblName}.create_date
              BETWEEN 
                  {$begin_of_day}
              AND 
                  {$end_of_day}
              ";

      if($profit = $this->connection()->field($sql))
      {
        return $profit;
      }
    }

    return 0;
  }

  public function getAllProfitsPerDay(string $day = null)
  {
    if (isset($day) === true) {

      $begin_of_day = strtotime(date("Y-m-d 00:00:00",strtotime($day)));
      $end_of_day = strtotime(date("Y-m-d 23:59:59",strtotime($day)));
      
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.user_plan_id,
                {$this->tblName}.catalog_profit_id,
                {$this->tblName}.from_user_plan_id,
                {$this->tblName}.profit as gain
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.status = '1'
              AND 
                -- {$this->tblName}.catalog_profit_id IN ('1','2')
                {$this->tblName}.catalog_profit_id IN ('1')
              AND 
                {$this->tblName}.create_date
              BETWEEN 
                  {$begin_of_day}
              AND 
                  {$end_of_day}
              ";

      if($profit = $this->connection()->rows($sql))
      {
        return $profit;
      }
    }

    return 0;
  }

  public function getAllProfitsPerDaySum(string $day = null,int $catalog_profit_id = null)
  {
    if (isset($day) === true) {

      $begin_of_day = strtotime(date("Y-m-d 00:00:00",strtotime($day)));
      $end_of_day = strtotime(date("Y-m-d 23:59:59",strtotime($day)));
      
      $sql = "SELECT
                SUM({$this->tblName}.profit) as gain
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.status = '1'
              AND 
                -- {$this->tblName}.catalog_profit_id IN ('1','2')
                {$this->tblName}.catalog_profit_id = '{$catalog_profit_id}'
              AND 
                {$this->tblName}.create_date
              BETWEEN 
                  {$begin_of_day}
              AND 
                  {$end_of_day}
              ";

      if($profit = $this->connection()->field($sql))
      {
        return $profit;
      }
    }

    return 0;
  }
  
  public function getAllProfitsByDay(string $day = null)
  {
    if (isset($day) === true) {

      $begin_of_day = strtotime(date("Y-m-d 00:00:00",strtotime($day)));
      $end_of_day = strtotime(date("Y-m-d 23:59:59",strtotime($day)));
      
      $sql = "SELECT
                SUM({$this->tblName}.profit) as gain
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.status = '1'
              AND 
                {$this->tblName}.catalog_profit_id IN ('1','2')
              AND 
                {$this->tblName}.create_date
              BETWEEN 
                  {$begin_of_day}
              AND 
                  {$end_of_day}
              ";

      if($profit = $this->connection()->field($sql))
      {
        return $profit;
      }
    }

    return 0;
  }

  public function getAllProfits()
  {
    $sql = "SELECT
              SUM({$this->tblName}.profit) as profit
            FROM 
              {$this->tblName}
            WHERE 
              {$this->tblName}.status = '1'
            ";

    return $this->connection()->field($sql);
  }

  public function getWorkingDays(string $day = null)
  {
    $working_days = 0;

    $start_date = strtotime(date("Y-m-01",strtotime($day)));

    for ($i = 0; $i < date("t"); $i++) 
    {
      $unix = strtotime("+" . ($i) . " days",$start_date);

      if (date('N', $unix) < 6) {
        $working_days++;
      }
    }
    return $working_days;
  }

  public function calculateProfit(float $profit = null, float $ammount = null,string $day = null)
  {
    $day_profit = $profit / $this->getWorkingDays($day);

    return round(Util::getPercentaje($ammount, $day_profit),2);
  }

  public function insertGain(int $user_plan_id = null, int $from_user_plan_id = null, int $catalog_profit_id = null, float $profit = null, string $day = null, $description = ''): bool
  {
    $ProfitPerUser = new ProfitPerUser;
    $ProfitPerUser->user_plan_id = $user_plan_id;
    $ProfitPerUser->from_user_plan_id = $from_user_plan_id;
    $ProfitPerUser->catalog_profit_id = $catalog_profit_id;
    $ProfitPerUser->profit = $profit;
    $ProfitPerUser->create_date = $day ? $day : time();
    $ProfitPerUser->description = $description;

    if ($ProfitPerUser->save()) {
      $UserPlan = new UserPlan;

      if ($user_login_id = $UserPlan->getUserId($user_plan_id)) {
        // if($catalog_profit_id == Transaction::REFERRAL_INVESTMENT)
        // {
        //   $UserReferral = new UserReferral;

        //   $user_login_id = $UserReferral->getUserReferralId($user_plan_id);
        // }

        $UserWallet = new UserWallet;

        if ($UserWallet->getSafeWallet($user_login_id)) {
          return $UserWallet->doTransaction($profit, $catalog_profit_id, $ProfitPerUser->getId());
        }
      }
    }

    return false;
  }

  public function hasProfitToday(int $user_plan_id = null, int $catalog_profit_id = null, string $day = null)
  {
    if (isset($user_plan_id, $catalog_profit_id) === true) {
      $begin_of_day = strtotime(date("Y-m-d 00:00:00", strtotime($day)));
      $end_of_day = strtotime(date("Y-m-d 23:59:59", strtotime($day)));

      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_plan_id = '{$user_plan_id}'
              AND 
                {$this->tblName}.catalog_profit_id = '{$catalog_profit_id}'
              AND 
                {$this->tblName}.create_date
              BETWEEN 
                {$begin_of_day}
              AND 
                {$end_of_day}
              AND 
                {$this->tblName}.status = '1'
              ";

      return $this->connection()->field($sql);
    }

    return false;
  }

  public function hasProfitTodayForReferral(int $user_plan_id = null, int $from_user_plan_id = null, int $catalog_profit_id = null, string $day = null)
  {
    if (isset($user_plan_id, $from_user_plan_id, $catalog_profit_id) === true) {
      $begin_of_day = strtotime(date("Y-m-d 00:00:00", strtotime($day)));
      $end_of_day = strtotime(date("Y-m-d 23:59:59", strtotime($day)));

      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_plan_id = '{$user_plan_id}'
              AND 
                {$this->tblName}.from_user_plan_id = '{$from_user_plan_id}'
              AND 
                {$this->tblName}.catalog_profit_id = '{$catalog_profit_id}'
              AND 
                {$this->tblName}.create_date
              BETWEEN 
                {$begin_of_day}
              AND 
                {$end_of_day}
              AND 
                {$this->tblName}.status = '1'
              ";

      return $this->connection()->field($sql);
    }

    return false;
  }
}
