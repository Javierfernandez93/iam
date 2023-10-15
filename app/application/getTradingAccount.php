<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
    if(isset($data['user_trading_account_id']))
    {
        if($account = (new DummieTrading\UserTradingAccount)->getSimpleTradingAccount($data['user_trading_account_id']))
        {
            if($drawdown = $UserLogin->getVar('drawdown'))
            {
                $account['initial_drawdown'] = $drawdown;
            }

            $data["account"] = $account;
            $data["s"] = 1;
            $data["r"] = "DATA_OK";
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_USER_TRADING_ACCOUNT";
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