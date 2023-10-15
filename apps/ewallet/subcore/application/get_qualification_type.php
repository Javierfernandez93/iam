

<?php define("TO_ROOT", "../../../..");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();
die;
$UserLogin = new Umbrella\UserLogin;

if($UserLogin->logged === true)
{


	$BuyPerUser = new Umbrella\BuyPerUser;
	// $UserAccount = new Umbrella\UserAccount;

	$user_login_id = ($data["user_login_id"]!='false') ? $data["user_login_id"] : $UserLogin->company_id;
	// $UserAccount->loadWhere("user_login_id=?",$user_login_id);
	$active =false;
	//$total_points = $BuyPerUser->hasBuyRotativeThisMonth($user_login_id);

	if(){
		$active =true;
	}else{

		$Cart = new Jcart\Cart();

		//busco en el carrito de compras 
		if(!empty($instances = $Cart->getInstances())){
			$instance_points=0;
			foreach ($instances as $key => $instance) {
				$explode =explode('-',$instance);
				if($user_login_id == $explode[0]){
					$Cart->init($instance);


					if($items = $Cart->getItems()){
						foreach ($items as $item_key => $item) {
							$instance_points += ($item["points"]*$item["products"]);
						}
					}					
				}
			}
		}

		$total_points += $instance_points;	
	}

	
	$data['html'] = getHtml($active,$total_points,$user_login_id);
	$data['s'] = 1;
	$data['r'] = 'DATA_OK';
	
} else {
	$data['s'] = 0;
	$data['r'] = 'INVALID_CREDENTIALS';
}

function getHtml($active=false,$total_points=false,$user_login_id=false)
{
	$html = false;
	if($user_login_id)
	{
		
		$Layout = JFStudio\Layout::getInstance();
		$Layout->init("","cart-qualification-type","clean",TO_ROOT.'/apps/applications/',TO_ROOT."/");
		$Layout->setVar("active",$active);
		$Layout->setVar("total_points",$total_points);			
		$Layout->setVar("user_login_id",$user_login_id);
		$Layout->setVar("Cart",new Jcart\Cart());	

		return $Layout->getHtml();
	}
	return $html;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); ?>