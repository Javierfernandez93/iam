<?php define("TO_ROOT", "../../");

require_once TO_ROOT . '/vendor/autoload.php';
require_once TO_ROOT . 'system/core.php';

$data = HCStudio\Util::getHeadersForWebService();

// $data = json_decode('{"update_id":872901468,"channel_post":{"message_id":6318,"sender_chat":{"id":-1001909128013,"title":"Dummie","username":"signalcatcherdt","type":"channel"},"chat":{"id":-1001909128013,"title":"Dummie","username":"signalcatcherdt","type":"channel"},"date":1695060913,"text":"{\n    \"telegram_api_id\":  \"2\",\n    \"signal_provider_id\" : \"6\",\n    \"symbol\": \"EURUSDm\",\n    \"side\": \"buy\",\n    \"priceEntrace\": \"1.06848\",\n    \"takeProfit\": \"1.07891\",\n    \"stopLoss\": \"1.06548\",\n    \"type\": \"market\",\n    \"volume\": \"0.08\",\n    \"operation\": \"Open order\"\n}"},"gzip":true}',true);
// $data = json_decode('{"update_id":872905827,"message":{"message_id":38005,"from":{"id":1930485774,"is_bot":false,"first_name":"Javier","last_name":"Fern\u00e1ndez","username":"Jfernandez93","language_code":"es"},"chat":{"id":1930485774,"first_name":"Javier","last_name":"Fern\u00e1ndez","username":"Jfernandez93","type":"private"},"date":1695346971,"text":"token=S7Hqax8iNwMfPusb"},"gzip":true}',true);
// $data = json_decode('{"update_id":872905827,"message":{"message_id":38005,"from":{"id":1930485774,"is_bot":false,"first_name":"Javier","last_name":"Fern\u00e1ndez","username":"Jfernandez93","language_code":"es"},"chat":{"id":1930485774,"first_name":"Javier","last_name":"Fern\u00e1ndez","username":"Jfernandez93","type":"private"},"date":1695346971,"text":"/start S7Hqax8iNwMfPusb"},"gzip":true}',true);
// $pass = true;

try {
	if($api = (new DummieTrading\TelegramApi)->getByName('DummieTrading'))
	{
		$telegram = new Longman\TelegramBot\Telegram($api['api_key'], $api['user_name']);
		
		// if(true)
		// if($pass)
		if($response = $telegram->handle())
		{
			if(isset($data['message']))
			{
				$message = $data['message']['text'];
				$chat_id = $data['message']['from']['id'];
				
				if(isset($data['message']['reply_to_message']))
				{
					$message = "copy={$data['message']['reply_to_message']['message_id']},si adelante {$message}";
				} 

				JFStudio\Dispatcher::dispatcher([
					'api' => $api,
					'message' => $message,
					'chat_id' => $chat_id,
				]);
			} else if(isset($data['channel_post'])) {
				$message = json_decode($data['channel_post']['text'], true);

				if($message['operation'] == 'Open order' && $message['takeProfit'] != 'N/A' && $message['stopLoss'] != 'N/A')
				{
					if((new DummieTrading\IpnTelegram)->existByQuery(http_build_query($data)) == false)
					{
						// try {
						// 	DummieTrading\TelegramMessage::sendSignalToUser([
						// 		'chat_id' => 1930485774,
						// 		'telegram_api_id' => $message['telegram_api_id'],
						// 		'signal_provider_id' => $message['signal_provider_id'],
						// 		'signal' => [
						// 			"market_type" => 1,
						// 			"symbol" => JFStudio\SymbolParser::sanitizePair($message['symbol']),
						// 			"quantity" => 1,
						// 			"side" => $message['side'],
						// 			"type" => $message['stopLoss'] == 'N/A' ? 'market' : 'oco',
						// 			"price" => 0,
						// 			"priceEntrace" => $message['priceEntrace'] == 'N/A' ? 0 :  $message['priceEntrace'],
						// 			"takeProfit" => $message['takeProfit'] == 'N/A' ? 0 :  $message['takeProfit'],
						// 			"stopPrice" => $message['stopLoss'] == 'N/A' ? 0 :  $message['stopLoss'],
						// 			"stopLimitPrice" => $message['stopLoss'] == 'N/A' ? 0 :  $message['stopLoss'],
						// 		],
						// 	]);
						// } catch (\Exception $e) {
						// 	// stuff
						// }

						if($followers = (new DummieTrading\UserSignalProvider)->getAllFollowing($message['signal_provider_id']))
						{
							foreach($followers as $follower)
							{
								try {

									DummieTrading\TelegramMessage::sendSignalToUser([
										'chat_id' => $follower['chat_id'],
										'telegram_api_id' => $message['telegram_api_id'],
										'signal_provider_id' => $message['signal_provider_id'],
										'signal' => [
											"market_type" => 1,
											"symbol" => JFStudio\SymbolParser::sanitizePair($message['symbol']),
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
								} catch (\Exception $e) {
									// stuff
								}
							}
						}
					} 

				} 
			}

			DummieTrading\IpnTelegram::add($data);
		} else {
			DummieTrading\IpnTelegram::add(["response"=>"NOT_HANDLED"]);
		}
	}
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    DummieTrading\IpnTelegram::add(["response"=>$e->getMessage()]);
}