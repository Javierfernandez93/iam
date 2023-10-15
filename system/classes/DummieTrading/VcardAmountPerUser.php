<?php

namespace DummieTrading;

use HCStudio\Orm;

use JFStudio\Constants;

use DummieTrading\VCardPerUser;

class VcardAmountPerUser extends Orm
{
	protected $tblName = 'vcard_amount_per_user';

	public function __construct()
	{
		parent::__construct();
	}

	public static function addCards(int $buy_per_user_id = null,int $user_login_id = null,int $amount = null) : bool
    {
        $VcardAmountPerUser = new VcardAmountPerUser;

        if(!$VcardAmountPerUser->exist($buy_per_user_id))
        {
            $VcardAmountPerUser->buy_per_user_id = $buy_per_user_id;
            $VcardAmountPerUser->user_login_id = $user_login_id;
            $VcardAmountPerUser->amount = $amount;
            $VcardAmountPerUser->create_date = time();
        
            return $VcardAmountPerUser->save();
        }

        return false;
    }

	public function isAbleToAddVcard(int $user_login_id = null) : bool
	{
        if (isset($user_login_id) === true) 
        {
			$totalOfAviableVCards = $this->getVcardSumAmount($user_login_id);
			$totalofUsedVCards = (new VCardPerUser)->getCount($user_login_id);

            return $totalOfAviableVCards > $totalofUsedVCards;
		}
	}
	
    public function getVcardSumAmount(int $user_login_id = null) : int
	{
		if (isset($user_login_id) === true) 
        {
			$sql = "SELECT 
                        SUM({$this->tblName}.amount) as amount
                    FROM 
                        {$this->tblName}
                    WHERE
                        {$this->tblName}.user_login_id = '{$user_login_id}'
                    AND 
                        {$this->tblName}.status = '".Constants::AVIABLE."'
                    ";

            if($amount = $this->connection()->field($sql))
            {
                return $amount;
            }
		}

        return 0;
	}

    public function exist(int $buy_per_user_id = null) : bool
	{
		if (isset($buy_per_user_id) === true) 
        {
			$sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id
                    FROM 
                        {$this->tblName}
                    WHERE
                        {$this->tblName}.buy_per_user_id = '{$buy_per_user_id}'
                    AND 
                        {$this->tblName}.status = '".Constants::AVIABLE."'
                    ";

            return $this->connection()->field($sql) ? true : false;
		}

        return false;
	}
}
