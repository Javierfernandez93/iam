<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

if(isset($data['login']))
{
    if($user_login_id = (new DummieTrading\UserMetatrader)->getUserIdByLogin($data['login']))
    {
        if(sendTelegramMessage([
            'chat_id' => (new DummieTrading\UserTelegram)->getChatId($user_login_id)
        ]))
        {
            $data["s"] = 1;
            $data["r"] = "DATA_OK";
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_TELEGRAM_MESSAGE_SENT";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_USER_LOGIN_ID";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

function sendTelegramMessage(array $data = null) : bool
{
    if(isset($data))
    {
        require_once TO_ROOT . '/vendor/autoload.php';
    
        try {
            $telegram = new Longman\TelegramBot\Telegram(JFStudio\ApiTelegram::BOT_API_KEY, JFStudio\ApiTelegram::BOT_USERNAME);

            $result = Longman\TelegramBot\Request::sendMessage([
                'chat_id' => $data['chat_id'] ?? '',
                'text' => "tienes un profit de $300 USD",
            ]);

            return $result ? true : false;
        } catch (Longman\TelegramBot\Exception\TelegramException $e) {
            return false;
        }
    }

    return false;
}

echo json_encode($data);