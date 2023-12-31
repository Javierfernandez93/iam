<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
    if($data['signal_provider_id'])
    {
        if(DummieTrading\UserSignalProvider::unFollowSignal([
            'signal_provider_id' => $data['signal_provider_id'],
            'user_login_id' => $UserLogin->company_id
        ])) 
        {
            $data["s"] = 1;
            $data["r"] = "DATA_OK";
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_RESPONSE";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_RESPONSE";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode($data);