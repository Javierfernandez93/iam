<?php define("TO_ROOT", "../../../..");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Umbrella\UserLogin;

if($UserLogin->logged === true)
{
	if($data['instance'])
	{
		if($data['product_id'])
		{
			$Cart = new Jcart\Cart();
			
			$instances = $Cart->getInstances();
			if($instances){

				$isValidInstance = false;
				foreach ($instances as $key => $instance) {
					if($instance==$data['instance'])
						$isValidInstance = true;		
				}

				if($isValidInstance){

					$Cart = Jcart\Cart::getInstance($data['instance']);
					$Cart->init($data['instance']);
					
					
					$Cart->deleteItem('package-'.$data['product_id']);
					
					$Cart->buy();


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

					$Cart->total_weight=$weight;
					$Cart->getTaxByWeight();
					$Cart->putGlobalShippingAmmount($Cart->shipping_ammount);				
					$Cart->init($instance_active);
					//checo el peso del total de los productos

					
					$data['s'] = 1;
					$data['r'] = 'DELETED_OK';
				}else{
					$data['s'] = 0;
					$data['r'] = 'INVALID_INSTANCE';
				}

			}else {
				$data['s'] = 0;
				$data['r'] = 'CART_HAS_NOT_INSTANCES';
			}
		} else {
			$data['s'] = 0;
			$data['r'] = 'NOT__PRODUCT_ID';
		}
	} else {
		$data['s'] = 0;
		$data['r'] = 'NOT_INSTANCE';
	}
} else {
	$data['s'] = 0;
	$data['r'] = 'INVALID_CREDENTIALS';
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); ?>