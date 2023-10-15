<?php define("TO_ROOT", "../../../..");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Umbrella\UserLogin;

if($UserLogin->logged === true)
{
	$Cart = new Jcart\Cart();
	
	if(!empty($instances = $Cart->getInstances()))
	{	

		// $mainInstance = end( $instances );
		$mainInstance = $Cart->getInstanceActive();
		$explode =explode("-",$mainInstance);
		$user_login_id = $explode[0];
		$kind = $explode[1];

		$CatalogQualificationType = new Umbrella\CatalogQualificationType();
		
		if($CatalogQualificationType->loadWhere('user_login_id = ? and status = ?',[$user_login_id,'1']))
		{
			$data['html'] = getHtml($Cart,$user_login_id,(new Umbrella\UserData()),$kind);
			$data['s'] = 1;
			$data['r'] = 'DATA_OK';
		}else{
			$data['s'] = 0;
			$data['r'] = 'NOT_ACTIVATION_KIND';
		}
	} else {
		$data['s'] = 0;
		$data['r'] = 'NOT_CART';
	}
} else {
	$data['s'] = 0;
	$data['r'] = 'INVALID_CREDENTIALS';
}

function getHtml($Cart = false,$user_login_id = false,$UserData = false,$kind=false)
{
	$html = false;

	if($Cart)
	{
		$Layout = JFStudio\Layout::getInstance();
		$Layout->init("","cart-menu","clean",TO_ROOT.'/apps/applications/',TO_ROOT."/");
		$Layout->setVar("Cart",$Cart);
		$Layout->setVar("UserData",$UserData);
		$Layout->setVar("user_login_id",$user_login_id);		
		$Layout->setVar("kind",$kind);
		return $Layout->getHtml();
	}

	return $html;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); ?>