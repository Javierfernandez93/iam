<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
    if($invoices = (new DummieTrading\BuyPerUser)->getAll($UserLogin->company_id))
    {
        $data['invoices'] = format($invoices);
        $data['s'] = 1;
        $data['r'] = 'DATA_OK';
    } else {
        $data['s'] = 0;
        $data['r'] = 'NOT_INVOICES';
    }	
} else {
	$data['s'] = 0;
	$data['r'] = 'INVALID_CREDENTIALS';
}

function format(array $invoices = null) : array 
{    
    return array_map(function($invoice) {
        $invoice = DummieTrading\BuyPerUser::_unformatData($invoice);

        return $invoice;
    },$invoices);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 