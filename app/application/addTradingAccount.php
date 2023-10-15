<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
    if($data['catalog_trading_account_id'] == DummieTrading\CatalogTradingAccount::METATRADER)
    {
        $data["platform"] = (new DummieTrading\CatalogPlatform)->getType($data['catalog_platform_id']);

        if(!Api\MT4::isValidBroker($data['server'],$data["platform"]))
        {
            $data["s"] = 0;
            $data["r"] = "INVALID_BROKER";

            echo json_encode(HCStudio\Util::compressDataForPhone($data)); die;
        }
    }

    if($user_trading_account_id = DummieTrading\UserTradingAccount::add([
        'user_login_id' => $UserLogin->company_id,
        'catalog_trading_account_id' => $data['catalog_trading_account_id'],
        'catalog_platform_id' => isset($data['catalog_platform_id']) ? $data['catalog_platform_id'] : 0,
        'login' => $data['login'],
        'server' => $data['server'] ?? '',
        'trader' => $UserLogin->getNames(),
        'password' => $data['password']
    ]))
    {
        if($chat_id = (new DummieTrading\UserTelegram)->getChatId($UserLogin->company_id))
        {
            sendTelegram($chat_id,"¡Gracias!\nHemos conectado tu cuenta {$data['login']} a tu DummieTrading");
        }

        $data["user_trading_account_id"] = $user_trading_account_id;
        $data["s"] = 1;
        $data["r"] = "DATA_OK";
    } else {
        $data["s"] = 0;
        $data["r"] = "ALREADY_HAS_ACCOUNT";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

function sendTelegram(string $chat_id = null,string $message = null)
{
    if($api = (new DummieTrading\TelegramApi)->getByName('DummieTrading'))
    {
        require_once TO_ROOT . '/vendor/autoload.php';
                    
        try {
            $telegram = new Longman\TelegramBot\Telegram($api['api_key'],$api['user_name']);
    
            $result = Longman\TelegramBot\Request::sendMessage([
                'chat_id' => $chat_id,
                'text' => $message,
            ]);
    
            if($result->ok == 1)
            {
                return true;
            }
        }  catch (Longman\TelegramBot\Exception\TelegramException $e) {
			return false;
		}
    }

    return false;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 