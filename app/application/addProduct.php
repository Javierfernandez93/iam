<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php"; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
	$Product = new DummieTrading\Product;

	if($Product->loadWhere("product_id = ? AND status = ?",[$data['product_id'],JFStudio\Constants::AVIABLE]))
	{
		$Cart = Jcart\Cart::getInstance(Jcart\Cart::LAST_INSTANCE);
		$Cart->loadFromSession();	

		if($Cart->addItem($Product,round($data['quantity'],2)))
		{
			if($Cart->save())
			{
				$data['r'] = 'DATA_OK';
				$data['s'] = 1;
			} else {
				$data['r'] = 'NOT_SAVED';
				$data['s'] = 0;	
			}
		} else {
			$data['r'] = 'NOT_ADDED';
			$data['s'] = 0;
		}
	} else {
		$data['r'] = 'NOT_PRODUCT';
		$data['s'] = 0;
	}
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 