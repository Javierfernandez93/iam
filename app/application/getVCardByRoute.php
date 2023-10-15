<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

if(true)
{	
    if($data['route'])
    {
        if($vcard = (new DummieTrading\VCardPerUser)->getVcardByRoute($data['route']))
        {
            $data['vcard'] = $vcard;
            $data['r'] = 'DATA_OK';
            $data['s'] = 1;
        } else {
            $data['r'] = 'NOT_VCARD';
            $data['s'] = 0;
        }
    } else {
        $data['r'] = 'NOT_ROUTE';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 