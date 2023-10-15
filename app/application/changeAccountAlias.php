<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
    if($data['alias'])
    {
        if($data['user_trading_account_id'])
        {
            if(DummieTrading\UserTradingAccount::changeAccountAlias([
                'alias' => $data['alias'],
                'user_trading_account_id' => $data['user_trading_account_id']
            ])) {
                $data["s"] = 1;
                $data["r"] = "DATA_OK";
            } else {
                $data["s"] = 0;
                $data["r"] = "NOT_RESPONSE";
            }
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_API_SECRET";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_API_KEY";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode($data);