<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

if($data['login'])
{
    $UserTradingAccount = new DummieTrading\UserTradingAccount;

    if($user_trading_account_id = $UserTradingAccount->getIdByLogin($data['login']))
    {
        if(DummieTrading\UserTradingAccountLog::add($user_trading_account_id))
        {
            if($account = $UserTradingAccount->getSimpleTradingAccount($user_trading_account_id))
            {
                $data["initial_balance"] = $account['initial_balance'];
                $data["initial_drawdown"] = $account['initial_drawdown'];
                $data["s"] = 1;
                $data["r"] = "SAVED_LOG_OK";
            } else {
                $data['r'] = "NOT_DATA";
                $data['s'] = 0;    
            }
        } else {
            $data['r'] = "NOT_LOG_SAVED";
            $data['s'] = 0;
        }
    } else {
        $data['r'] = "NOT_USER_TRADING_ACCOUNT_ID";
        $data['s'] = 0;
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 