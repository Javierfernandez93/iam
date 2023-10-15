<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php"; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
	if(isset($data['message']))
	{	
		if($tag = JFStudio\ML::getResponseIATag($data['message'],$UserLogin->getTempVar("catalog_tag_intent_id")))
		{
			$CatalogTagIntent = new DummieTrading\CatalogTagIntent;
			
			$data['catalog_tag_intent_id'] = $CatalogTagIntent->getCatalogTagIntentIdByTag($tag);

			if($CatalogTagIntent->hasResponse($data['catalog_tag_intent_id']['catalog_tag_intent_id']))
			{
				$data['response'] = JFStudio\ML::getMLReplay($tag,$data['catalog_tag_intent_id']['catalog_tag_intent_id']);

				if(isset($data['catalog_tag_intent_id']['attach_catalog_tag_intent_id']))
				{
					$UserLogin->setTempVar("catalog_tag_intent_id",$data['catalog_tag_intent_id']['attach_catalog_tag_intent_id']);
				}
			}
			
			$data['tag'] = $tag;

			$data['response'] = DummieTrading\Parser::doParser($data['response'],[
				'names' => trim($UserLogin->getNames()),
				'referral_link' => HCStudio\Connection::getMainPath().$UserLogin->getReferralLanding()
			]);

			$data['response'] = nl2br($data['response']);

			$data['r'] = "DATA_OK";
			$data['s'] = 1;
		} else {
			$data['response'] = 'No tenemos una respuesta para esto. Puedes contactarte con nuestros asesores en <a href="https://www.zuum.link/VG02FH"></a>';
			$data['r'] = "DATA_OK";
			$data['s'] = 1;
		}
	
		$data['r'] = 'DATA_OK';
		$data['s'] = 1;
	} else {
		$data['r'] = 'NOT_MESSAGE';
		$data['s'] = 0;
	}
} else {
	$data['r'] = 'INVALID_CREDENTIALS';
	$data['s'] = 0;
}

echo json_encode($data); 