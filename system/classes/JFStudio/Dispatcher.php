<?php

namespace JFStudio;

use DummieTrading\UserTelegram;
use DummieTrading\UserData;
use DummieTrading\UserTradingAccount;
use DummieTrading\TradePerUserTrading;
use DummieTrading\TelegramMessage;
use DummieTrading\UserSignalProvider;
use DummieTrading\SignalProvider;
use DummieTrading\CatalogTradingAccount;
use DummieTrading\CatalogSignalProvider;
use DummieTrading\UserTemp;
use DummieTrading\UserVar;
use DummieTrading\Parser;

use JFStudio\ML;
use JFStudio\ApiTelegram;
use JFStudio\OrderIdentificator;
use JFStudio\OrderKeysIdentificator;
use JFStudio\RandomReply;

use Api\MT4;

use Exception;

class Dispatcher
{
    const configuration = [
		'set_configuration',
		'set_follow'
	];

	const copyTrading = [
		'copy_trading',
	];

    const BUY_STRINGS = ['buy', 'compra', 'comprar'];
    const ORDER_MARKET = ['order', 'market', 'mercado', 'orden', 'oco'];
    const ORDER_QUICK = ['agent.placeOrderQuick'];
    const CLOSE_ORDER = ['cerrar', 'close'];
    const FOLLOW_PROVIDER = ['follow_provider', 'seguir_proveedor'];
    const PRICE = ['price', 'precio'];
    
	public static function isCommand(string $text = null): bool
	{
		return in_array($text, ApiTelegram::commands);
	}

	public static function isConfig(string $text = null): bool
	{
		return in_array($text, ApiTelegram::configuration);
	}

	public static function isCopy(string $text = null): bool
	{
		return in_array($text, ApiTelegram::copyTrading);
	}

	public static function convertBuyStringToInt(string $text = null): int
	{
		return in_array(strtolower($text), self::BUY_STRINGS) ? 1 : 0;
	}

    /* MT4 FUNCTIONS */

    public static function getGains(array $data = null): string
	{
		if(isset($data['id']))
		{
			$initialBalance = (new UserTradingAccount)->getInitialBalance($data['user_trading_account_id']);

			if($response = MT4::getAccount($data['id']))
			{
				$response['account']['drawdown'] = MT4::calculateDrawDown($response['account']['balance'],$response['account']['equity']);
				$response['account']['user_trading_account_id'] = $data['user_trading_account_id'];
			
				UserTradingAccount::updateData($response['account']);
			}

			$balances = (new UserTradingAccount)->getBalances($data['user_trading_account_id']);

			if(isset($balances))
			{
				$gain = $balances['equity'] - $initialBalance;
				$gainPercentaje = abs(100 - (round((($balances['equity'] * 100) / $initialBalance), 2)));

				$reply = ($gain > 0) ? 'view_profit' : 'view_loss';

				return Parser::doParser(RandomReply::getRandomReply($reply),[
					'gain' => number_format($gain,2),
					'gainPercentaje' => number_format($gainPercentaje,2),
					'login' => $data['login']
				]);
			}
		}
		
		return RandomReply::getRandomReply("no_info_about_gains");
	}

    public static function getBalance(array $data = null): string
	{
		if(isset($data['id']))
		{
			if($response = MT4::getAccount($data['id']))
			{
				$response['account']['drawdown'] = MT4::calculateDrawDown($response['account']['balance'],$response['account']['equity']);
				$response['account']['user_trading_account_id'] = $data['user_trading_account_id'];
			
				UserTradingAccount::updateData($response['account']);	
			}

			if($balances = (new UserTradingAccount)->getBalances($data['user_trading_account_id']))
			{
				return Parser::doParser(RandomReply::getRandomReply("view_balances"),[
					'balance' => number_format($balances['balance'],2),
					'equity' => number_format($balances['equity'],2),
					'login' => $data['login']
				]);
			}
		}

		return RandomReply::getRandomReply("no_info_about_balance");
	}

   
    public static function getInitialBalance(array $data = null): string
	{
		$balance = 0;
		$balance = (new UserTradingAccount)->getInitialBalance($data['user_trading_account_id']);

		return Parser::doParser(RandomReply::getRandomReply("current_balance"),[
			'balance' => number_format($balance,2),
			'login' => $data['login']
		]);
	}
   
	public static function getVariables(array $data = null): string
	{
		if($variables = (new UserVar)->getVarInfoFormatted($data['user_login_id']))
		{
			return "Tus variables guardadas son: \n".Parser::parserMultidimensionalArray($variables);
		}

		return RandomReply::getRandomReply("no_info_about_variables");
	}

	public static function getDrawdown(array $data = null): string
	{
		$drawdown = 0;
		$drawdown = (new UserTradingAccount)->getDrawdown($data['user_trading_account_id']);

		return Parser::doParser(RandomReply::getRandomReply("current_drawdown"),[
			'drawdown' => number_format($drawdown,2),
			'login' => $data['login']
		]);
	}

	public static function getLastTrade(array $data = null): string
	{
		$profit = 0;
		$profit = (new TradePerUserTrading)->getLastTrade($data['user_trading_account_id']);
		
		return Parser::doParser(RandomReply::getRandomReply("last_trade"),[
			'profit' => number_format($profit,2),
			'login' => $data['login']
		]);
	}
	
