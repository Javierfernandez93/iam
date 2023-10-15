<?php define("TO_ROOT", "../../");

require_once TO_ROOT . '/vendor/autoload.php';
require_once TO_ROOT . 'system/core.php';

$data = HCStudio\Util::getHeadersForWebService();

$data = json_decode('{"update_id":872889596,"channel_post":{"message_id":17,"sender_chat":{"id":-1001909128013,"title":"SignalCatcher","type":"channel"},"chat":{"id":-1001909128013,"title":"SignalCatcher","type":"channel"},"date":1692582452,"text":"{\n    \"telegram_api_id\":  \"2\",\n    \"signal_provider_id\" : \"3\",\n    \"symbol\": \"XAUUSD.\",\n    \"side\": \"sell\",\n    \"priceEntrace\": \"1886.19\",\n    \"takeProfit\": \"N\/A\",\n    \"stopLoss\": \"N\/A\",\n    \"type\": \"market\",\n    \"volume\": \"1\",\n    \"operation\": \"Open order\"\n}"},"gzip":true}',true);
$pass = true;
// d($data);

try {
    DummieTrading\IpnTelegram::add(["file"=>'telegramSignalCopier']);

	if($api = (new DummieTrading\TelegramApi)->getByName('DummieTradingSingalsCopier'))
	{
		$telegram = new Longman\TelegramBot\Telegram($api['api_key'], $api['user_name']);
		
		// if($response = $telegram->handle())
		if(true)
		{
			$message = json_decode($data['channel_post']['text'], true);

            if($followers = (new DummieTrading\UserSignalProvider)->getAllFollowing($message['signal_provider_id']))
            {
                $url = HCStudio\Connection::getMainPath()."/app/application/sendMessageToUser.php";

                $Curl = new JFStudio\Curl;
                
                foreach($followers as $follower)
                {
                    $Curl->post($url,[
                        'user' => HCStudio\Util::USERNAME,
                        'password' => HCStudio\Util::PASSWORD,
                        'chat_id' => $follower['chat_id'],
                        'telegram_api_id' => $message['telegram_api_id'],
                        'signal_provider_id' => $message['signal_provider_id'],
                        'signal' => [
                            "market_type" => 1,
                            "symbol" => $message['symbol'],
                            "quantity" => 1,
                            "side" => $message['side'],
                            "type" => $message['stopLoss'] == 'N/A' ? 'market' : 'oco',
                            "price" => 0,
                            "priceEntrace" => $message['priceEntrace'] == 'N/A' ? 0 :  $message['priceEntrace'],
                            "takeProfit" => $message['takeProfit'] == 'N/A' ? 0 :  $message['takeProfit'],
                            "stopPrice" => $message['stopLoss'] == 'N/A' ? 0 :  $message['stopLoss'],
                            "stopLimitPrice" => $message['stopLoss'] == 'N/A' ? 0 :  $message['stopLoss'],
                        ],
                    ]);

                    $Curl->getResponse(true);
                }
            } 

            // d($message);
		} else {
			DummieTrading\IpnTelegram::add(["response"=>"NOT_HANDLED"]);
		}
	}
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    DummieTrading\IpnTelegram::add(["response"=>$e->getMessage(),"file"=>'telegramSignalCopier']);
}