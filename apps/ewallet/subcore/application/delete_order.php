<?php define("TO_ROOT", "../../../..");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Umbrella\UserLogin;

if($UserLogin->logged === true)
{

	$Cart = new Jcart\Cart();
	$instances = $Cart->getInstances();

	$instance_active = ($data["instance"]) ? $data["instance"] : $Cart->getInstanceActive();

	if(count($instances)==1)
		$Cart->deleteAll();
	else{
		foreach ($instances as $key => $instance) {
			if($instance_active==$instance){
				unset($instances[$key]);
				$Cart->unsetInstance($instances,$instance);
			}
		}

		$set_instance_active=false;
		foreach ($instances as $key => $instance) {
			if(!$set_instance_active){
				$user_id=explode('-', $instance)[0];
				if($UserLogin->company_id == $user_id ){
					$Cart = Jcart\Cart::getInstance($instance);
					$Cart->init($instance);
					$Cart->setInstanceActive($instance);
					$set_instance_active=true;
				}
			}
		}

		if(!$set_instance_active)
			$Cart->deleteAll();
		else{
			//checo el peso del total de los productos				
			$weight=0;
			foreach ($Cart->getInstances() as $key => $instance) {
				$Cart->init($instance);
				if($items = $Cart->getItems()){						
					foreach ($items as $key => $item) {										
						$weight+=($item['products']*$item['weight']);							
					}					
				}
			}

			// $weight;
			$Cart->total_weight=$weight;
			$Cart->getTaxByWeight();
			$Cart->putGlobalShippingAmmount($Cart->shipping_ammount);
			$Cart->init($instance);
			$Cart->setInstanceActive($instance);
		}
	}
	$data['s'] = 1;
	$data['r'] = 'DATA_OK';	
} else {
	$data['s'] = 0;
	$data['r'] = 'INVALID_CREDENTIALS';
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); ?>