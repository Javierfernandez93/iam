<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
    if($data['catalog_landing_id'])
    {
        if($data['route'])
        {
            $LandingPerUser = new DummieTrading\LandingPerUser;
            
            if(!$LandingPerUser->existRoute($UserLogin->company_id,$data['catalog_landing_id'],$data['route']))
            {
                if(!$LandingPerUser->loadWhere("user_login_id = ? AND catalog_landing_id = ?",[$UserLogin->company_id,$data['catalog_landing_id']]))
                {
                    $LandingPerUser->create_date = time();
                }
                
                $LandingPerUser->user_login_id = $UserLogin->company_id;
                $LandingPerUser->route = $data['route'];
                $LandingPerUser->catalog_landing_id = $data['catalog_landing_id'];

                if($LandingPerUser->save())
                {
                    $data["s"] = 1;
                    $data["r"] = "DATA_OK";
                } else {
                    $data["s"] = 0;
                    $data["r"] = "NOT_SAVE";
                }
            } else {
                $data["s"] = 0;
                $data["r"] = "ALREADY_EXIST_ROUTE";
            }
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_ROUTE";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_CATALOG_LANDING_ID";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "INVALID_CREDENTIALS";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 