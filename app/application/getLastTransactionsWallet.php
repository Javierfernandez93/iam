<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if($Wallet = BlockChain\Wallet::getWallet($UserLogin->company_id))
    {
        if($lastTransactions = (new BlockChain\BlockChain)->getLastTransactionsWallet($Wallet->getId(),' LIMIT 8'))
        {
            $data['lastTransactions'] = format(BlockChain\BlockChain::unCompressTransactions($lastTransactions),$Wallet->public_key);
            $data['r'] = 'DATA_OK';
            $data['s'] = 1;
        } else {
            $data['r'] = 'NOT_FUNDS';
            $data['s'] = 0;
        }
    } else {
        $data['r'] = 'NOT_EWALLET';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

function findOutput(array $outputs = null,string $public_key,bool $same = null)  
{
    $_output = null;

    foreach($outputs as $output)
    {
        if(($public_key == $output->address) == $same)
        {
            $_output = $output;
        }
    }

    return $_output;
}

function getTransactionData($lastTransaction = null,string $public_key) : array 
{
    if($lastTransaction['input']->address == $public_key) 
    {
        $type = 'output';
        $output = findOutput($lastTransaction['output'],$public_key,false);
    } else {
        $type = 'input';
        $output = findOutput($lastTransaction['output'],$public_key,true);
    }


    return [
        'type' => $type,
        'address' => $output,
    ];
}

function format(array $lastTransactions = null,string $public_key) : array 
{
    return array_map(function($lastTransaction) use($public_key) {
        $lastTransaction['transactionData'] = getTransactionData($lastTransaction,$public_key);

        return $lastTransaction;
    },$lastTransactions);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 