<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if($Wallet = BlockChain\Wallet::getWallet($UserLogin->company_id))
    {
        $data['ewallet'] = $Wallet->attr();
        $data['ewallet']['amount'] = $Wallet->getBalance();
        $data['ewallet']['link'] = (new DummieTrading\ShortUrl)->getLink($Wallet);
        $data['ewallet']['holder'] = $UserLogin->getNames();
        $data['ewallet']['addressLenght'] = BlockChain\Wallet::ADDRESS_LENGTH;

        $data['r'] = 'DATA_OK';
        $data['s'] = 1;
    } else {
        $data['r'] = 'NOT_WALLET';
        $data['s'] = 1;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 