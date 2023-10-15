<?php define("TO_ROOT", "../../");

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
            if(!(new DummieTrading\TradePerUserTrading)->existTicket($data['ticket']))
            {
                if($user_login_id = $UserTradingAccount->getUserLoginIdByUserName($account['user_trading_account_id']))
                {
                    if(DummieTrading\TradePerUserTrading::add([
                        'user_trading_account_id' => $account['user_trading_account_id'],
                        'ticket' => $data['ticket'],
                        'login' => $data['login'],
                        'symbol' => $data['symbol'],
                        'price' => $data['price'] ?? 0,
                        'profit' => $data['profit'],
                        'buy' => $data['buy'],
                    ]))
                    {
                        if(isset($data['telegram']))
                        {
                            if($chat_id = (new DummieTrading\UserTelegram)->getChatId($user_login_id))
                            {
                                if(JFStudio\ApiTelegram::getResponse([
                                    'chat_id' => (new DummieTrading\UserTelegram)->getChatId($user_login_id),
                                    'user_trading_account_id' => $account['user_trading_account_id'],
                                ],"/profit"))
                                {
                                    
                                    $data["send_message"] = true;
                                } 
                            }
                        }
            
                        $data["s"] = 1;
                        $data["r"] = "DATA_OK";
                    } else {
                        $data["s"] = 0;
                        $data["r"] = "NOT_USER_ID";
                    }
                } else {
                    $data["s"] = 0;
                    $data["r"] = "NOT_USER_LOGIN_ID";
                }
            } else {
                $data["s"] = 0;
                $data["r"] = "TICKET_EXIST";
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