	public static function getDeals(array $data = null): string
	{
		if ($response = MT4::getPositions($data['id'])) 
		{
			if($response['s'] == 1)
			{
				if($response['positions'])
				{
					$positions = [];
	
					foreach($response['positions'] as $position)
					{
						$tipo = $position['type'] == 'POSITION_TYPE_BUY' ? 'compra' : 'venta';
	
						$positions[] = "Orden {$position['id']}\nSimbolo {$position['symbol']}\nVolumen {$position['volume']}\nProfit {$position['profit']}\nLado {$tipo}";
					}
	
					$totalProfit = number_format(array_sum(array_column($response['positions'],'profit')),2);
					$partialSide = $totalProfit > 0 ? 'Ganancias' : 'Perdida';
	
					return Parser::doParser(RandomReply::getRandomReply("open_orders_list"),[
						"info" => implode(PHP_EOL.PHP_EOL,$positions),
						"side" => $partialSide,
						"profit" => $totalProfit
					]);
				} else {
					return RandomReply::getRandomReply("no_positions");
				}
			}
		}

		return RandomReply::getRandomReply("no_info_about_last_trades");
	}
	
	public static function getAccount(array $data = null): string
	{
		if($account = (new UserTradingAccount)->getLastTradingAccountData($data['user_login_id']))
		{
			return "La última que añadiste fue: \n".Parser::parserArray($account);
		}

		return RandomReply::getRandomReply("account_not_connected");
	}
	
	public static function getAccounts(array $data = null): string
	{
		if($accounts = (new UserTradingAccount)->getTradingAccountData($data['user_login_id']))
		{
			return "Cuentas conectadas ".sizeof($accounts).": \n".Parser::parserMultidimensionalArray($accounts);
		}

		return RandomReply::getRandomReply("account_not_connected");
	}

    public static function getSignalsProvider(array $data = null): string
	{
		if($singalsProviders = (new UserSignalProvider)->getAllList($data['user_login_id'],CatalogSignalProvider::SEMI_COPY)) 
		{
			$phrases = [];

			foreach ($singalsProviders as $provider) {
				$phrases[] = "👉 Operativa de {$provider['name']} que opera en {$provider['type']} su identificador es {$provider['signal_provider_id']} ".($provider['isFollowing'] ? '(estás siguiendo está señal)' : '(puedes seguir esta señal)');
			}

			return RandomReply::getRandomReply("signals_providers").implode(PHP_EOL.PHP_EOL,$phrases).RandomReply::getRandomReply("follow_singals");
		}

		return RandomReply::getRandomReply("no_info_about_last_providers");
	}

    public static function getLastTrades(array $data = null): string
	{
		if ($trades = (new TradePerUserTrading)->getLastTrades($data['user_trading_account_id'])) 
		{
			$text = "";

			foreach ($trades as $trade) {
				$profit = number_format($trade['profit'], 2);

				$date = date('Y-m-d', $trade['create_date']) == date('Y-m-d') ? 'Hoy' : date('Y-m-d');
				$date .= ' a las ' . date('H:m', $trade['create_date']);

				$text .= "#{$trade['ticket']} de {$date} - PROFIT $ {$profit} USD" . PHP_EOL;
			}

			$text .= PHP_EOL;
			$text .= "👉 Cuenta {$data['login']}";

			return $text;
		}

		return RandomReply::getRandomReply("no_info_about_last_trades");
	}

    public static function getResponse(array $data = null) : array
	{
		if (self::isCommand($data['message'])) 
		{
			if (in_array($data['message'], ['/ayuda', '/start'])) 
			{
				$response = ApiTelegram::sendMessage([
					'api' => $data['api'],
					'message' => RandomReply::getRandomReply("help"),
					'chat_id' => $data['chat_id']
				]);
			} else if ($data['message'] == '/accounts') {
				$response = ApiTelegram::sendMessage([
					'api' => $data['api'],
					'message' => self::getAccounts($data),
					'chat_id' => $data['chat_id']
				]);
			} else if ($data['message'] == '/account') {
				$response = ApiTelegram::sendMessage([
					'api' => $data['api'],
					'message' => self::getAccount($data),
					'chat_id' => $data['chat_id']
				]);
			} else if ($data['message'] == '/gain') {
				$response = ApiTelegram::sendMessage([
					'api' => $data['api'],
					'message' => self::getGains($data),
					'chat_id' => $data['chat_id']
				]);
			} else if ($data['message'] == '/deals') {
				$response = ApiTelegram::sendMessage([
					'api' => $data['api'],
					'message' => self::getDeals($data),
					'chat_id' => $data['chat_id']
				]);
			} else if ($data['message'] == '/profit') {
				$response = ApiTelegram::sendMessage([
					'api' => $data['api'],
					'message' => self::getLastTrade($data),
					'chat_id' => $data['chat_id']
				]);
			} else if ($data['message'] == '/profits') {
				$response = ApiTelegram::sendMessage([
					'api' => $data['api'],
					'message' => self::getLastTrades($data),
					'chat_id' => $data['chat_id']
				]);
			} else if ($data['message'] == '/signalsProviders') {
				$response = ApiTelegram::sendMessage([
					'api' => $data['api'],
					'message' => self::getSignalsProvider($data),
					'chat_id' => $data['chat_id']
				]);
			} else if ($data['message'] == '/initial_balance') {
				$response = ApiTelegram::sendMessage([
					'api' => $data['api'],
					'message' => self::getInitialBalance($data),
					'chat_id' => $data['chat_id']
				]);
			} else if ($data['message'] == '/balance') {
				$response = ApiTelegram::sendMessage([
					'api' => $data['api'],
					'message' => self::getBalance($data),
					'chat_id' => $data['chat_id']
				]);
			} else if ($data['message'] == '/variables') {
				$response = ApiTelegram::sendMessage([
					'api' => $data['api'],
					'message' => self::getVariables($data),
					'chat_id' => $data['chat_id']
				]);
			} else if ($data['message'] == '/drawdown') {
				$response = ApiTelegram::sendMessage([
					'api' => $data['api'],
					'message' => self::getDrawdown($data),
					'chat_id' => $data['chat_id']
				]);
			} else if ($data['message'] == '/connect') {
				$response = ApiTelegram::sendMessage([
					'api' => $data['api'],
					'message' => 'Connectando',
					'chat_id' => $data['chat_id']
				]);
			} 
		} else {
			$response = ApiTelegram::sendMessage([
				'api' => $data['api'],
				'message' => $data['message'],
				'chat_id' => $data['chat_id']
			]);
		}

		if(isset($response->result->caption))
		{
			$textRespose = $response->result->caption;
		} else if(isset($response->result->text)) {
			$textRespose = $response->result->text;
		}

		return [
			'result' => $response->ok,
			'response' => $textRespose
		];
	}
    
