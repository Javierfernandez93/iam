<?php

use JFStudio\ApiTelegram;

 define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

if(isset($data['login']))
{
    DummieTrading\IpnMt5::add([
        'from' => 'addTradePerUserTrading',
        'date' => time(),
        'data' => $data,
    ]);

    $UserTradingAccount = new DummieTrading\UserTradingAccount;

    if($account = $UserTradingAccount->getByLogin($data['login']))
    {   
        if($account['status'] == DummieTrading\UserTradingAccount::IN_PROGRESS)
        { 
            if(DummieTrading\TradePerUserTrading::setOrderAsPlaced([
                'trade_per_user_trading_id' => $data['trade_per_user_trading_id'],
                'ticket' => $data['ticket']
            ]))
            {
                if($api = (new DummieTrading\TelegramApi)->getByName('DummieTrading'))
	            {
                    if($chat_id = (new DummieTrading\UserTelegram)->getChatId($account['user_login_id']))
                    {
                        if(JFStudio\ApiTelegram::sendMessage([
                            'api' => $api,
                            'message' => "Orden procesada TN#{$data['ticket']} - Cuenta {$account['login']}",
                            'chat_id' => $chat_id
                        ]))
                        {
                            $data["send_message"] = true;
                        } 
                    }
                } 


                $data["s"] = 1;
                $data["r"] = "DATA_OK";
            } else {
                $data["s"] = 0;
                $data["r"] = "NOT_ORDER";
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