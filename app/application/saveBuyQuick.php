<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

if($data['user'] == HCStudio\Util::USERNAME && $data['password'] == HCStudio\Util::PASSWORD)
{
    Jcart\Cart::deleteCarts();

    $Cart = Jcart\Cart::hasInstances() ? Jcart\Cart::getInstance(Jcart\Cart::LAST_INSTANCE) : Jcart\Cart::getInstance();
	$Cart->setVar('country_id',$data['country_id']);
	$Cart->loadFromSession();

    if($Cart->save())
    {
        $Package = new DummieTrading\Package;
        
        if($Package->loadWhere("package_id = ? AND status = ?",[$data['package_id'],JFStudio\Constants::AVIABLE]))
        {
            $Cart = Jcart\Cart::getInstance(Jcart\Cart::LAST_INSTANCE);
            $Cart->loadFromSession();	
    
            if($Cart->addItem($Package,1))
            {
                if($Cart->save())
                {
                    if($BuyPerUser = saveBuy($Cart,$data))
                    {	
                        $data['buy_per_user_id'] = $BuyPerUser->getId();
                        $data['invoice_id'] = $BuyPerUser->invoice_id;
                
                        if($BuyPerUser->save())
                        {
                            $Cart->delete();

                            $url = HCStudio\Connection::getMainPath()."/app/application/validateBuy.php";

                            $Curl = new JFStudio\Curl;
                            $Curl->post($url, [
                                'user' => HCStudio\Util::USERNAME,
                                'password' => HCStudio\Util::PASSWORD,
                                'invoice_id' => $data['invoice_id'],
                                'catalog_validation_method_id' => DummieTrading\CatalogValidationMethod::EVOX_IPN,
                            ]);
                            
                            if($response = $Curl->getResponse(true))
                            {
                                $data['response'] = $response;
                                $data['s'] = 1;
                                $data['r'] = 'DATA_OK';
                            } else {
                                $data['s'] = 0;
                                $data['r'] = 'NOT_RESPONSE';
                            }
                        } else {
                            $data['s'] = 0;
                            $data['r'] = 'NOT_UPDATE';
                        }
                    } else {
                        $data['s'] = 0;
                        $data['r'] = 'NOT_SAVE';
                    }
                } else {
                    $data['r'] = 'NOT_SAVED';
                    $data['s'] = 0;	
                }
            } else {
                $data['r'] = 'NOT_ADDED';
                $data['s'] = 0;
            }
        } else {
            $data['r'] = 'NOT_MAIN_PACKAGE';
            $data['s'] = 0;
        }
    } else {
        $data['r'] = 'NOT_SAVE_VARS';
        $data['s'] = 0;
    }
} else {
	$data['s'] = 0;
	$data['r'] = 'INVALID_CREDENTIALS';
}

function saveBuy($Cart = null,array $data = null)
{
	$BuyPerUser = new DummieTrading\BuyPerUser;
	$BuyPerUser->user_login_id = $data['user_login_id'];
	$BuyPerUser->fee = $Cart->getVar('fee') ? $Cart->getVar('fee') : 0;
	$BuyPerUser->item = $Cart->getFormatedItems();
	$BuyPerUser->checkout_data = json_encode($data['checkout_data']);
	$BuyPerUser->ipn_data = json_encode([]);
	$BuyPerUser->invoice_id = $Cart->_instance_id;
	$BuyPerUser->shipping = 0;
	$BuyPerUser->catalog_payment_method_id = DummieTrading\CatalogPaymentMethod::EXTERNAL;
	$BuyPerUser->catalog_currency_id = DummieTrading\CatalogCurrency::USD;
	$BuyPerUser->amount = $Cart->getTotalAmount(null,null,['fee'=>false]);
	$BuyPerUser->create_date = time();
	$BuyPerUser->status = 1;

	return $BuyPerUser->save() ? $BuyPerUser : false;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 