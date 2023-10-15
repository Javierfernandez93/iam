<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

if($data['utm'])
{
    if($data['user_login_id'] = (new DummieTrading\LandingPerUser)->getReferralIdByRoute($data['utm']))
    {
        if($referral = (new DummieTrading\UserLogin(false,false))->getProfile($data['user_login_id']))
        {
            $data['referral'] = format($referral);
            $data["s"] = 1;
            $data["r"] = "DATA_OK";
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_DATA";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_USER_LOGIN_ID";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "INVALID_CREDENTIALS";
}

function format(array $referral = null) : array
{
    $referral['image'] = str_replace('../..',HCStudio\Connection::getMainPath(),$referral['image']);
    $referral['phone'] = (new World\Country)->getPhoneCodeByCountryId($referral['country_id']).$referral['phone'];
    $referral['phone'] = HCStudio\Util::sanitizeString($referral['phone'],true);

    return $referral;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 