    public static function getDefaultVars(int $chat_id = null,int $catalog_trading_account_id = null): array
	{
		if ($user_login_id = (new UserTelegram)->getUserId($chat_id)) 
		{
			$UserTradingAccount = new UserTradingAccount;
			
			if($user_trading_account_id = $UserTradingAccount->getTradingAccountFollowing($user_login_id,$catalog_trading_account_id))
			{
				$id = $UserTradingAccount->getIdById($user_trading_account_id);
				$login = $UserTradingAccount->getAccountById($user_trading_account_id);
			}

			return [
				'user_login_id' => $user_login_id,
				'catalog_tag_intent_id' => UserTemp::getVar($user_login_id,'catalog_tag_intent_id'),
				'user_trading_account_id' => $user_trading_account_id ?? 0,
				'names' => (new UserData)->getName($user_login_id),
				'id' => isset($id) ? $id : 0,
				'login' => isset($login) ? $login : 0
			];
		}

		return [];
	}

    public static function getMeessagePartsFromCopy(string $message = null): array|bool
	{
		$message = explode("=",trim($message))[1];

		if(str_contains($message,","))
		{
			return OrderKeysIdentificator::identifyKeysPlaceOrder(explode(",",$message));
		} else {
			return [
				'message_id' => $message
			];
		}

		return false;
	}

    public static function dispatchOrderMarket(array $data = null): bool|string
	{
		$data['market_type'] = isset($data['market_type']) ? $data['market_type'] : 'forex';

		if($data['market_type'] == 'forex')
		{
			$data['lotage'] = $data['quantity'];
			$data['buy'] = $data['side'] == 'buy' ? 1 : 0;
			
			if ($response = MT4::createMarketOrder($data)) {
			
				if($response['s'] == 1)
				{
					$openPrice = isset($response['order']['origQty']) ? $response['order']['origQty'] : $response['order']['openPrice'];

					return "Listo! Hemos enviado la orden {$response['order']['orderId']} por ".number_format($openPrice,2);
				} else {
					return "Error al procesar la orden {$data['signal']['data']['symbol']} {$data['signal']['data']['quantity']}";
				}
			}
		} else if($data['market_type'] == 'crypto') {
		 
			if($response = ApiBinance::createMarketOrder($data)) {
				if($response['s'] == 1)
				{
					return "Listo! Hemos enviado la orden {$response['order']['orderId']} por {$response['order']['origQty']}";
				} else {
					return "Error al procesar la orden {$data['signal']['data']['symbol']} {$data['signal']['data']['quantity']}";
				}
			}
		}
	}
	
    public static function dispatchOrderOco(array $data = null): bool|string
	{
		if($data['market_type'] == CatalogTradingAccount::METATRADER)
		{
			$data['lotage'] = $data['quantity'];
			$data['buy'] = $data['side'] == 'buy' ? 1 : 0;

			if($response = Mt4::createOrderOco($data))
			{
				if($response['s'] == 1)
				{
					return "Listo! Hemos enviado la orden Ticket number #{$response['order']['orderId']}";
				} else if($response['r'] == 'NOT_META_API') {
					return "No estás conectado a Meta trader con esta cuenta";
				} else if($response['r'] == 'NOT_META_API') {
					return "Error al procesar la orden";
				} else if($response['r'] == 'ERR_INVALID_STOPS') {
					return Parser::doParser(RandomReply::getRandomReply("invalid_stops"),$data);
				} else {
					$reason = isset($response['r']) ? $response['r'] : '';
					return "¡Oh no! hubo un problema al conectar con {$data['apiKey']}. Es probable que no estés conectado a MetaTrader. Revisa tu cuenta [{$reason}]";
				}
			}
		} else if($data['market_type'] == CatalogTradingAccount::BINANCE) {
		 
			if($response = ApiBinance::createMarketOco($data)) 
			{
				if($response['s'] == 1)
				{
					return "Listo! Hemos enviado la orden {$response['order']['orderListId']}";
				} else {
					return "Error al procesar la orden {$data['signal']['data']['symbol']} {$data['signal']['data']['quantity']}";
				}
			}
		}
	}

