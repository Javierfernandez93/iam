<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if($symbols = JFStudio\ApiBinance::getAllCriptoSymbols())
    {
        $data['market_type'] = DummieTrading\CatalogTradingAccount::decodeId($data['market_type']);
        
        $data['symbols'] = $symbols[$data['market_type']];
        $data['r'] = 'DATA_OK';
        $data['s'] = 1;
    } else {
        $data['r'] = 'NOT_SYMBOLS';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 