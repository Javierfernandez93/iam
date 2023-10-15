<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if(($data['user'] == HCStudio\Util::USERNAME && $data['password'] == HCStudio\Util::PASSWORD) || $UserSupport->logged === true)
{
    if($data['invoice_id'])
	{
        $BuyPerUser = new DummieTrading\BuyPerUser;
        
        if($BuyPerUser->isInvoiceDeletedOrExpired($data['invoice_id']))
        {
            if($BuyPerUser->loadWhere('invoice_id = ?',$data['invoice_id']))
            {	
                $BuyPerUser->ipn_data = $data['ipn_data'] ? $data['ipn_data'] : $BuyPerUser->ipn_data;
                $BuyPerUser->catalog_validation_method_id = $data['catalog_validation_method_id'] ? $data['catalog_validation_method_id'] : $BuyPerUser->catalog_validation_method_id;
                $BuyPerUser->user_support_id = $data['user_support_id'] ? $data['user_support_id'] : $BuyPerUser->user_support_id;
                $BuyPerUser->status = $data['status'];
                
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
		$data['r'] = 'NOT_ITEMS';
	}
} else {
	$data['s'] = 0;
	$data['r'] = 'INVALID_CREDENTIALS';
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 