    public static function copySignal(array $data = null): bool|string
	{
		if($data['messageParts'] = self::getMeessagePartsFromCopy($data['text']))
		{
			$quantity = 0;

			if(isset($data['messageParts']['quantity']))
			{
				$quantity = $data['messageParts']['quantity'];
			} else {
				$quantity = (new UserVar)->getVarValueByIdentificator($data['user_login_id'],'lotage');

				$data['messageParts']['quantity'] = $quantity;
			}

			if(isset($quantity))
			{
				if(isset($quantity) > 0)
				{
					$data['query'] = ML::getResponseIATag($data['messageParts']['message']);

					if($data['signal'] = (new TelegramMessage)->getByMessageId($data['messageParts']['message_id']))
					{
						$data['signal']['data'] = json_decode($data['signal']['data'],true);

						// adding the quantify sent by user
						$data['signal']['data']['quantity'] = $quantity;

						if($user_login_id = (new UserTelegram)->getUserId($data['chat_id']))
						{
							if($data['account'] = (new UserTradingAccount)->getAccountFilterByCatalogTradingAccount($user_login_id,$data['signal']['catalog_trading_account_id']))
							{
								$data['signal']['data']['symbol'] = UserTradingAccount::getFixedSymbol($data['account']['user_trading_account_id'], $data['signal']['data']['symbol']);

								if($data['signal']['data']['type'] == ParserOrder::OCO)
								{
									if($text = self::dispatchOrderOco([
										'id' => $data['account']['id'],
										'symbol' => $data['signal']['data']['symbol'],
										'side' => $data['signal']['data']['side'],
										'price' => $data['signal']['data']['price'],
										'stopPrice' => $data['signal']['data']['stopPrice'],
										'market_type' => $data['signal']['data']['market_type'],
										'priceEntrace' => $data['signal']['data']['priceEntrace'],
										'stopLimitPrice' => $data['signal']['data']['stopLimitPrice'],
										'stopPrice' => $data['signal']['data']['stopPrice'],
										'takeProfit' => $data['signal']['data']['takeProfit'],
										'quantity' => $data['signal']['data']['quantity'],
										'apiKey' => $data['account']['login'],
										'apiSecret' => $data['account']['password'],
									])) {
										return $text;
									}
								} else if($data['signal']['data']['type'] == ParserOrder::MARKET) {
									if($text = self::dispatchOrderMarket([
										'id' => $data['account']['id'],
										'symbol' => $data['signal']['data']['symbol'],
										'side' => $data['signal']['data']['side'],
										'quantity' => $data['signal']['data']['quantity'],
										'apiKey' => $data['account']['login'],
										'apiSecret' => $data['account']['password'],
									])) {
										return $text;
									}
								}
							} else {
								return "No encontramos ninguna cuenta de MetaTrader conectada a DummieTrading.\n\nPuedes ver nuestra guía si tienes dudas www.zuum.link/BienvenidoDummieTrading";
							}
						} else {
							return "Debes de conectar tu Telegram a DummieTrading.\n\nPuedes hacerlo con nuestra guía www.zuum.link/BienvenidoDummieTrading";
						}
					} else {
						return "No hemos encontrado la señal {$data['message_id']}, asegúrate que estés respondiendo correctamente a la señal. Debes de responder al mensaje de la señal al menos con un 'si adelante', 'si procede' e incluir el lotaje si no lo has ingresado";
					}
				} else {
					return "El lotaje debe de ser mayor a 0";
				}
			} else {
				return "Debes ingresar un lotaje";
			}
		}


		return "No hemos podido copiar la señal";
	}

    public static function applyConfig(array $data = null): bool|string
	{
		if (in_array($data['config'], ['set_configuration'])) {
			$data['key'] = '';
			if (str_contains($data['text'], '/start')) {
				$data['key'] = explode(" ", $data['text'])[1];
			} else {
				$data['key'] = explode("=", $data['text'])[1];
			}

			return UserTelegram::attachChatId([
				'key' => $data['key'],
				'chat_id' => $data['chat_id']
			]) ? "¡Gracias {$data['names']}!." . PHP_EOL . PHP_EOL . "Ya hemos vinculado DummieTrading a tu cuenta." : false;
		} else if (in_array($data['config'], ['set_follow'])) {

			if(str_contains($data['text'], '='))
			{
				$login = explode("=", $data['text'])[1];
			} else {
				$words = explode(" ",$data['text']);
				$words = array_map(function($word){
					return "'{$word}'";
				},$words);

				$words = implode(",",$words);

				if($loginAux = (new UserTradingAccount)->searchAccountByLoginOrAlias($words,$data['user_login_id']))
				{
					$login = $loginAux;
				}
			}
			
			if(isset($login))
			{
				if(UserTradingAccount::follow([
					'login' => $login,
					'chat_id' => $data['chat_id']
				]))
				{
					return Parser::doParser(RandomReply::getRandomReply('account_changed'),[
						'login' => $login
					]);
				}
			} else {
				return RandomReply::getRandomReply('no_loggin_set_for_change_account');
			}
		}

		return false;
	}

    public static function isExecution(string $text = null): bool
	{
		$array = explode("=", $text);

		return in_array(strtolower($array[0]), self::ORDER_MARKET);
	}
    
	public static function isExecutionQuick(string $query = null): bool
	{
		return in_array($query, self::ORDER_QUICK);
	}
  
	public static function isCloseOrder(string $text = null): bool
	{
		$array = explode("=", $text);

		return in_array(strtolower($array[0]), self::CLOSE_ORDER);
	}

	public static function isFollowProvider(string $text = null): bool
	{
		$array = explode("=", $text);

		return in_array(strtolower($array[0]), self::FOLLOW_PROVIDER);
	}
	
	public static function isGetMarketPrice(string $text = null): bool
	{
		$array = explode("=", $text);

		return in_array(strtolower($array[0]), self::PRICE);
	}

	public static function isCloseOrders(string $text = null): bool
	{
		return str_contains($text, "close_orders");
	}

	public static function closeMarketOrder(array $data = null): string|bool
	{
		$orderId = OrderKeysIdentificator::identifyOrderId($data['text']);

		if ($response = MT4::closeOrder([
			'id' => $data['id'],
			'orderId' => $orderId
		])) {
			// d($response);

			if ($response['s'] === 1) 
			{
				if (TradePerUserTrading::closeOrderByTicketId($orderId)) {
					return "¡Bien! hemos cerrado la orden #{$orderId}";
				} else {
					return "¡Bien! hemos cerrado la orden #{$orderId}";
				}
			}  else if($response['r'] == 'NOT_ORDER_CLOSED') {
				return "No pudimos cerrar la orden #{$orderId}";
			}  else if($response['r'] == 'Position not found') {
				return "No encontramos la orden #{$orderId}";
			}
		}

		return false;
	}
	
