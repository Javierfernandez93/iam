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
        if($account['drawdown'] != $data['drawdown'])
        {
            if($api = (new DummieTrading\TelegramApi)->getByName('DummieTrading'))
            {
                if($chat_id = (new DummieTrading\UserTelegram)->getChatId($account['user_login_id']))
                {
                    if(JFStudio\ApiTelegram::sendMessage([
                        'api' => $api,
                        'message' => JFStudio\ApiTelegram::getDrawdownMessage($data['drawdown'],$account['initial_drawdown']),
                        'chat_id' => $chat_id
                    ]))
                    {
                        $data["send_message"] = true;
                    } 
                }
            }
        }

        if(DummieTrading\UserTradingAccount::updateDrawdown([
            'user_trading_account_id' => $account['user_trading_account_id'],
            'drawdown' => $data['drawdown']
        ]))
        {
            $data["s"] = 1;
            $data["r"] = "DATA_OK";
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_UPDATE_DRAWDOWN";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_ACCOUNT";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode($data);