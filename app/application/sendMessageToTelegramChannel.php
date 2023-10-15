<?php  define("TO_ROOT", "../../");

require_once TO_ROOT . 'system/core.php';

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
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
                    $TelegramChannel = new DummieTrading\TelegramChannel;

                    if($channel = $TelegramChannel->get($data['telegram_channel_id']))
                    {
                        require_once TO_ROOT . '/vendor/autoload.php';
                
                        try {
                            $telegram = new Longman\TelegramBot\Telegram($api['api_key'],$api['user_name']);
            

                            $result = Longman\TelegramBot\Request::sendMessage([
                                'chat_id' => $channel['chat_id'],
                                'text' => $data['message'],
                            ]);

                            if($result->ok == 1)
                            {
                                DummieTrading\TelegramMessageChannel::add([
                                    'telegram_channel_id' => $channel['telegram_channel_id'],
                                    'message' => $data['message'],
                                ]);
                                
                                $data["s"] = 1;
                                $data["r"] = "DATA_OK";
                            } else {
                                $data["s"] = 0;
                                $data["r"] = "NOT_RESULT";
                            } 
                        } catch (Longman\TelegramBot\Exception\TelegramException $e) {
                            DummieTrading\IpnTelegram::add(["response"=>$e->getMessage()]);
                        }
                    } else {
                        $data["s"] = 0;
                        $data["r"] = "NOT_CHANNEL";
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
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode($data);