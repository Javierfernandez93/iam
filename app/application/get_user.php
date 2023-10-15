<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if($data['user_login_id'])
    {
        $data["user"] = formatDate($UserSupport->getUser($data['user_login_id']));
        $data["user_referral_id"] = $UserSupport->getUserReferralId($data['user_login_id']);
        
        $data["s"] = 1;
        $data["r"] = "DATA_OK";
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_USER_LOGIN_ID";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

function formatDate(array $user = null) : array
{
    if(isset($user['signup_date']))
    {
        $user['signup_date'] = date("Y-m-d",$user['signup_date']);
    }

    return $user;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 