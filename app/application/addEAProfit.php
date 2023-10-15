<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

if(isset($data['login']))
{
    if($user_login_id = (new DummieTrading\UserMetatrader)->getUserIdByLogin($data['login']))
    {   
        if(DummieTrading\ProfitPerUserMetatrader::addProfit([
            'profit' => $data['profit'],
            'login' => $data['login'],
        ]))
        {
            if(JFStudio\ApiTelegram::getResponse("/profit",(new DummieTrading\UserTelegram)->getChatId($user_login_id)))
            {
                $data["send_message"] = true;
            } 

            $data["s"] = 1;
            $data["r"] = "DATA_OK";
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_USER_ID";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_USER_LOGIN_ID";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode($data);