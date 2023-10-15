<?php define("TO_ROOT", "../../../..");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Umbrella\UserLogin;

if($UserLogin->logged === true)
{
	$Cart = new Jcart\Cart;
	
	if(!empty($instances = $Cart->getInstances()))
	{
		// $mainInstance = end( $instances );
		$mainInstance = $Cart->getInstanceActive();
		$explode =explode("-",$mainInstance);
		$user_login_id = $explode[0];
		$kind = $explode[1];
			
		$data['html'] = getHtml($UserLogin,$Cart,$user_login_id,$data['line'],$kind,$data['old']);
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

function getHtml($UserLogin = null,$Cart = null,$user_login_id = false,$line = false,$kind = false,$old = false)
{
	if(isset($UserLogin,$Cart))
	{
		$Layout = JFStudio\Layout::getInstance();

		if($Cart->isCountryAviable($UserLogin->_data['user_address']['country_id']))
		{
			$Layout->setVar("is_active",false);	
		
			if(!empty($instances = $Cart->getInstances()))
			{	
				foreach ($instances as $key => $instance) {
				$explode =explode('-',$instance);
					if($user_login_id == $explode[0]){		
						$found_user++;			
					}
				}

				if($found_user > 1) {
					$Layout->setVar("is_active",true);	
				}
			}

			$UserData = new Umbrella\UserData;
			$user_data = $UserData->getNames($user_login_id);
			
			$instance_active = $user_login_id.'-'.$kind;
			$Cart->init($instance_active);
			
			if($UserLogin->isUserLoginFromOutboard() === false) {
				$divice = "MXN";
			} else {
				$divice = "USD";
			}

			$BuyPerUser = new Umbrella\BuyPerUser;

			$Layout->init("","cart-content","clean",TO_ROOT.'/apps/applications/',TO_ROOT."/");

			$Layout->setVar([
				"old" => $old,
				"has_buy_ever" => $BuyPerUser->hasBuyEver($user_login_id),
				"UserLogin" => $UserLogin,
				"BuyPerUser" => (new Umbrella\BuyPerUser),
				"line" => $line,
				"user_login_id" => $user_login_id,
				"kind" => $kind,
				"items" => $items,
				"Product" => (new Umbrella\Product),
				"total_ins" => count($Cart->getInstances()),
				"divice" => $divice,
				"names" => strtoupper($user_data),
			]);

			if((new Umbrella\BuyPerUser)->isActive($user_login_id)){
				$Layout->setVar("is_active",true);
			} else {
				$Layout->setVar("is_active",false);
			}
	
		} else {
			$Layout->init("","country-not-active","clean",TO_ROOT.'/apps/applications/',TO_ROOT."/");
			$Layout->setScriptPath('../../../../src/');	
			$Layout->setVar([
				'Country' => (new World\Country),
				'AdapterAviableCountries' => (new Jcart\AdapterAviableCountries)
			]);
			$Layout->setScript(['no_country.css']);

		}
		
		return $Layout->getHtml();
	}
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); ?>