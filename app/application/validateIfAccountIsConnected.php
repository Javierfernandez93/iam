<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if ($UserLogin->logged === true) 
{
    if ($data['id']) 
    {
        if ($response = Api\MT4::getAccount($data['id'])) 
        {
            if($response['s'] == 1)
            {
                $data["s"] = 1;
                $data["r"] = "ACCOUNT_CONNECTED";
            } else {
                if(DummieTrading\UserTradingAccount::disconnectFromMetaTrader($data['id']))
                {
                    $data['account_disconnected'] = true;
                }

                $data["s"] = 0;
                $data["r"] = "NOT_CONNECTED";
            }
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_RESPONSE";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_USER_TRADING_ACCOUNT_ID";
    }
} else {
    $data["s"] = 0;
    $data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data));
