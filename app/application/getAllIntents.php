<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{	
	if($intents = (new DummieTrading\Intent)->getAllGroup())
	{
		$data['intents'] = format($intents);
		$data['r'] = 'DATA_OK';
		$data['s'] = 1;
	} else {
		$data['r'] = 'NOT_INTENTS';
		$data['s'] = 0;
	}
} else {
	$data['r'] = 'INVALID_CREDENTIALS';
	$data['s'] = 0;
}

function format(array $intents = null) : array
{
	$ReplyPerCatalogTagIntent = new DummieTrading\ReplyPerCatalogTagIntent;
	$Intent = new DummieTrading\Intent;

	return array_map(function($intent) use($Intent,$ReplyPerCatalogTagIntent) {
		$intent['words'] = $Intent->getAllByCatalogTagIntentId($intent['catalog_tag_intent_id']);
		$intent['replys'] = $ReplyPerCatalogTagIntent->getReply($intent['catalog_tag_intent_id']);

		return $intent;
	},$intents);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 