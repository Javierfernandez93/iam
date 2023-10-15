<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if($data['hash'] ?? false) 
    {
        if($transaction = (new BlockChain\Transaction)->getTransactionInfo($data['hash']))
        {
            $data['transaction'] = $transaction;
            $data['r'] = 'DATA_OK';
            $data['s'] = 1;
        } else {
            $data['r'] = 'NOT_TRANSACTION';
            $data['s'] = 1;
        }
    } else {
        $data['r'] = 'NOT_HASH';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'INVALID_CREDENTIALS';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 