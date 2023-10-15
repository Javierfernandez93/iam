<?php  define("TO_ROOT", "../../");

require_once TO_ROOT . 'system/core.php';

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
	require_once TO_ROOT . '/vendor/autoload.php';

	try { 
        try {
            $data['signal'] = [
                ...['provider' => "{$UserLogin->getNames()}\n"],
                ...$data['signal']
            ];
        
            $data['message'] = JFStudio\ParserOrder::parse($data['signal']);
        
            if($api = (new DummieTrading\TelegramApi)->getByName('DummieTrading'))
			{
				$telegram = new Longman\TelegramBot\Telegram($api['api_key'], $api['user_name']);

				if($chat_id = (new DummieTrading\UserTelegram)->getChatId($UserLogin->company_id))
				{
					$result = Longman\TelegramBot\Request::sendMessage([
                        'chat_id' => $chat_id,
                        'text' => $data['message'],
                    ]);

					DummieTrading\TelegramMessage::add([
						'signal_provider_id' => DummieTrading\SignalProvider::TEST_SIGNAL_PROVIDER_ID,
						'message_id' => $result->result->message_id,
						'catalog_trading_account_id' => DummieTrading\CatalogTradingAccount::METATRADER,
						'message' => $data['message'],
						'data' => json_encode($data['signal']),
					]);

                    $data['s'] = 1;
                    $data['r'] = 'DATA_OK';
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
} else {
	$data['s'] = 0;
	$data['r'] = 'INVALID_CREDENTIALS';
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 