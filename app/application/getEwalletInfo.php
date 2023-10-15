<?php

use BlockChain\Wallet;
use DummieTrading\UserData;

 define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if($data['address'])
    {
        if($Wallet = BlockChain\Wallet::getWallet((new BlockChain\Wallet)->getUserIdByPublicKey($data['address'])))
        {
            if($lastTransactions = (new BlockChain\BlockChain)->getLastTransactionsWallet($Wallet->getId(),' LIMIT 120'))
            {
                $data['lastTransactions'] = format(BlockChain\BlockChain::unCompressTransactions($lastTransactions),$Wallet->public_key);
                $data['r'] = 'DATA_OK';
                $data['s'] = 1;
            } else {
                $data['r'] = 'NOT_TRANSACTIONS';
                $data['s'] = 0;
            }
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_EWALLET";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_RECIPIENTADRESS";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
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

    if($user_login_id = (new BlockChain\Wallet)->getUserIdByPublicKey($output->address))
    {

        $names = (new DummieTrading\UserData)->getNames($user_login_id);
    }

    return [
        'type' => $type,
        'names' => $names,
        'user_login_id' => $user_login_id,
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