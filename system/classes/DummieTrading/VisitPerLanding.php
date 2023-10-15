<?php

namespace DummieTrading;

use HCStudio\Orm;

use JFStudio\Constants;

class VisitPerLanding extends Orm
{
	protected $tblName = 'visit_per_landing';

	public function __construct()
	{
		parent::__construct();
	}

	public static function addVisit(int $landing_per_user_id = null,string $client_ip = null,int $catalog_landing_id = null) : bool
    {
        $VisitPerLanding = new VisitPerLanding;
        $VisitPerLanding->client_ip = $client_ip;
        $VisitPerLanding->landing_per_user_id = $landing_per_user_id;
        $VisitPerLanding->catalog_landing_id = $catalog_landing_id;
        $VisitPerLanding->create_date = time();

        return $VisitPerLanding->save();
    }
	
    public function getCount(int $landing_per_user_id = null,int $catalog_landing_id = null) : int
    {
        if(isset($landing_per_user_id) == true)
        {
            $sql = "SELECT 
                        COUNT({$this->tblName}.{$this->tblName}_id) as c
                    FROM
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.landing_per_user_id = '{$landing_per_user_id}'
                    AND 
                        {$this->tblName}.catalog_landing_id = '{$catalog_landing_id}'
                    AND  
                        {$this->tblName}.status = '".Constants::AVIABLE."'";
                
            return $this->connection()->field($sql);
        }

        return 0;
    }
}
