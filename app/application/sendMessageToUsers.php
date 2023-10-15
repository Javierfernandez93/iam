<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if(isset($data['message']))
    {
        if(isset($data['telegram_channel_id']))
        {
            if(isset($data['telegram_api_id']))
            {
                $TelegramApi = new DummieTrading\TelegramApi;
                
                if($api = $TelegramApi->get($data['telegram_api_id']))
                {
                    if($signalProvier = (new DummieTrading\SignalProvider)->get($api['telegram_api_id']))
                    {
                        if($followers = (new DummieTrading\UserSignalProvider)->getAllFollowing($signalProvier['signal_provider_id']))
                        {
                            require_once TO_ROOT . '/vendor/autoload.php';
                            
                            try {
                                foreach($followers as $follower)
                                {
                                    $telegram = new Longman\TelegramBot\Telegram($api['api_key'],$api['user_name']);
                                    
                                    $result = Longman\TelegramBot\Request::sendMessage([
                                        'chat_id' => $follower['chat_id'],
                                        'text' => $data['message'],
                                    ]);
                                    if($result->ok == 1)
                                    {
                                        DummieTrading\TelegramMessage::add([
                                            'signal_provider_id' => $signalProvier['signal_provider_id'],
                                            'message_id' => $result->result->message_id,
                                            'catalog_trading_account_id' => DummieTrading\CatalogTradingAccount::BINANCE,
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
                                }
                            } catch (Longman\TelegramBot\Exception\TelegramException $e) {
                                DummieTrading\IpnTelegram::add(["response"=>$e->getMessage()]);
                            }
                        } else {
                            $data["s"] = 0;
                            $data["r"] = "NOT_CHANNEL";
                        } 
                    }
                } else {
                    $data["s"] = 0;
                    $data["r"] = "NOT_API";
                } 
            } else {
                $data["s"] = 0;
                $data["r"] = "NOT_TELEGRAM_API_ID";
            }    
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_TELEGRAM_CHANNEL_ID";
        }    
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_MESSAGE";
    }  
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 