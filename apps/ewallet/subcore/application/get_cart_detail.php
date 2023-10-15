<?php define("TO_ROOT", "../../../..");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Umbrella\UserLogin;

if($UserLogin->logged === true)
{
	$Cart = new Jcart\Cart;

	if(!empty($instances = $Cart->getInstances()))
	{	
		$data['html'] = getHtml($UserLogin,$Cart);
		
		$data['weight'] = $Cart->total_weight;
		$data['s'] = 1;
		$data['r'] = 'DATA_OK';		
	} else {
		$data['s'] = 0;
		$data['r'] = 'NOT_CART';
	}
} else {
	$data['s'] = 0;
	$data['r'] = 'INVALID_CREDENTIALS';
}

function getHtml($UserLogin = nunll,$Cart = null)
{
	$html = false;

	if(isset($UserLogin,$Cart))
	{
		$Layout = JFStudio\Layout::getInstance();
		$Layout->init("","cart-detail","clean",TO_ROOT.'/apps/applications/',TO_ROOT."/");
		$Layout->setVar([
			"Cart" => $Cart,
			"UserLogin" => $UserLogin,
			"UserData" => (new Umbrella\UserData)
		]);
		return $Layout->getHtml();
	}

	return $html;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); ?>