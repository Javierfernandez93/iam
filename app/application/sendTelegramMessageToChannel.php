<?php  define("TO_ROOT", "../../");

require_once TO_ROOT . '/vendor/autoload.php';
require_once TO_ROOT . 'system/core.php';

$data = HCStudio\Util::getHeadersForWebService();

// $data = json_decode('{"update_id":299840323,"message":{"message_id":266,"from":{"id":1930485774,"is_bot":false,"first_name":"Javier","last_name":"Fern\u00e1ndez","username":"Jfernandez93","language_code":"es"},"chat":{"id":1930485774,"first_name":"Javier","last_name":"Fern\u00e1ndez","username":"Jfernandez93","type":"private"},"date":1686714305,"text":"\/profit","entities":[{"offset":0,"length":7,"type":"bot_command"}]},"gzip":true}',true);
$data = json_decode('{"update_id":872888677,"message":{"message_id":10,"from":{"id":1930485774,"is_bot":false,"first_name":"Javier","last_name":"Fern\u00e1ndez","username":"Jfernandez93","language_code":"es"},"chat":{"id":1930485774,"first_name":"Javier","last_name":"Fern\u00e1ndez","username":"Jfernandez93","type":"private"},"date":1688007529,"text":"Hola"},"gzip":true}',true);
// $data = json_decode('{"update_id":299840436,"message":{"message_id":1117,"from":{"id":1930485774,"is_bot":false,"first_name":"Javier","last_name":"Fern\u00e1ndez","username":"Jfernandez93","language_code":"es"},"chat":{"id":1930485774,"first_name":"Javier","last_name":"Fern\u00e1ndez","username":"Jfernandez93","type":"private"},"date":1688007537,"text":"Hola"},"gzip":true}',true);
$pass = true;

try {
    $telegram = new Longman\TelegramBot\Telegram(JFStudio\ApiTelegramDummieTrading::BOT_API_KEY, JFStudio\ApiTelegramDummieTrading::BOT_USERNAME);

    // if($response = $telegram->handle())
    if($pass)
    {
        // DummieTrading\IpnTelegram::add($data);

		// // d($data);

		// $result = Longman\TelegramBot\Request::sendMessage([
		// 	'chat_id' => -1001988020140,
		// 	'text' => "Estamos procesando tu orden Hola",
		// ]);

		// d($result);

		// $reply = '';		
		// $message = $data['message']['text'];
        // $chat_id = $data['message']['from']['id'];

		// $default_vars = JFStudio\ApiTelegram::getDefaultVars($chat_id);

		// $query = getResponseIATag($message);   
		
        // if(JFStudio\ApiTelegram::isCommand($message))
        // {
		// 	$reply = JFStudio\ApiTelegram::getResponse([
		// 		...$default_vars,
		// 		...[
		// 			'chat_id' => $chat_id
		// 		]
		// 	],$message);   
        // } 
		
		// if(JFStudio\ApiTelegram::isConfig($query)) 
		// {
		// 	if(JFStudio\ApiTelegram::applyConfig([
		// 		'config' => $query,
		// 		'text' => $message,
		// 		'chat_id' => $chat_id
		// 	]))
		// 	{
		// 		$result = Longman\TelegramBot\Request::sendMessage([
		// 			'chat_id' => $chat_id,
		// 			'text' => "¡Gracias! Hemos aplicado la configuración para {$message}.".PHP_EOL.PHP_EOL."Tu chat se ha vinculado a tu cuenta.",
		// 		]);
		// 	}
        // }
		
		// if(JFStudio\ApiTelegram::isExecution($message)) 
		// {
		// 	if($orderText = JFStudio\ApiTelegram::placeOrder($message))
		// 	{
		// 		$result = Longman\TelegramBot\Request::sendMessage([
		// 			'chat_id' => -1001988020140,
		// 			'text' => "Estamos procesando tu orden {$orderText}",
		// 		]);
		// 	}
        // }

		// if(!JFStudio\ApiTelegram::isCommand($message))
		// {
		// 	$message = getResponseIA($query);
			
		// 	$reply = JFStudio\ApiTelegram::getResponse([
		// 		...$default_vars,
		// 		...[
		// 			'chat_id' => $chat_id
		// 		]
		// 	],$message);
		// }

        // // $result = Longman\TelegramBot\Request::sendMessage([
        // //     'chat_id' => $chat_id,
        // //     'text' => $reply,
        // // ]);
    } else {
        DummieTrading\IpnTelegram::add(["response"=>"NOT_HANDLED"]);
    }
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    DummieTrading\IpnTelegram::add(["response"=>$e->getMessage()]);
}

