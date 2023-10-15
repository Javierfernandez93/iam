<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{       
    if($data['user_trading_account_id'])
    {
        $data['drawdown'] = Api\MT4::calculateDrawDown($data['balance'],$data['equity']);

        if(DummieTrading\UserTradingAccount::updateData($data))
        {
            $data["s"] = 1;
            $data["r"] = "DATA_OK";
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_UPDATE";
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