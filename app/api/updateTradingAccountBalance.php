<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

if(isset($data['login']))
{
    DummieTrading\IpnMt5::add([
        'from' => 'updateTradingAccountBalance',
        'date' => time(),
        'data' => $data,
    ]);

    $UserTradingAccount = new DummieTrading\UserTradingAccount;

    if($account = $UserTradingAccount->getByLogin($data['login']))
    {   
        if($account['status'] == DummieTrading\UserTradingAccount::IN_PROGRESS)
        {
            if(DummieTrading\UserTradingAccount::updateBalance([
                'user_trading_account_id' => $account['user_trading_account_id'],
                'balance' => $data['balance']
            ]))
            {
                $data["s"] = 1;
                $data["r"] = "DATA_OK";
            } else {
                $data["s"] = 0;
                $data["r"] = "NOT_UPDATE_BALANCE";
            }
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_RUNNING_ACCOUNT";
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