function getResponseIA(string $tag = null) : string
{
	if(!JFStudio\ApiTelegram::isCommand($tag))
	{
		$CatalogTagIntent = new DummieTrading\CatalogTagIntent;
		$catalog_tag_intent_id = $CatalogTagIntent->getCatalogTagIntentIdByTag($tag);
		
		if($CatalogTagIntent->hasResponse($catalog_tag_intent_id))
		{
			return getMLReplay($tag,$catalog_tag_intent_id);
		} 
	
		return "No tenemos una respuesta para esta pregunta, por favor intentalo de nuevo.";
	}

	return $tag;
}

function getResponseIATag(string $query = null)
{
    $intents = DummieTrading\Intent::getIntents($query);

    if($tag = predict($intents,$query))
    {
		return $tag['tag'];
    } 

	return $query;
}

function isSingleData(array $data = null) 
{
	return sizeOf(array_unique(array_column($data,"tag"))) <= 2;
} 

function predict(array $data = null, string $sentence = null) 
{
    require_once TO_ROOT . '/vendor/autoload.php';

	if(isSingleData($data) === true)
	{
		return [
			"tag" => $data[0]['tag'],
			"probability" => 100
		];
	} else {
		$data = array_values($data);

		$classifier = new Phpml\Classification\SVC(
		    Phpml\SupportVectorMachine\Kernel::LINEAR, // $kernel
		    1.0,            // $cost
		    3,              // $degree
		    null,           // $gamma
		    0.0,            // $coef0
		    0.001,          // $tolerance
		    100,            // $cacheSize
		    true,           // $shrinking
		    true            // $probabilityEstimates, set to true
		);

		$ML = new JFStudio\ML;

		$samples = $ML->getTargetsByBD($data);
		$targets = $ML->getSamplesByBD($data);

		$vectorizer = new Phpml\FeatureExtraction\TokenCountVectorizer(new Phpml\Tokenization\WordTokenizer);
		$tfIdfTransformer = new Phpml\FeatureExtraction\TfIdfTransformer;

		$vectorizer->fit($samples);
		$vectorizer->transform($samples);

		$tfIdfTransformer->fit($samples);
		$tfIdfTransformer->transform($samples);

		$dataset = new Phpml\Dataset\ArrayDataset($samples, $targets);

		$ML->setValues($ML->convertValues($dataset->getSamples()));
		$ML->setVocabulary($vectorizer->getVocabulary());
    
		$classifier->train($samples, $targets);

		if($samples = $ML->getSamples($sentence))
		{
			if($tag = $classifier->predict($samples))
			{
				if($probabilities = $classifier->predictProbability($samples))
				{
					if($probability = $probabilities[$tag])
					{
						$probability *= 100;
						
						if($probability > 10)
						{
							return [
								"tag" => $tag,
								"probabilities" => $probabilities
							];
						}
					}
				}
			}
		}
	}

	return false;
}

function getMLReplay(string $tag = null,$catalog_tag_intent_id = null) : string
{
	if(isset($tag,$catalog_tag_intent_id) === true)
	{
		return DummieTrading\ReplyPerCatalogTagIntent::getReplyRandom($catalog_tag_intent_id);
	}

	return DummieTrading\ReplyPerCatalogTagIntent::getDefaultReply();
}
