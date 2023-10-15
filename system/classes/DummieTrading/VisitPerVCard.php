<?php

namespace DummieTrading;

use HCStudio\Orm;

use JFStudio\Constants;

class VisitPerVCard extends Orm
{
	protected $tblName = 'visit_per_vcard';

	public function __construct()
	{
		parent::__construct();
	}

	public static function addVisit(int $vcard_per_user_id = null) : bool
    {
        $VisitPerVCard = new VisitPerVCard;
        $VisitPerVCard->vcard_per_user_id = $vcard_per_user_id;
        $VisitPerVCard->create_date = time();
        return $VisitPerVCard->save();
    }
	
    public function getCount(int $vcard_per_user_id = null) : int
    {
        if(isset($vcard_per_user_id) == true)
        {
            $sql = "SELECT 
                        COUNT({$this->tblName}.{$this->tblName}_id) as c
                    FROM
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.vcard_per_user_id = '{$vcard_per_user_id}'
                    AND  
                        {$this->tblName}.status = '".Constants::AVIABLE."'";
                
            return $this->connection()->field($sql);
        }

        return 0;
    }
}
