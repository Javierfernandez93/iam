<?php

namespace JFStudio;

use DummieTrading\TradePerUserTrading;
use JFStudio\Curl;

use DummieTrading\UserTelegram;
use DummieTrading\UserTradingAccount;

class ApiTelegramDummieTrading
{
	const BOT_API_KEY = '6053021769:AAEibN19fwFYXJ7jXni4lOb0Eef1_B5JYWs';	
	const BOT_USERNAME = 'DummieTrading';	
	const HOOK_URL = 'https://www.iam.com.mx/app/hook/telegramSignalCopier.php';	

	const configuration = [
		'set_configuration'
	];

	const commands = [
		'/start',
		'/ayuda',
		'/profit',
		'/profits',
		'/balance',
		'/drawdown',
		'/usuario',
		'/password',
	];

	public static function getUrlConfigureHook()
	{
		return "https://api.telegram.org/bot".self::BOT_API_KEY."/setWebhook?url=".self::HOOK_URL;
	}
	
	public static function configureTelegramHook(string $api_key = null)
	{
		return self::configureHook("https://api.telegram.org/bot".$api_key."/setWebhook?url=".self::HOOK_URL);
	}
	
	public static function configureHook(string $url = null) : array|string
	{
		$Curl = new Curl;
		$Curl->get($url);

		return $Curl->getResponse(true);
	}

	public static function applyConfig(array $data = null) : bool
	{
		if(in_array($data['config'],['set_configuration']))
		{
			return UserTelegram::attachChatId([
				'key' => explode("=",$data['text'])[1],
				'chat_id' => $data['chat_id']
			]);
		}

		return false;
	}

	public static function getDefaultVars(int $chat_id = null) : array
	{
		if($user_login_id = (new UserTelegram)->getUserId($chat_id))
		{
			return [
				'user_trading_account_id' => (new UserTradingAccount)->getLastTradingAccount($user_login_id),
			];
		}

		return [];
	}

	public static function getLastTrade(int $user_trading_account_id = null) : string
	{
		$profit = 0;
		$profit = (new TradePerUserTrading)->getLastTrade($user_trading_account_id);
		$profit = number_format($profit,2);

		return "Tu última operación fué de $ {$profit} USD";
	}
	
	public static function getBalance(int $user_trading_account_id = null) : string
	{
		$balance = 0;
		$balance = (new UserTradingAccount)->getBalance($user_trading_account_id);
		$balance = number_format($balance,2);

		return "Tu balance es de $ {$balance} USD";
	}
	
	public static function getDrawdown(int $user_trading_account_id = null) : string
	{
		$drawdown = 0;
		$drawdown = (new UserTradingAccount)->getDrawdown($user_trading_account_id);

		return "Tu drawdown es de {$drawdown} %";
	}

	public static function getLastTrades(int $user_trading_account_id = null) : string
	{
		if($trades = (new TradePerUserTrading)->getLastTrades($user_trading_account_id))
		{
			$text = "";

			foreach($trades as $trade)
			{
				$profit = number_format($trade['profit'],2);

				$date = date('Y-m-d',$trade['create_date']) == date('Y-m-d') ? 'Hoy' : date('Y-m-d');
				$date .= ' a las '.date('H:m',$trade['create_date']);

				$text .= "Operación {$date} $ {$profit} USD".PHP_EOL;
			}
			
			return $text;
		}

		return "No tenemos información de tus trades";
	}

	public static function sendMessage(string $text = null,int $chat_id = null)
	{
		require_once TO_ROOT . '/vendor/autoload.php';
		
		try {
			$telegram = new \Longman\TelegramBot\Telegram(self::BOT_API_KEY, self::BOT_USERNAME);

            $result = \Longman\TelegramBot\Request::sendMessage([
                'chat_id' => $chat_id,
                'text' => $text
            ]);

            return $result ? true : false;
        } catch (\Longman\TelegramBot\Exception\TelegramException $e) {
            return false;
        }
	}

	public static function getResponse(array $data = null,string $text = null)
	{
		if(self::isCommand($text))
		{
			if(in_array($text,['/ayuda','/start']))
			{
				return 'Para iniciar debes de configurar tu token con el mensaje "token=tu_token"';
			} else if($text == '/profit') {
				return self::sendMessage(self::getLastTrade($data['user_trading_account_id']),$data['chat_id']);
			} else if($text == '/profits') {
				return self::sendMessage(self::getLastTrades($data['user_trading_account_id']),$data['chat_id']);
			} else if($text == '/balance') {
				return self::sendMessage(self::getBalance($data['user_trading_account_id']),$data['chat_id']);
			} else if($text == '/drawdown') {
				return self::sendMessage(self::getDrawdown($data['user_trading_account_id']),$data['chat_id']);
			} else if($text == '/usuario') {
				return "Ingresa tu usuario de MT5";
			} else if($text == '/password') {
				return "Ingresa tu contraseña de MT5";
			} else if($text == '/token') {
			} else {
				return "Comando exitoso";
			}
		} 

		return "No reconocemos el comando o mensaje que envías";
	}

	public static function isCommand(string $text = null) : bool
	{
	  return in_array($text,self::commands);
	}

	public static function isConfig(string $text = null) : bool
	{
	  return in_array($text,self::configuration);
	}

	public static function placeOrder(string $text = null) : string|bool {
		$array = explode("=",$text);
		$data = explode(",",$array[1]);

		if(TradePerUserTrading::pushOrder([
			'symbol' => $data[0],
			'price' => $data[1],
			'buy' => $data[2],
		]))
		{
			$type = $data[2] ? 'buy' : 'sell';

			return "(MARKET) symbol {$data[0]} ({$type})";
		}

		return false;
	}

	public static function isExecution(string $text = null) : bool
	{
	  	$array = explode("=",$text);
	  
	 	return strtolower($array[0]) == 'order';
	}

	public static function getDrawdownMessageAlert(float $drawdown = null,float $initial_drawdown = null) : string
	{
		$percentajeDrawdown = round((($drawdown * 100) / $initial_drawdown),0);

		if($percentajeDrawdown > 10 && $percentajeDrawdown < 30)
		{
			return "Importante";
		} else if($percentajeDrawdown > 30 && $percentajeDrawdown < 60) {
			return "Cuidado !";
		} else if($percentajeDrawdown > 60 && $percentajeDrawdown < 100) {
			return "¡Mucho cuidado!";
		}

		return "Aviso";
	}

	public static function getDrawdownMessage(float $drawdown = null,float $initial_drawdown = null) : string {
		return self::getDrawdownMessageAlert($drawdown,$initial_drawdown)." has alcanzado un Drawdown {$drawdown} % (dd. max. {$initial_drawdown}%)";
	}	
}