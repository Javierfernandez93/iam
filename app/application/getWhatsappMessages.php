<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
	$WhatsAppMessagePerCampaign = new DummieTrading\WhatsAppMessagePerCampaign;
	$WhatsAppMessagePerCampaign->connection()->stmtQuery("SET NAMES utf8mb4");

	if($messages = $WhatsAppMessagePerCampaign->getAll($UserLogin->company_id))
	{
		$data['messages'] = format($messages);
		$data['r'] = 'DATA_OK';
		$data['s'] = 1;
	} else {
		$data['r'] = 'NOT_MESSAGES';
		$data['s'] = 0;
	}
} else {
	$data['r'] = 'INVALID_CREDENTIALS';
	$data['s'] = 0;
}

function format(array $messages = null) : array
{
	return array_map(function($message){
		$message['sections'] = json_decode($message['section'],true);
		return $message;
	},$messages);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 