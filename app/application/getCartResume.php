<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
    if(Jcart\Cart::hasInstances())
    {
        $Cart = Jcart\Cart::getInstance(Jcart\Cart::LAST_INSTANCE);
        $Cart->loadFromSession();
        
        if($Cart->save())
        {
            $data['resume'] = [
                'items' => $Cart->getItems(),
                'amount' => $Cart->getTotalAmount(),
                'shipping' => $Cart->getVar('shipping'),
                'payment_method' => (new DummieTrading\CatalogPaymentMethod)->getFeePaymentMethod($Cart->getVar('catalog_payment_method_id'))
            ];
            $data['r'] = 'DATA_OK';
            $data['s'] = 1;
        } else {
            $data['r'] = 'NOT_SAVE';
            $data['s'] = 0;
        }
    } else {
        $data['r'] = 'NOT_INSTANCES';
        $data['s'] = 0;
    }
} else {
	$data['s'] = 0;
	$data['r'] = 'INVALID_CREDENTIALS';
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 