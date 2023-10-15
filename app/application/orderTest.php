<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if($account = (new DummieTrading\UserTradingAccount)->getTradingAccountLogin(77))
    {
        $s = JFStudio\ApiBinance::orderTest([
            'apiKey' => $account['login'],
            'apiSecret' => $account['password'],
        ]);
        d($s);
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 