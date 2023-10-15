<?php

namespace DummieTrading;

use HCStudio\Orm;

use JFStudio\Constants;

use DummieTrading\UserMetatrader;

class ProfitPerUserMetatrader extends Orm {
  protected $tblName  = 'profit_per_user_metatrader';

  public function __construct() {
    parent::__construct();
  }

  public static function addProfit(array $data = null) : bool
  {
    if(isset($data) === true)
    {
        $ProfitPerUserMetatrader = new self;

        if(isset($data['login']) === true)
        {
            $data['user_metatrader_id'] = (new UserMetatrader)->getIdByLogin($data['login']);
        }
        
        $ProfitPerUserMetatrader->user_metatrader_id = $data['user_metatrader_id'];
        $ProfitPerUserMetatrader->profit = $data['profit'];
        $ProfitPerUserMetatrader->create_date = time();
            
        return $ProfitPerUserMetatrader->save();
    }

    return false;
  }

  public function getLastProfit(string $user_metatrader_id = null) : int|float
  {
    if(isset($user_metatrader_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.profit
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_metatrader_id = '{$user_metatrader_id}'
              AND
                {$this->tblName}.status = '".Constants::AVIABLE."'
              ORDER BY 
                {$this->tblName}.create_date 
              DESC
              ";
            
      return $this->connection()->field($sql);
    }

    return 0;
  }

  public function getLastProfits(string $user_metatrader_id = null) : int|float
  {
    if(isset($user_metatrader_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.profit
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_metatrader_id = '{$user_metatrader_id}'
              AND
                {$this->tblName}.status = '".Constants::AVIABLE."'
              ";
              
      return $this->connection()->field($sql);
    }

    return 0;
  }
}