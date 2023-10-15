<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php"; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if($data['id'])
    {
        $Cart = Jcart\Cart::getInstance(Jcart\Cart::LAST_INSTANCE);
        $Cart->loadFromSession();
        
        $Package = new DummieTrading\Package;

        if($Package->loadWhere("package_id = ? AND status = ?",[$data['id'],1]))
        {
            $Cart->deletePackage($data['id']);

            if($Cart->save())
            {
                $data['r'] = 'DATA_OK';
                $data['s'] = 1;
            } else {
                $data['r'] = 'NOT_SAVE';
                $data['s'] = 0;
            }
        } else {
            $data['r'] = 'NOT_PRODUCT';
	        $data['s'] = 0;    
        }
    } else {
        $data['r'] = 'NOT_PRODUCT_ID';
	    $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 