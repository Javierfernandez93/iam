<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php"; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
    if($api = (new DummieTrading\TelegramApi)->getByName('DummieTrading'))
    {
        require_once TO_ROOT . '/vendor/autoload.php';
                    
        try {
            $telegram = new Longman\TelegramBot\Telegram($api['api_key'],$api['user_name']);
    
            $result = Longman\TelegramBot\Request::sendMessage([
                'chat_id' => $data['chat_id'],
                'text' => "¡Hola desde Dummie Trading!",
            ]);
    
            if($result->ok == 1)
            {
                $data["s"] = 1;
                $data["r"] = "DATA_OK";
            } else {
                $data["result"] = $result;
                $data["r"] = "NOT_RESULT";
            } 
        } catch (Longman\TelegramBot\Exception\TelegramException $e) {
            DummieTrading\IpnTelegram::add(["response"=>$e->getMessage()]);
        }
    } else {
        $data['r'] = 'NOT_API';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'INVALID_CREDENTIALS';
	$data['s'] = 0;
}

echo json_encode($data); 