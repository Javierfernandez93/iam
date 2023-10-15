<?php define("TO_ROOT", "../../../..");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Umbrella\UserLogin();

if($UserLogin->logged === true)
{
	$Cart = new Jcart\Cart();
	
	if(!empty($Cart->getInstances()))
	{	
		$CatalogQualificationType = new Umbrella\CatalogQualificationType();
		
		if($CatalogQualificationType->loadWhere('user_login_id = ? and status = ?',[$UserLogin->company_id,'1']))
		{
			$data['html'] = getHtml($Cart,$UserLogin,(new Umbrella\BuyPerUser()),$CatalogQualificationType);
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

function getHtml($Cart = false,$UserLogin = false,$BuyPerUser = false,$CatalogQualificationType=false)
{
	$html = false;

	if($Cart)
	{
		// $instances = $Cart->getInstances();
		//obtengo la instancia activa para agregar el producto
		$instance_active = $Cart->getInstanceActive();
		// end( $instances);		
		$explode =explode("-",$instance_active);
		$user_login_id = $explode[0];
		$kind = $explode[1];

		$Cart->init($instance_active);



		if($items = $Cart->getItems()){
			foreach ($items as $key => $item) {						
				$total_products+=$item["products"];						
			}					
		}

		$Layout = JFStudio\Layout::getInstance();
		$Layout->init("","cart-floating","clean",TO_ROOT.'/apps/applications/',TO_ROOT."/");
		$Layout->setVar("Cart",$Cart);
		$Layout->setVar("UserData",(new Umbrella\UserData()));
		$Layout->setVar("user_login_id",$user_login_id);		
		$Layout->setVar("total_products",$total_products);
		$Layout->setVar("kind",$kind);
		return $Layout->getHtml();
	}

	return $html;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); ?>