<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
    if($landings = (new DummieTrading\CatalogLanding)->getAll())
    {
        $data['active'] =  $UserLogin->isActive();
        $data['active'] =  true;
        $data['landings'] = format(_filter($landings,$UserLogin->company_id),$UserLogin->company_id);
        $data["s"] = 1;
        $data["r"] = "DATA_OK";
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_DATA";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "INVALID_CREDENTIALS";
}

function _filter(array $landings = null,int $user_login_id = null) : array {
    $user_ids = [2,6,8,9,11,20,25,33,35,58,96,112,143,3];
    
    return array_filter($landings,function($landing) use($user_login_id,$user_ids) {
        if($landing['catalog_landing_id'] === 3)
        {
            return in_array($user_login_id,$user_ids);
        } else {
            return $landing;
        }

        return false;
    });
}

function format(array $landings = null,int $user_login_id = null) : array {
    $LandingPerUser = new DummieTrading\LandingPerUser;
    $VisitPerLanding = new DummieTrading\VisitPerLanding;

    return array_map(function($landing) use($LandingPerUser,$VisitPerLanding,$user_login_id) {
        $landing['error'] = null;
        $landing['editing'] = false;
        $landing['userLanding'] = [
            'route' => null
        ];

        if($userLanding = $LandingPerUser->getLanding($user_login_id,$landing['catalog_landing_id']))
        {
            $landing['userLanding'] = $userLanding;
            $landing['userLanding']['visit'] = $VisitPerLanding->getCount($userLanding['landing_per_user_id'],$userLanding['catalog_landing_id']);
        }

        return $landing;
    },$landings);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 