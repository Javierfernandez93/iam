<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if($data['user_trading_account_id'] ?? false)
    {
        if($user = DummieTrading\UserTradingAccount::updateAdditionalData([
            'user_trading_account_id' => $data['user_trading_account_id'],
            'additional_data' => $data['additional_data'],
            'balance' => $data['balance'],
            'drawdown' => $data['drawdown'],
        ]))
        {
            $data["s"] = 1;
            $data["r"] = "DATA_OK";
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_UPDATE_ADDITIONAL_DATA";
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