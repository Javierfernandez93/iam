<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

if(isset($data['login']))
{
    DummieTrading\IpnMt5::add([
        'from' => 'setDrawdownAsReached',
        'date' => time(),
        'data' => $data,
    ]);

    $UserTradingAccount = new DummieTrading\UserTradingAccount;

    if($account = $UserTradingAccount->getByLogin($data['login']))
    {   
        if($account['status'] == DummieTrading\UserTradingAccount::IN_PROGRESS)
        {
            if(DummieTrading\UserTradingAccount::setAccountAsDrawdownReached($account['user_trading_account_id']))
            {
                if($chat_id = (new DummieTrading\UserTelegram)->getChatId($account['user_login_id']))
                {
                    if(JFStudio\ApiTelegram::sendMessage("Tu cuenta {$data['login']} será deshabilitada porque alcanzaste el máximo de Drawdown",$chat_id))
                    {
                        $data["send_message"] = true;
                    } 
                }
    
                $data["s"] = 1;
                $data["r"] = "DATA_OK";
            } else {
                $data["s"] = 0;
                $data["r"] = "NOT_SETTED_AS_REACHED";
            }
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_RUNNING_ACCOUNT";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_USER_TRADING_ACCOUNT_ID";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode($data);