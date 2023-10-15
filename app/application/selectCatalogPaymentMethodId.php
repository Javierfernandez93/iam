<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php"; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
	$CatalogPaymentMethod = new DummieTrading\CatalogPaymentMethod;
	
	if($CatalogPaymentMethod->loadWhere("catalog_payment_method_id = ? AND status != ?",[$data['catalog_payment_method_id'],-1]))
	{
		$Cart = Jcart\Cart::getInstance(Jcart\Cart::LAST_INSTANCE);
        $Cart->loadFromSession();    
		$Cart->setVar('catalog_payment_method_id',$data['catalog_payment_method_id']);

		$Cart->setVar('fee',$Cart->calculateFee());

		if($Cart->save())
		{
			$data['r'] = 'DATA_OK';
			$data['s'] = 1;
		} else {
			$data['r'] = 'NOT_SAVE';
			$data['s'] = 0;
		}
	} else {
		$data['r'] = 'NOT_CATALOG_PAYMENT_METHOD';
		$data['s'] = 0;
	}
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 