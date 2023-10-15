<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

$data['user'] = isset($data['user']) ? $data['user'] : null;
$data['password'] = isset($data['password']) ? $data['password'] : null;

if(($data['user'] == HCStudio\Util::USERNAME && $data['password'] == HCStudio\Util::PASSWORD) || $UserLogin->logged === true)
{	
    if(isset($data['signal']))
    {
        if(isset($data['telegram_api_id']))
        {
            if($signalProvider = (new DummieTrading\SignalProvider)->get($data['signal_provider_id']))
            {
                $data['signal'] = [
                    ...['provider' => "{$signalProvider['name']}\n"],
                    ...$data['signal']
                ];

                $data['message'] = JFStudio\ParserOrder::parse($data['signal']);

                $TelegramApi = new DummieTrading\TelegramApi;
                
                if($api = $TelegramApi->get($data['telegram_api_id']))
                {
                    require_once TO_ROOT . '/vendor/autoload.php';
                    
                    try {
                        $telegram = new Longman\TelegramBot\Telegram($api['api_key'],$api['user_name']);
                        
                        $result = Longman\TelegramBot\Request::sendMessage([
                            'chat_id' => $data['chat_id'],
                            'text' => $data['message'],
                        ]);
    
                        if($result->ok == 1)
                        {
                            DummieTrading\TelegramMessage::add([
                                'signal_provider_id' => $data['signal_provider_id'],
                                'message_id' => $result->result->message_id,
                                'catalog_trading_account_id' => $data['signal']['market_type'],
                                'message' => $data['message'],
                                'data' => json_encode($data['signal']),
                            ]);
                            
                            $data["s"] = 1;
                            $data["r"] = "DATA_OK";
                        } else {
                            $data["result"] = $result;
                            $data["r"] = "NOT_RESULT";
                            $data["r"] = "NOT_RESULT";
                        } 
                    } catch (Longman\TelegramBot\Exception\TelegramException $e) {
                        DummieTrading\IpnTelegram::add(["response"=>$e->getMessage()]);
                    }
                } else {
                    $data["s"] = 0;
                    $data["r"] = "NOT_API";
                } 
            } else {
                $data["s"] = 0;
                $data["r"] = "NOT_SIGNALPROVIDER";
            } 
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_TELEGRAM_API_ID";
        }    
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_SIGNAL";
    }  
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 