	public static function _getMarketPrice(array $data = null): string|bool
	{
		$symbol = OrderKeysIdentificator::identifySymbol($data['text']);
		$symbol = UserTradingAccount::getFixedSymbol($data['user_trading_account_id'],$symbol);
		
		if ($response = MT4::getMarketPrice([
			'id' => $data['id'],
			'symbol' => $symbol
		])) {
			if ($response['s'] === 1) 
			{
				return Parser::doParser(RandomReply::getRandomReply("symbol_price"),[
					'symbol' => $response['price']['symbol'],
					'ask' => number_format($response['price']['ask'],2),
					'bid' => number_format($response['price']['bid'],2)
				]);
			} else {
				return Parser::doParser(RandomReply::getRandomReply("no_price_for_symbol"),[
					"symbol" => $response['price']['symbol'],
				]);
			}
		}

		return false;
	}

	public static function closeMarketOrders(array $data = null): string|bool
	{
		if ($response = MT4::closeOrders($data['id'])) {
			
			if ($response['s'] === 1) 
			{
				// if (TradePerUserTrading::closeOrderByTicketId($orderId)) {
					return "¡Bien! hemos cerrado las ordenes";
				// }
			} else if($response['r'] == 'NOT_ORDERS_PENDING') {
				return "No tienes ordenes pendientes";
			}
		}

		return false;
	}
	
	public static function closeMarketOrdersWithBenefit(array $data = null): string|bool
	{
		if ($response = MT4::closeOrdersWithBenefit($data['id'])) {
			
			if ($response['s'] === 1) 
			{
				// if (TradePerUserTrading::closeOrderByTicketId($orderId)) {
					return "¡Bien! hemos cerrado las ordenes";
				// }
			} else if($response['r'] == 'NOT_ORDERS_PENDING') {
				return "No tienes ordenes pendientes";
			}
		}

		return false;
	}
	
	public static function closeMarketOrdersWithLoss(array $data = null): string|bool
	{
		if ($response = MT4::closeOrdersWithLoss($data['id'])) {
			
			if ($response['s'] === 1) 
			{
				// if (TradePerUserTrading::closeOrderByTicketId($orderId)) {
					return "¡Bien! hemos cerrado las ordenes";
				// }
			} else if($response['r'] == 'NOT_ORDERS_PENDING') {
				return "No tienes ordenes pendientes";
			}
		}

		return false;
	}

	public static function placeOcoOrder(array $data = null): string|bool
	{
		if(!isset($data['keys']))
		{
			$array = explode("=", $data['text']);
			$_data = explode(",", $array[1]);

			$keys = OrderKeysIdentificator::identifyKeysOco($_data);
			$keys['buy'] = self::convertBuyStringToInt($keys['operation']);
		} else {
			$keys = $data['keys'];
		}

		if(!isset($keys['symbol']))
		{
			$keys['symbol'] = OrderKeysIdentificator::identifySymbol($data['text']);
		}

		if ($keys['lotage'] > 0) 
		{	
			$keys['symbol'] = UserTradingAccount::getFixedSymbol($data['user_trading_account_id'],$keys['symbol']);

			if ($response = MT4::createOrderOco([
				...['id' => $data['id']],
				...$keys
			])) {
				if ($response['s'] === 1) 
				{
					if (TradePerUserTrading::add([
						'user_trading_account_id' => $data['user_trading_account_id'],
						'symbol' => $keys['symbol'],
						'buy' => $keys['buy'],
						'lotage' => $keys['lotage'],
						'take_profit' => $keys['takeProfit'],
						'stop_loss' => $keys['stopPrice'],
						'ticket' => $response['order']['orderId'],
					])) {
						$keys['orderId'] = $response['order']['orderId'];
						$keys['symbol'] = strtoupper($keys['symbol']);

						return Parser::doParser(RandomReply::getRandomReply("order_oco_placed"),$keys);
					}
				} else if($response['r'] == 'ERR_INVALID_STOPS') {
					return Parser::doParser(RandomReply::getRandomReply("invalid_stops"),$keys);
				} else if($response['r'] == 'NOT_META_API') {
					return 'Not api workings';
				}
			} else {
				return 'Not api workings';
			}
		} else {
			return 'Not api workings';
		}

		return false;
	}

	public static function placeMarketOrder(array $data = null): string|bool
	{
		if(!isset($data['keys']))
		{
			$array = explode("=", $data['text']);
			$_data = explode(",", $array[1]);
			
			$keys = OrderKeysIdentificator::identifyKeys($_data);
			$keys['buy'] = self::convertBuyStringToInt($keys['operation']);
		} else {
			$keys = $data['keys'];
			$keys['operation'] = $data['keys']['buy'] ? 'buy' : 'sell'; 
		}

		if(!isset($keys['lotage']))
		{
			$keys['lotage'] = (new UserVar)->getVarValueByIdentificator($data['user_login_id'],'lotage');
		}

		if(!isset($keys['symbol']))
		{
			$keys['symbol'] = OrderKeysIdentificator::identifySymbol($data['text']);
		}

		if (isset($keys['lotage']) && $keys['lotage'] > 0) 
		{	
			$keys['symbol'] = strtoupper($keys['symbol']);
			$keys['symbol'] = UserTradingAccount::getFixedSymbol($data['user_trading_account_id'],$keys['symbol']);

			if ($response = MT4::createMarketOrder([
				...['id' => $data['id']],
				...$keys
				])) 
			{
				if ($response['s'] == 1) 
				{
					if (TradePerUserTrading::add([
						'user_trading_account_id' => $data['user_trading_account_id'],
						'symbol' => $keys['symbol'],
						'buy' => $keys['buy'],
						'lotage' => $keys['lotage'],
						'take_profit' => isset($keys['takeProfit']) ?  $keys['takeProfit'] : 0,
						'stop_loss' => isset($keys['stopPrice']) ?  $keys['stopPrice'] : 0,
						'ticket' => $response['order']['orderId'],
						'open_price' => isset($response['order']['openPrice']) ? $response['order']['openPrice'] : 0,
						'profit' => isset($response['order']['profit']) ? $response['order']['profit'] : 0,
					])) {
						$replys = ["¡Perfecto!","¡Increíble!","¡Genial!","¡Bien!"];

						$priceEntraceText = isset($response['order']['openPrice']) ? "\nPrecio entrada: {$response['order']['openPrice']}" : "";

						return "🥳 ".$replys[rand(0,sizeof($replys)-1)]." procesamos tu orden:\n\n{$keys['symbol']}{$priceEntraceText}\nLado: {$keys['operation']}\nLotaje: {$keys['lotage']}\nTicket#: {$response['order']['orderId']}";
					}
				} else if(isset($response['e'])) {
					if($response['e'] == 'ERR_MARKET_CLOSED')
					{
						return "No puedes hacer operaciones porque el mercado está cerrado";
					} 
				} else if($response['r'] == 'NOT_META_API') {
					return "No tenemos conexión a tu cuenta de MetaTrader";
				}
			} else {
				return RandomReply::getRandomReply("no_communication");
			}
		} else {
			return RandomReply::getRandomReply("no_lotage");
		}

		return false;
	}

