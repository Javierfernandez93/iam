<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php"; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    Jcart\Cart::deleteCarts();

    $Cart = Jcart\Cart::hasInstances() ? Jcart\Cart::getInstance(Jcart\Cart::LAST_INSTANCE) : Jcart\Cart::getInstance();
	$Cart->setVar('country_id',$UserLogin->getCountryId());
	$Cart->loadFromSession();
    
    if($Cart->save())
    {
        $data['instance_id'] = $Cart->_instance_id;
        $data['r'] = 'DATA_OK';
        $data['s'] = 1;
    } else {
        $data['r'] = 'NOT_SAVE';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 