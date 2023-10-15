<?php define("TO_ROOT", "../../../..");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Umbrella\UserLogin;

if($UserLogin->logged === true)
{
	if($data['address'])
	{
		
		$Cart = new Jcart\Cart();
		$instances = $Cart->getInstances();

		if($instances)
		{
			foreach ($instances as $key => $instance) {

				$Cart = Jcart\Cart::getInstance($instance);
				$Cart->init($instance);
				$Cart->setShippingAddress($data['address']);
				$Cart->buy();	
				
			}			
			$data['s'] = 1;
			$data['r'] = 'ADDED_OK';

		}else {
			$data['s'] = 0;
			$data['r'] = 'CART_HAS_NOT_INSTANCES';
		}
	} else {
		$data['s'] = 0;
		$data['r'] = 'NOT_PAYMENT_METHOD';
	}
} else {
	$data['s'] = 0;
	$data['r'] = 'INVALID_CREDENTIALS';
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); ?>