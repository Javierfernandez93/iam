<?php define("TO_ROOT", "../../../..");

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once TO_ROOT . "/system/core.php";

$returnData = Array();
$UserLogin = new Umbrella\UserLogin();

$returnData['shipping'] = HCStudio\Util::getVarFromPGS('shipping');

if($UserLogin->logged === true)
{
	$Cart = new Jcart\Cart();
	if($Cart->loaded)
	{
		if(!$returnData['shipping']){
			$Cart->shipping_shared = false;
			$Cart->shipping_shared_id = false;
			$Cart->address_shipping_shared = false;
		}

		$Cart->setShipping($returnData['shipping']);
		$Cart->buy();
		$returnData['success'] = 1;
		$returnData['reason'] = 'ADDED_OK';
	} else {
		$returnData['success'] = 0;
		$returnData['reason'] = 'NOT_CART';
	}
} else {
	$returnData['success'] = 0;
	$returnData['reason'] = 'INVALID_CREDENTIALS';
}

echo json_encode($returnData); ?>