	public static function placeOrderQuick(array $data = null): string|bool
	{
		$keys = OrderIdentificator::identify($data['text']);
		
		if($keys)
		{
			if((new UserTradingAccount)->isAccountInProgress($data['user_trading_account_id']))
			{
				$variables = array_column($keys,"variable");
				$values = array_column($keys,"value");

				$side = OrderIdentificator::getVarIndexValue([
					"variables" => $variables,
					"values" => $values,
					"variable" => 'side'
				]);
				
				$stopLoss = OrderIdentificator::getVarIndexValue([
					"variables" => $variables,
					"values" => $values,
					"variable" => 'stopLoss'
				]);
				
				if(in_array($side,['venta','vender','sell'])) {
					$buy = 0;
				} else {
					$buy = 1;
				}

				$keys[] = [
					'variable' => 'buy',
					'value' => $buy
				];

				if($stopLoss)
				{
					$keys[] = [
						'variable' => 'stopPrice',
						'value' => $stopLoss
					];
				}

				if(in_array('stopLoss',$variables) && in_array('takeProfit',$variables))
				{
					return self::placeOcoOrder([
						...$data,
						...["keys" => OrderIdentificator::asSingleArray($keys)]
					]);
				} else {
					return self::placeMarketOrder([
						...$data,
						...["keys" => OrderIdentificator::asSingleArray($keys)]
					]);
				}

				// $orderType = strtolower(explode("=",$data['text'])[0]);
				// if(in_array($orderType,['oco','order']))
				// {
				// 	return self::placeOcoOrder($data);
				// } else if(in_array($orderType,['market'])) {
				// 	return self::placeMarketOrder($data);
				// }
			} 
	
			$login = (new UserTradingAccount)->getAccountById($data['user_trading_account_id']);
	
			return Parser::doParser(RandomReply::getRandomReply("account_disabled"),[
				'login' => $login,
			]);
		}
	}

	public static function placeOrder(array $data = null): string|bool
	{
		if((new UserTradingAccount)->isAccountInProgress($data['user_trading_account_id']))
		{
			$orderType = strtolower(explode("=",$data['text'])[0]);

			if(in_array($orderType,['oco','order']))
			{
				return self::placeOcoOrder($data);
			} else if(in_array($orderType,['market'])) {
				return self::placeMarketOrder($data);
			}
		} 

		$login = (new UserTradingAccount)->getAccountById($data['user_trading_account_id']);

		return Parser::doParser(RandomReply::getRandomReply("account_disabled"),[
			'login' => $login,
		]);
	}
	
	public static function closeOrder(array $data = null): string|bool
	{
		if((new UserTradingAccount)->isAccountInProgress($data['user_trading_account_id']))
		{
			if(str_contains($data['text'],'close'))
			{
				return self::closeMarketOrder($data);
			}
		} 

		$login = (new UserTradingAccount)->getAccountById($data['user_trading_account_id']);

		return Parser::doParser(RandomReply::getRandomReply("account_disabled"),[
			'login' => $login,
		]);
	}
	
	public static function getMarketPrice(array $data = null): string|bool
	{
		if((new UserTradingAccount)->isAccountInProgress($data['user_trading_account_id']))
		{
			if(str_contains($data['text'],'price'))
			{
				return self::_getMarketPrice($data);
			}
		} 

		$login = (new UserTradingAccount)->getAccountById($data['user_trading_account_id']);

		return Parser::doParser(RandomReply::getRandomReply("account_disabled"),[
			'login' => $login,
		]);
	}
	
	public static function followProvider(array $data = null): string|bool
	{
		if((new UserTradingAccount)->isAccountInProgress($data['user_trading_account_id']))
		{
			if(str_contains($data['text'],'follow_provider'))
			{
				if($signal_provider_id = OrderKeysIdentificator::identifyOrderId($data['text']))
				{
					if($signalProvider = (new SignalProvider)->getName($signal_provider_id))
					{
						if(!(new UserSignalProvider)->isFollowing($data['user_login_id'],$signal_provider_id))
						{
							if(UserSignalProvider::followSignal([
								'signal_provider_id' => $signal_provider_id,
								'user_login_id' => $data['user_login_id'],
							])) 
							{
								return Parser::doParser(RandomReply::getRandomReply("following_provider"),[
									'signalProvider' => $signalProvider
								]);
							}		
						} else {
							return Parser::doParser(RandomReply::getRandomReply("already_following"),[
								'signalProvider' => $signalProvider
							]);
						}
					}
				}
			}
		} 
	}

	public static function closeOrders(array $data = null): string|bool
	{
		if((new UserTradingAccount)->isAccountInProgress($data['user_trading_account_id']))
		{
			if($data['text'] == 'close_orders')
			{
				return self::closeMarketOrders($data);
			} else if($data['text'] == 'close_orders_benefit') {
				return self::closeMarketOrdersWithBenefit($data);
			} else if($data['text'] == 'close_orders_loss') {
				return self::closeMarketOrdersWithLoss($data);
			} 
		} 

		$login = (new UserTradingAccount)->getAccountById($data['user_trading_account_id']);

		return Parser::doParser(RandomReply::getRandomReply("account_disabled"),[
			'login' => $login,
		]);
	}

