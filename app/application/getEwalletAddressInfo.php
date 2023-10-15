<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if($data['public_key'])
    {
        if($Wallet = BlockChain\Wallet::getWalletByPublicKey($data['public_key']))
        {
            $data['ewallet'] = [ 
                'public_key' => $Wallet->public_key,
                'create_date' => $Wallet->create_date,
                'format' => $Wallet->format,
                'totalReceived' => $Wallet->format,
                'totalSent' => $Wallet->format,
                'link' => (new DummieTrading\ShortUrl)->getLink($Wallet),
                'transactions' => BlockChain\BlockChain::getCountTransactions($Wallet->getId()),
            ];

            $data['ewallet'] = array_merge($data['ewallet'], $Wallet->getBalanceInfo());
            
            $data['r'] = 'DATA_OK';
            $data['s'] = 1;
        } else {
            $data['r'] = 'NOT_WALLET';
            $data['s'] = 1;
        }
    } else {
        $data['r'] = 'NOT_PUBLIC_KEY';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'INVALID_CREDENTIALS';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 