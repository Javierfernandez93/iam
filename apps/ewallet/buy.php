<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$Layout = JFStudio\Layout::getInstance();
$Layout->init("Carrito de compras","buy","index","",TO_ROOT."/");

if($UserLogin->logged === false) {
	HCStudio\Util::redirectTo('../..');
}

// # si esta inactivo lo llevamos a cuenta inactiva
if(!$UserLogin->hasActive()) {
	HCStudio\Util::redirectTo(TO_ROOT."/apps/home/inactive_account.php");
}

$UserLogin = new Umbrella\UserLogin();
$BuyPerUser = new Umbrella\BuyPerUser();
$ShippingShared = new Umbrella\ShippingShared();

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript(['carousel.css','backend.css','buy.css','home.css','bootstrap-datepicker.js','buy.js','home.js']);


foreach ($BuyPerUser->BuysPerUserLogin($UserLogin->company_id) as $buy_per_user_login_id => $buy) {
	
	if($buy['date_reg'] == '0000-00-00 00:00:00' && $buy['date_validation'] == '0000-00-00 00:00:00' && $buy['payment_date'] == '0000-00-00 00:00:00'  )
		$reg_buys[$buy_per_user_login_id] = $buy;
	else if($buy['date_reg'] != '0000-00-00 00:00:00' && $buy['payment_date'] != '0000-00-00 00:00:00')
		$validate_buys[$buy_per_user_login_id] = $buy;
	else
		$reg_buys[$buy_per_user_login_id] = $buy;
}

if($reg_buys){

	$shared_shipping=false;
	foreach ($reg_buys as $key => $buy) {
		$ShippingShared = new Umbrella\ShippingShared();
		if($ShippingShared->loadWhere("buy_per_user_login_id=? and status!=?",[$buy["buy_per_user_login_id"],"2"])){
			$shared_shipping[]=$buy;
			unset($reg_buys[$key]);		
		}
	}
	
}

$Layout->setVar("bpulid",HCStudio\Util::getVarFromPGS('bpulid'));
$Layout->setVar("tab",HCStudio\Util::getVarFromPGS('tab'));
$Layout->setVar("UserLogin",$UserLogin);
$Layout->setVar("reg_buys",$reg_buys);
$Layout->setVar("validate_buys",$validate_buys);
$Layout->setVar('ShippingShared',$ShippingShared);
$Layout->setVar('shared_shipping',$shared_shipping);

$Layout();

?>