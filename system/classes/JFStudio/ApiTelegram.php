<?php

namespace JFStudio;

use JFStudio\Curl;

use HCStudio\Connection;

class ApiTelegram
{
	// const BOT_API_KEY = '6093891258:AAE3K1WQ03ieLD3nEZI1wNreXQCkYaAdWGw';	
	const BOT_API_KEY = '6053021769:AAEibN19fwFYXJ7jXni4lOb0Eef1_B5JYWs';
	// const BOT_USERNAME = 'DummieTrading';	
	const BOT_USERNAME = 'DummieTrading';
	const HOOK_URL = '/app/hook/telegram.php';

	const ORDER_MARKET = ['order', 'market', 'mercado', 'orden'];
	

	const configuration = [
		'set_configuration',
		'set_follow'
	];

	const copyTrading = [
		'copy_trading',
	];

	const commands = [
		'/start',
		'/ayuda',
		'/profit',
		'/profits',
		'/balance',
		'/drawdown',
		'/variables',
		'/initial_balance',
		'/usuario',
		'/connect',
		'/accounts',
		'/gain',
		'/deals',
		'/account',
		'/signalsProviders',
		'/password',
	];

	public static function getHookUrl()
	{
		return Connection::getMainPath().self::HOOK_URL;
	}

	public static function getUrlConfigureHook()
	{
		return "https://api.telegram.org/bot" . self::BOT_API_KEY . "/setWebhook?url=" . self::HOOK_URL;
	}

	public static function configureHook(): array|string
	{
		$Curl = new Curl;
		$Curl->get(self::getUrlConfigureHook());

		return $Curl->getResponse(true);
	}

	public static function sendMessage(array $data = null)
	{
		require_once TO_ROOT . '/vendor/autoload.php';

		try {
			$telegram = $telegram ?? new \Longman\TelegramBot\Telegram($data['api']['api_key'], $data['api']['user_name']);

			$messageJson = json_decode($data['message'],true);

			if($messageJson)
			{
				if(isset($messageJson['video']))
				{
					return \Longman\TelegramBot\Request::sendVideo([
						'chat_id' => $data['chat_id'],
						'video' => $messageJson['video'],
						'caption' => $messageJson['caption']
					]);
				} if(str_contains($data['message'], '.jpg') || str_contains($data['message'], '.png')) {
					return \Longman\TelegramBot\Request::sendPhoto([
						'chat_id' => $data['chat_id'],
						'photo'=> $data['message'],
					]);
				} 
			} else {
				return \Longman\TelegramBot\Request::sendMessage([
					'chat_id' => $data['chat_id'],
					'text' => $data['message']
				]);
			}

		} catch (\Longman\TelegramBot\Exception\TelegramException $e) {
			return false;
		}
	}
}
