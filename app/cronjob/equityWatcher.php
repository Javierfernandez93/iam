<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getVarFromPGS();

// if(($data['PHP_AUTH_USER'] == HCStudio\Util::USERNAME && $data['PHP_AUTH_PW'] == HCStudio\Util::PASSWORD) || $UserSupport->logged === true)
if(true)
{
    $UserTradingAccount = new DummieTrading\UserTradingAccount;

    if($accounts = $UserTradingAccount->getAccountFilterByCatalogTradingAccountsWatchingDrawDown(DummieTrading\CatalogTradingAccount::METATRADER))
    {
        foreach($accounts as $account)
        {
            if($balance = $UserTradingAccount->getBalance($account['user_trading_account_id']))
            {
                $response = Api\MT4::getAccount($account['id']);
                
                if($response['s'] == 1)
                {
                    $drawdown = Api\MT4::calculateDrawDown($balance,$response['account']['equity']);

                    echo "Drawdown {$drawdown} {$account['login']}";

                    if($drawdown < 0)
                    {
                        DummieTrading\UserTradingAccount::updateDrawdown([
                            'drawdown' => $drawdown,
                            'user_trading_account_id' => $account['user_trading_account_id']
                        ]);

                        if($drawdown <= -5)
                        {
                            DummieTrading\UserTradingAccount::setAccountAsDrawdownReached($account['user_trading_account_id']);
                            
                            Api\MT4::closeOrders($account['id']);

                            $drawdownCeil = round(abs($drawdown),2);
                        
                            sendMessage($account['user_login_id'],"Tu cuenta {$account['login']} fué deshabilitada. Alcanzó el DrawDown {$drawdownCeil} %");

                            echo "Disabling account";
                        }
                    } else {
                        DummieTrading\UserTradingAccount::updateDrawdown([
                            'drawdown' => $drawdown,
                            'user_trading_account_id' => $account['user_trading_account_id']
                        ]);
                        $drawdownCeil = round(abs($drawdown),2);

                        sendMessage($account['user_login_id'],"Tu cuenta {$account['login']} alcanzó un DD de {$drawdownCeil}");
                    }
                }
            }
        }
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

function sendMessage(int $user_login_id = null,string $text = null)
{
    if(isset($user_login_id,$text))
    {
        require_once TO_ROOT . '/vendor/autoload.php';
    
        try { 
            try {
                if($api = (new DummieTrading\TelegramApi)->getByName('DummyTrader'))
                {
                    $telegram = new Longman\TelegramBot\Telegram($api['api_key'], $api['user_name']);
                    
                    if($chat_id = (new DummieTrading\UserTelegram)->getChatId($user_login_id))
                    {
                        $result = \Longman\TelegramBot\Request::sendMessage([
                            'chat_id' => $chat_id,
                            'text' => $text,
                        ]);
                    } else {
                        $data['s'] = 0;
                        $data['r'] = 'NOT_CHAT_ID';
                    }
                } else {
                    $data['s'] = 0;
                    $data['r'] = 'NOT_API';
                }
            } catch (Longman\TelegramBot\Exception\TelegramException $e) {
                $data['error_message'] = $e->getMessage();
                $data['s'] = 0;
                $data['r'] = 'ERROR_TELEGRAM';
            }
        } catch(Exception $e) {
            $data['error_message'] = $e->getMessage();
            $data['s'] = 0;
            $data['r'] = 'ERROR_TELEGRAM';
        }
    }
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 