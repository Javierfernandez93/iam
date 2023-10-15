<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
    if($data['user_trading_account_id'] ?? null)
    {
        if($account = (new DummieTrading\UserTradingAccount)->getSimpleTradingAccount($data['user_trading_account_id']))
        {
            $data["account"] = $account;
        }
        
        if($trades = (new DummieTrading\TradePerUserTrading)->getAllFromUser($data))
        {
            $data["trades"] = $trades;   
        }
        
        if($drawdown = $UserLogin->getVar('drawdown'))
        {
            $data["account"]['initial_drawdown'] = $drawdown;
        }
        
        $data["s"] = 1;
        $data["r"] = "DATA_OK";
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_USER_TRADING_ID";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 