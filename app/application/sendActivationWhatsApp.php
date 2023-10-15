<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php"; 

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{	
    if($response = DummieTrading\UserTradingAccount::sendWhatsAppActivation($data))
    {
        $data['wa_api'] = $response;
        $data['r'] = 'DATA_OK';
        $data['s'] = 1;
    } else {
        $data['r'] = 'NOT_SEND_WHATSAPP';
        $data['s'] = 1;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}
echo json_encode(HCStudio\Util::compressDataForPhone($data)); 