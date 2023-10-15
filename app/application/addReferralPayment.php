<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
    if($data['observation'])
	{
        if($data['image'])
        {
            $BuyPerUser = new DummieTrading\BuyPerUser;
            
            if($BuyPerUser->isInvoicePending($data['invoice_id']))
            {
                if($BuyPerUser->loadWhere('invoice_id = ?',$data['invoice_id']))
                {	
                    $BuyPerUser->ipn_data = json_encode([
                        'image' => $data['image'],
                        'observation' => $data['observation']
                    ]);

                    if($BuyPerUser->save())
                    {
                        $data['s'] = 1;
                        $data['r'] = 'SAVE_OK';
                    } else {
                        $data['s'] = 0;
                        $data['r'] = 'NOT_UPDATE';
                    }
                } else {
                    $data['s'] = 0;
                    $data['r'] = 'NOT_SAVE';
                } 		
            } else {
                $data['s'] = 0;
                $data['r'] = 'NOT_PEDNING';
            } 	
        } else {
            $data['s'] = 0;
            $data['r'] = 'NOT_IMAGE';
        }
	} else {
		$data['s'] = 0;
		$data['r'] = 'NOT_OBSERVATION';
	}
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

function saveBuy(array $Cart = null,$UserLogin = null)
{
	$BuyPerUser = new DummieTrading\BuyPerUser;
	$BuyPerUser->user_login_id = $UserLogin->company_id;
	$BuyPerUser->fee = $Cart->getVar('fee');
	$BuyPerUser->item = $Cart->getFormatedItems();
	$BuyPerUser->checkout_data = json_encode([]);
	$BuyPerUser->ipn_data = json_encode(["image"=>$data['image']]);
	$BuyPerUser->invoice_id = $Cart->_instance_id;
	$BuyPerUser->shipping = 0;
	$BuyPerUser->catalog_payment_method_id = $Cart->getVar('catalog_payment_method_id');
	$BuyPerUser->catalog_currency_id = $Cart->getVar('catalog_currency_id') ? $Cart->getVar('catalog_currency_id') : DummieTrading\CatalogCurrency::USD;
	$BuyPerUser->amount = $Cart->getTotalAmount(null,null,['fee'=>false]);
	$BuyPerUser->create_date = time();

	return $BuyPerUser->save() ? $BuyPerUser : false;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 