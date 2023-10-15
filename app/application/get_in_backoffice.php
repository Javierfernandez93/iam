<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if($data['company_id'])
    {
        if($user_credentials = $UserSupport->getUserCredentials($data['company_id']))
        {
            $UserLogin = new DummieTrading\UserLogin(false,false);
            
            if($UserLogin->login($user_credentials['email'],$user_credentials['password']))
            {
                $data["s"] = 1;
                $data["r"] = "DATA_OK";
            } else {
                $data["s"] = 0;
                $data["r"] = "NOT_LOGGED";
            }
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_USER_LOGIN_ID";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_USERLOGIN_ID";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 