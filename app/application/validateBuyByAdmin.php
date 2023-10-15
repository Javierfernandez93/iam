<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if($data['invoice_id']) 
    {
        if($UserSupport->hasPermission('approbe_payment') === true) 
        {
            $url = HCStudio\Connection::getMainPath()."/app/application/validateBuy.php";

            // d($url."?".http_build_query([
            //     'user' => HCStudio\Util::USERNAME,
            //     'password' => HCStudio\Util::PASSWORD,
            //     'invoice_id' => $data['invoice_id'],
            //     'catalog_validation_method_id' => DummieTrading\CatalogValidationMethod::ADMINISTRATOR,
            //     'user_support_id' => $UserSupport->getId(),
            // ]));

            $Curl = new JFStudio\Curl;
            $Curl->post($url, [
                'user' => HCStudio\Util::USERNAME,
                'password' => HCStudio\Util::PASSWORD,
                'invoice_id' => $data['invoice_id'],
                'catalog_validation_method_id' => DummieTrading\CatalogValidationMethod::ADMINISTRATOR,
                'user_support_id' => $UserSupport->getId(),
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
            $UserSupport->addLog([
                'invoice_id' => $data['invoice_id'],
                'unix_date' => time(),
            ],DummieTrading\LogType::INVALID_VALIDATION_PERMISSION);

            $data['s'] = 0;
            $data['r'] = 'INVALID_PERMISSION';
        }
    } else {
        $data['s'] = 0;
        $data['r'] = 'NOT_INVOICE_ID';
    }
} else {
	$data['s'] = 0;
	$data['r'] = 'INVALID_CREDENTIALS';
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 