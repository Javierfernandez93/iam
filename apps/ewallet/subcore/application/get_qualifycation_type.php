<?php define("TO_ROOT", "../../../..");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Umbrella\UserLogin;

if($UserLogin->logged === true)
{
	$BuyPerUser = new Umbrella\BuyPerUser;



	if($compra = $BuyPerUser->hasBuyMoneyExpressThisMonth($UserLogin->company_id)){
		if($BuyPerUser->foundPurchaseBybuyFrom($UserLogin->company_id,5))
			$primeraFase=true;
		else if($BuyPerUser->foundPurchaseBybuyFrom($UserLogin->company_id,8))
			$segundaFase=true;
	}

	echo "compra->";
	print_r($compra);
	echo "<br>";
	echo "primera->";
	print_r($primeraFase);
	echo "<br>";
	echo "Segunda->";
	print_r($segundaFase);
	die();
	$data['html'] = getHtml($compra,$primeraFase,$segundaFase,$UserLogin);
	$data['s'] = 1;
	$data['r'] = 'DATA_OK';
	
} else {
	$data['s'] = 0;
	$data['r'] = 'INVALID_CREDENTIALS';
}

function getHtml($compra=false,$primeraFase=false,$segundaFase=false,$UserLogin=false)
{
	$html = false;

	if($UserLogin)
	{
		$Layout = JFStudio\Layout::getInstance();
		$Layout->init("","cart-qualification-type","clean",TO_ROOT.'/apps/applications/',TO_ROOT."/");
		$Layout->setVar("compra",$compra);
		$Layout->setVar("primeraFase",$primeraFase);
		$Layout->setVar("segundaFase",$segundaFase);
		return $Layout->getHtml();
	}
	return $html;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); ?>