	public static function sendTypingAction(string $chat_id = null)
	{
		\Longman\TelegramBot\Request::sendChatAction([
			'chat_id' => $chat_id,
			'action'  => \Longman\TelegramBot\ChatAction::TYPING,
		]);
	}
	
	public static function clearVars(string $query = null,int $user_login_id = null)
	{
		if($query == 'revoke')
		{
			UserTemp::clearVars($user_login_id);
		}
	}	

	public static function messageMiddelware(array $data = null)
	{
		$message = $data['message'];
		
		if(self::isCommand($data['query']) || self::isCloseOrders($data['query']))
		{
			$message = $data['query'];
		} else if(str_contains($data['message'],'/start ')) {
			$message = explode(" ",$message);
			$message = "token=".$message[1];
		} else if(self::isGetMarketPrice($data['query'])) {
			$message = $data['message'];
			$message = "{$data['query']} {$message}";
		} else if(self::isCloseOrder($data['query'])) {
			$message = $data['message'];
			$message = "{$data['query']} {$message}";
		} else if(self::isFollowProvider($data['query'])) {
			$message = $data['message'];
			$message = "{$data['query']} {$message}";
		}

		return $message;
	}
	
	public static function addSpinVars(string $message = null)
	{
		$keys = OrderIdentificator::identify($message);

		if(!empty($keys)) 
		{
			$variables = array_column($keys,"variable");
			$values = array_column($keys,"value");

			if($symbol = OrderKeysIdentificator::identifySymbol($message))
			{
				$message .= " {{symbol_spinvar}}";
			}
		
			if(OrderIdentificator::getVarIndexValue([
				"variables" => $variables,
				"values" => $values,
				"variable" => "side"
			]))
			{
				$message .= " {{side_spinvar}}";
			}

			if(OrderIdentificator::getVarIndexValue([
				"variables" => $variables,
				"values" => $values,
				"variable" => "lotage"
			]))
			{
				$message .= " {{lotage_spinvar}}";
			}
		}

		return $message;
	}

	public static function queryMiddelware(array $data = null)
	{
		if(str_contains($data['message'],"market=")) {
			$data['query'] = 'place_order';
		} else if(str_contains($data['message'],"/start ")) {
			$data['query'] = 'set_configuration';
		} else if(str_contains($data['message'],"order=")) {
			$data['query'] = 'place_order_order_limit';
		} else if(str_contains($data['message'],"follow=")) {
			$data['query'] = 'set_follow';
		}

		return $data['query'];
	}

