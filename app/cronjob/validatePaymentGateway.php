<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getVarFromPGS();

$UserSupport = new DummieTrading\UserSupport;

if(($data['PHP_AUTH_USER'] == HCStudio\Util::USERNAME && $data['PHP_AUTH_PW'] == HCStudio\Util::PASSWORD) || $UserSupport->logged === true)
{
    $PaymentGateway = new DummieTrading\PaymentGateway;

    if($data['pendingBuys'] = $PaymentGateway->getAllPending())
    {
        require TO_ROOT . '/vendor/autoload.php';

        $ApiTron = new DummieTrading\ApiTron;

        foreach($data['pendingBuys'] as $pendingBuy)
        {
            if($response = $ApiTron->getTrasanctionHistory($pendingBuy['address']))
            {
                if($response['success'] == 1)
                {
                    if($response['data'])
                    {
                        foreach($response['data'] as $transaction)
                        {
                            if($transaction['to'] == $pendingBuy['address'])
                            {
                                if($transaction['token_info']['symbol'] == DummieTrading\ApiTron::USDT)
                                {
                                    if(round($pendingBuy['amount'],2) == round(DummieTrading\ApiTron::parserAmount($transaction['value'],2)))
                                    {
                                        if($pendingBuy['trx_balance'] >= DummieTrading\ApiTron::FEE_LIMIT)
                                        {
                                            sendMoneyToMainWallet($pendingBuy);
                                        } else {
                                            $pendingBuy['trx_balance'] = updateTronBalance($pendingBuy['tron_wallet_id'],$pendingBuy['address']);

                                            if($pendingBuy['trx_balance'] < DummieTrading\ApiTron::FEE_LIMIT)
                                            {
                                                $gasTrx = (float)DummieTrading\ApiTron::FEE_LIMIT - (float)$pendingBuy['trx_balance'];
                                                
                                                if(sendTrxGas(array_merge([
                                                    'tron_wallet_id' => $pendingBuy['tron_wallet_id'],
                                                    'gasTrx' => $gasTrx,
                                                    'toAddress' => $pendingBuy['address']
                                                ],(new DummieTrading\TronWallet)->getMainWalletData()))) {
                                                    $pendingBuy['trx_balance'] = updateTronBalance($pendingBuy['tron_wallet_id'],$pendingBuy['address']);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    } else {
        $data['s'] = 0;
        $data['r'] = "NOT_PENDING_BUYS";
    }
} else {
    $data['s'] = 0;
    $data['r'] = "INVALID_CREDENTIALS";
}

function sendTrxGas(array $data = null) : bool
{
    if(!DummieTrading\TronWallet::isWaitingForGas($data['tron_wallet_id']))
    {
        require TO_ROOT . '/vendor/autoload.php';
    
        $fullNode = new \IEXBase\TronAPI\Provider\HttpProvider(DummieTrading\ApiTron::BASE_URL, 3000, false, false, [
            'TRON-PRO-API-KEY' => DummieTrading\ApiTron::API_KEY,
            'Content-Type' => 'application/json'
        ]);
        $solidityNode = new \IEXBase\TronAPI\Provider\HttpProvider(DummieTrading\ApiTron::BASE_URL);
        $eventServer = new \IEXBase\TronAPI\Provider\HttpProvider(DummieTrading\ApiTron::BASE_URL);
    
        try {
            $tron = new \IEXBase\TronAPI\Tron($fullNode, $solidityNode, $eventServer);
        } catch (\IEXBase\TronAPI\Exception\TronException $e) {
            exit($e->getMessage());
        }
    
        $tron->setAddress($data['address']);
        $tron->setPrivateKey($data['private_key']);
    
        $transfer = $tron->send($data['toAddress'], $data['gasTrx']);
    
        if($transfer['result'] == 1)
        {
            return DummieTrading\TronWallet::setWaitingForGas($data['tron_wallet_id']);
        }
    }
}

function updateTronBalance(int $tron_wallet_id = null,string $address = null) : float
{
    if(isset($tron_wallet_id,$address) === true) 
    {
        require TO_ROOT . '/vendor/autoload.php';
    
        $fullNode = new \IEXBase\TronAPI\Provider\HttpProvider(DummieTrading\ApiTron::BASE_URL, 3000, false, false, [
            'TRON-PRO-API-KEY' => DummieTrading\ApiTron::API_KEY,
            'Content-Type' => 'application/json'
        ]);
        $solidityNode = new \IEXBase\TronAPI\Provider\HttpProvider(DummieTrading\ApiTron::BASE_URL);
        $eventServer = new \IEXBase\TronAPI\Provider\HttpProvider(DummieTrading\ApiTron::BASE_URL);
    
        try {
            $tron = new \IEXBase\TronAPI\Tron($fullNode, $solidityNode, $eventServer);
        } catch (\IEXBase\TronAPI\Exception\TronException $e) {
            exit($e->getMessage());
        }
    
        if($balance = $tron->getBalance($address,true))
        {
            return DummieTrading\TronWallet::updateTrxBalance($tron_wallet_id,$balance);
        }
    }

    return 0;
}

function sendMoneyToMainWallet(array $data = null)
{
    require TO_ROOT . '/vendor/autoload.php';

	$fullNode = new \IEXBase\TronAPI\Provider\HttpProvider(DummieTrading\ApiTron::BASE_URL, 3000, false, false, [
        'TRON-PRO-API-KEY' => DummieTrading\ApiTron::API_KEY,
        'Content-Type' => 'application/json'
    ]);
	$solidityNode = new \IEXBase\TronAPI\Provider\HttpProvider(DummieTrading\ApiTron::BASE_URL);
	$eventServer = new \IEXBase\TronAPI\Provider\HttpProvider(DummieTrading\ApiTron::BASE_URL);

	try {
		$tron = new \IEXBase\TronAPI\Tron($fullNode, $solidityNode, $eventServer);
	} catch (\IEXBase\TronAPI\Exception\TronException $e) {
		exit($e->getMessage());
	}

    $tron->setAddress($data['address']);
    $tron->setPrivateKey($data['private_key']);

    $transfer = $tron->contract(DummieTrading\ApiTron::USDT_TRC20_CONTRACT)->setFeeLimit(DummieTrading\ApiTron::FEE_LIMIT); //fee limit 15 trx o more AND TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t tether contract address
    
    if($result = $transfer->transfer((new DummieTrading\TronWallet)->getMainWalletAddress(), $data['amount']))
    {
        if($result['result'] == 1)
        {
            if(DummieTrading\BuyPerUser::validateIpnPayment($data['buy_per_user_id'],[
                'txID' => $result['txID']
            ]))
            {
                return DummieTrading\PaymentGateway::setStatusAs($data['payment_gateway_id'],DummieTrading\PaymentGateway::PAYED);
            }
        }
    }
}


echo json_encode(HCStudio\Util::compressDataForPhone($data)); 