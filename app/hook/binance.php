<?php define("TO_ROOT", "../../");

require_once TO_ROOT . 'system/core.php';

$data = HCStudio\Util::getHeadersForWebService();

// $data = json_decode('{"eventType":"executionReport","eventTime":1694118464954,"symbol":"ADABUSD","newClientOrderId":"web_ab82230117a34de9aacec75507f18979","originalClientOrderId":"","side":"BUY","orderType":"MARKET","timeInForce":"GTC","quantity":"148.60000000","price":"0.00000000","executionType":"NEW","stopPrice":"0.00000000","icebergQuantity":"0.00000000","orderStatus":"NEW","orderRejectReason":"NONE","orderId":1228051190,"orderTime":1694118464954,"lastTradeQuantity":"0.00000000","totalTradeQuantity":"0.00000000","priceLastTrade":"0.00000000","commission":"0","commissionAsset":null,"tradeId":-1,"isOrderWorking":true,"isBuyerMaker":false,"creationTime":1694118464954,"totalQuoteTradeQuantity":"0.00000000","orderListId":-1,"quoteOrderQuantity":"38.10768000","lastQuoteTransacted":"0.00000000"}',true);

// d($data);

if(isset($data['eventType']))
{
	if($data['eventType'] == 'executionReport')
	{
		if($data['executionType'] == 'NEW')
		{
			DummieTrading\IpnBinance::add($data);
			
			$data['signal_provider_id'] = 1;
	
			if($followers = (new DummieTrading\UserSignalProvider)->getAllFollowing($data['signal_provider_id']))
			{
				$url = HCStudio\Connection::getMainPath()."/app/application/sendMessageToUser.php";
	
				$Curl = new JFStudio\Curl;
	
				$message['symbol'] = DummieTrading\SymbolParserPerBroker::fixSymbol($data['symbol']); 
				
				// foreach($followers as $follower)
				// {
				// 	$Curl->post($url,[
				// 		'user' => HCStudio\Util::USERNAME,
				// 		'password' => HCStudio\Util::PASSWORD,
				// 		'chat_id' => $follower['chat_id'],
				// 		'telegram_api_id' => $message['telegram_api_id'],
				// 		'signal_provider_id' => $message['signal_provider_id'],
				// 		'signal' => [
				// 			"market_type" => 1,
				// 			"symbol" => $message['symbol'],
				// 			"quantity" => 0.02,
				// 			"side" => $message['side'],
				// 			"type" => $message['stopLoss'] == 'N/A' ? 'market' : 'oco',
				// 			"price" => 0,
				// 			"priceEntrace" => $message['priceEntrace'] == 'N/A' ? 0 :  $message['priceEntrace'],
				// 			"takeProfit" => $message['takeProfit'] == 'N/A' ? 0 :  $message['takeProfit'],
				// 			"stopPrice" => $message['stopLoss'] == 'N/A' ? 0 :  $message['stopLoss'],
				// 			"stopLimitPrice" => $message['stopLoss'] == 'N/A' ? 0 :  $message['stopLoss'],
				// 		],
				// 	]);
	
				// 	$Curl->getResponse(true);
				// }	
				
				$Curl->post($url,[
					'user' => HCStudio\Util::USERNAME,
					'password' => HCStudio\Util::PASSWORD,
					'chat_id' => 1930485774,
					'telegram_api_id' => 2,
					'signal_provider_id' => $data['signal_provider_id'],
					'signal' => [
						"market_type" => 1,
						"symbol" => JFStudio\SymbolParser::sanitizePair($data['symbol']),
						"quantity" => 1,
						"side" => $data['side'],
						"type" => isset($data['orderType']) ? $data['orderType'] : '',
						"price" => 0,
						"priceEntrace" => isset($data['price']) ? $data['price'] : 0,
						"takeProfit" => isset($data['takeProfit']) ? $data['takeProfit'] : 0,
						"stopPrice" => isset($data['stopPrice']) ? $data['stopPrice'] : 0,
						"stopLimitPrice" => isset($data['stopPrice']) ? $data['stopPrice'] : 0,
					],
				]);
	
				$Curl->getResponse(true);
			}
		}
	} 
} 