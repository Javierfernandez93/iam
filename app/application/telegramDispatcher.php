<?php  define("TO_ROOT", "../../");

require_once TO_ROOT . 'system/core.php';

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
	require_once TO_ROOT . '/vendor/autoload.php';

	try { 
		try {
			if($api = (new DummieTrading\TelegramApi)->getByName('DummieTrading'))
			{
				$telegram = new Longman\TelegramBot\Telegram($api['api_key'], $api['user_name']);

				if($chat_id = (new DummieTrading\UserTelegram)->getChatId($UserLogin->company_id))
				{
					DummieTrading\IpnTelegram::add($data);
				
					$message = $data['message'];

					$data['message'] = JFStudio\Dispatcher::dispatcher([
						'api' => $api,
						'message' => $message,
						'catalog_trading_account_id' => isset($data['catalog_trading_account_id']) ? $data['catalog_trading_account_id'] : DummieTrading\CatalogTradingAccount::METATRADER,
						'chat_id' => $chat_id,
					]);
					
					$data['messageOriginal'] = $data['message'];
					$data['message'] = $data['message'] ? nl2br($data['message']) : $data['message'];
					
					$data['s'] = 1;
					$data['r'] = 'DATA_OK';
				} else {
					$data['message'] = DummieTrading\Parser::doParser(JFStudio\RandomReply::getRandomReply('no_telegram_connected'),[
						...[
							'names' => trim($UserLogin->getNames())
						],
						...DummieTrading\UserTelegram::getTelegramConfigForConnection($UserLogin->company_id)
					]);
					
					$data['messageOriginal'] = $data['message'];
					$data['s'] = 1;
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