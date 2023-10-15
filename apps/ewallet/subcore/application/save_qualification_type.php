<?php define("TO_ROOT", "../../../..");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Umbrella\UserLogin;

if($UserLogin->logged === true)
{
	$data["user_login_id"] = ($data["user_login_id"]!='false') ? $data["user_login_id"] : $UserLogin->company_id;
	
	if($data["user_login_id"]){
		
		$found_user=0;
		$Cart = new Jcart\Cart();
		//$Cart->deleteAll();		
		$shipping_company=0;
		
		if(!empty($instances = $Cart->getInstances())){
			foreach ($instances as $key => $instance){	
				$explode =explode('-',$instance);
				if($data["user_login_id"]==$explode[0]){
					$found_user++;
				}
				if($key==0){
					$shipping_company=$Cart->shipping_company;
				}
				$Cart->init($instance);
				$Cart->setShippingCompany($shipping_company);
				$Cart->buy();					
			}

		}	

		$cartInstance = ($found_user<1)?$data["user_login_id"].'-'.$data["kind"]:$data["user_login_id"].'-'.'EXTRA';
		$Cart = Jcart\Cart::getInstance($cartInstance);
		$Cart->init($cartInstance);
		$Cart->setInstanceActive($cartInstance);

		$data['s'] = 1;
		$data['r'] = 'DATA_OK';

	}else{
		
		
	}	
} else {
	$data['s'] = 0;
	$data['r'] = 'INVALID_CREDENTIALS';
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); ?>