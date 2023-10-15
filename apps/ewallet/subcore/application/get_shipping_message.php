<?php define("TO_ROOT", "../../../..");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Umbrella\UserLogin;

if($UserLogin->logged === true)
{
	$Cart = new Jcart\Cart;

	$instances = $Cart->getInstances();
	
	if($instances)
	{
		foreach ($Cart->getInstances() as $instance) 
		{
			$Cart->init($instance);

			if(isset($data['shipping_company']) == false)
			{
				$data['shipping_company'] = $Cart->shipping_company;
			}
		}

		$ModelShippingMethodCOD = new Jcart\ModelShippingMethodCOD;

		if(in_array($Cart->shipping_company, $ModelShippingMethodCOD->getModelShippingMethodCOD()))
		{
			$data['message'] = "Te recordamos que este tipo de paqueteria tiene cobro a destino. Los precios dependen de la distacia.";
			$data['s'] = 1;
			$data['r'] = 'DATA_OK';
		} else {
			$data['s'] = 0;
			$data['r'] = 'NOT_MESSAGE';
		}
	} else {
		$data['s'] = 0;
		$data['r'] = 'CART_HAS_NOT_INSTANCES';
	}
} else {
	$data['s'] = 0;
	$data['r'] = 'INVALID_CREDENTIALS';
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 