	public static function dispatcher(array $data = null)
	{
		self::sendTypingAction($data['chat_id']);

		$data['catalog_trading_account_id'] = isset($data['catalog_trading_account_id']) ? $data['catalog_trading_account_id'] : CatalogTradingAccount::METATRADER;
		$data['default_vars'] = self::getDefaultVars($data['chat_id'],$data['catalog_trading_account_id']);

		$data['default_vars']['catalog_tag_intent_id'] = isset($data['default_vars']['catalog_tag_intent_id']) ? $data['default_vars']['catalog_tag_intent_id'] : null;
		$data['default_vars']['user_login_id'] = isset($data['default_vars']['user_login_id']) ? $data['default_vars']['user_login_id'] : null;

		// for quickorders
		$data['message'] = self::addSpinVars($data['message']);
		
		$data['query'] = ML::getResponseIATag($data['message'],$data['default_vars']['catalog_tag_intent_id']);
		
		// clearing user temp data if intent trys to revoke
		self::clearVars($data['query'],$data['default_vars']['user_login_id'] ?? null);	
			
		$data['query'] = self::queryMiddelware([
			'query' => $data['query'],
			'message' => $data['message'],
		]);

		// this methods connect commands from human language
		$data['message'] = self::messageMiddelware([
			'query' => $data['query'],
			'message' => $data['message'],
		]);

		if (self::isCommand($data['message'])) {
			$response = self::getResponse([
				...$data['default_vars'],
				...[
					'api' => $data['api'],
					'chat_id' => $data['chat_id'],
					'message' => $data['message']
				]
			]);
		} else if(self::isConfig($data['query'])) {
			if ($text = self::applyConfig([
				'config' => $data['query'],
				'user_login_id' => $data['default_vars']['user_login_id'],
				'names' => $data['default_vars']['names'] ?? '',
				'text' => $data['message'],
				'chat_id' => $data['chat_id']
			])) {
				$response = \Longman\TelegramBot\Request::sendMessage([
					'chat_id' => $data['chat_id'],
					'text' => $text,
				]);

				return;
			}
		} else if(self::isCopy($data['query'])) {
			if ($text = self::copySignal([
				'config' => $data['query'],
				'user_login_id' => $data['default_vars']['user_login_id'],
				'names' => $data['default_vars']['names'],
				'text' => $data['message'],
				'chat_id' => $data['chat_id']
			])) {
				$response = \Longman\TelegramBot\Request::sendMessage([
					'chat_id' => $data['chat_id'],
					'text' => $text,
				]);
			}
		} else if (self::isFollowProvider($data['message'])) {
			if ($providerText = self::followProvider([
				'text' => $data['message'],
				'id' => $data['default_vars']['id'],
				'catalog_trading_account_id' => $data['catalog_trading_account_id'],
				'user_trading_account_id' => $data['default_vars']['user_trading_account_id'],
				'user_login_id' => $data['default_vars']['user_login_id'],
			])) {

				$response = \Longman\TelegramBot\Request::sendMessage([
					'chat_id' => $data['chat_id'],
					'text' => $providerText,
				]);
			} 
		} else if (self::isCloseOrders($data['message'])) {
			if ($orderText = self::closeOrders([
				'text' => $data['message'],
				'id' => $data['default_vars']['id'],
				'catalog_trading_account_id' => $data['catalog_trading_account_id'],
				'user_trading_account_id' => $data['default_vars']['user_trading_account_id'],
			])) {

				$response = \Longman\TelegramBot\Request::sendMessage([
					'chat_id' => $data['chat_id'],
					'text' => $orderText,
				]);
			} 
		} else if (self::isGetMarketPrice($data['message'])) {
			if ($orderText = self::getMarketPrice([
				'text' => $data['message'],
				'id' => $data['default_vars']['id'],
				'catalog_trading_account_id' => $data['catalog_trading_account_id'],
				'user_trading_account_id' => $data['default_vars']['user_trading_account_id'],
			])) {
				$response = \Longman\TelegramBot\Request::sendMessage([
					'chat_id' => $data['chat_id'],
					'text' => $orderText,
				]);
			} 
		} else if (self::isCloseOrder($data['message'])) {
			if ($orderText = self::closeOrder([
				'text' => $data['message'],
				'id' => $data['default_vars']['id'],
				'catalog_trading_account_id' => $data['catalog_trading_account_id'],
				'user_trading_account_id' => $data['default_vars']['user_trading_account_id'],
			])) {

				$response = \Longman\TelegramBot\Request::sendMessage([
					'chat_id' => $data['chat_id'],
					'text' => $orderText,
				]);
			} 
		} else if (self::isExecutionQuick($data['query'])) {
			if ($orderText = self::placeOrderQuick([
				'text' => $data['message'],
				'id' => $data['default_vars']['id'],
				'user_login_id' => $data['default_vars']['user_login_id'],
				'catalog_trading_account_id' => $data['catalog_trading_account_id'],
				'user_trading_account_id' => $data['default_vars']['user_trading_account_id'],
			])) {
				// important return, dont not remove
				return \Longman\TelegramBot\Request::sendMessage([
					'chat_id' => $data['chat_id'],
					'text' => $orderText,
				]);
			} 
		} else if (self::isExecution($data['message'])) {
			if ($orderText = self::placeOrder([
				'text' => $data['message'],
				'id' => $data['default_vars']['id'],
				'user_login_id' => $data['default_vars']['user_login_id'],
				'catalog_trading_account_id' => $data['catalog_trading_account_id'],
				'user_trading_account_id' => $data['default_vars']['user_trading_account_id'],
			])) {
				// important return, dont not remove
				return \Longman\TelegramBot\Request::sendMessage([
					'chat_id' => $data['chat_id'],
					'text' => $orderText,
				]);
			} 
		} else {
			$response = ML::getResponseIA([
				'tag' => $data['query'],
				'user_login_id' => $data['default_vars']['user_login_id'],
				'message' => $data['message']
			]);
			
			if(in_array($response['tag'],['set_symbol','set_symbol_order_limit','set_order_type','set_order_type_order_limit','set_lotage','set_lotage_order_limit','set_price_entrace_order_limit','set_take_profit_order_limit','set_stop_loss_order_limit']))
			{
				if(empty($response['extracted_vars']))
				{
					$response = self::dispatcher([
						'api' => $data['api'],
						'message' => 'cancelar',
						'catalog_trading_account_id' => isset($data['catalog_trading_account_id']) ? $data['catalog_trading_account_id'] : CatalogTradingAccount::METATRADER,
						'chat_id' => $data['chat_id'],
					]);
				}
			}

			// d($response);
			if(isset($response['extracted_vars']) && !empty($response['extracted_vars']))
			{
				TradePerUserTrading::appendToInprogressOrder([
					'user_trading_account_id' => $data['default_vars']['user_trading_account_id'],
					'lotage' => $response['extracted_vars']['lotage'] ?? null,
					'symbol' => $response['extracted_vars']['symbol'] ?? null,
					'price_entrace' => $response['extracted_vars']['priceEntrace'] ?? null,
					'take_profit' => $response['extracted_vars']['takeProfit'] ?? null,
					'stop_loss' => $response['extracted_vars']['stopLoss'] ?? null,
					'buy' => $response['extracted_vars']['buy'] ?? null,
				]);
			} else {
				// $response = self::dispatcher([
				// 	'api' => $data['api'],
				// 	'message' => 'cancelar',
				// 	'catalog_trading_account_id' => isset($data['catalog_trading_account_id']) ? $data['catalog_trading_account_id'] : CatalogTradingAccount::METATRADER,
				// 	'chat_id' => $data['chat_id'],
				// ]);
			}

			self::getResponse([
				...$data['default_vars'],
				...[
					'chat_id' => $data['chat_id'],
					'message' => $response['response'],
					'api' => $data['api']
				]
			], $data['message']);

			if(in_array($response['tag'],['place_order','place_order_order_limit']))
			{
				if($order = TradePerUserTrading::_getLastOrderInProgress($data['default_vars']['user_trading_account_id']))
				{
					UserTemp::clearVars($data['default_vars']['user_login_id']);

					$message = '';

					if($response['tag'] == 'place_order') 
					{
						$message = "market={$order['lotage']},{$order['symbol']},{$order['type']}";
					} else if($response['tag'] == 'place_order_order_limit') {
						$message = "order={$order['type']},{$order['lotage']},{$order['takeProfit']},{$order['stopLoss']},{$order['symbol']}";
					}

					$response = self::dispatcher([
						'api' => $data['api'],
						'message' => $message,
						'catalog_trading_account_id' => isset($data['catalog_trading_account_id']) ? $data['catalog_trading_account_id'] : CatalogTradingAccount::METATRADER,
						'chat_id' => $data['chat_id'],
					]);
				}
			}
		}

		if(isset($response))
		{
			if(is_array($response))
			{
				return $response['response'];
			} else if(is_object($response)) {
				return $response->result->text;
			}
		}
	}
}
