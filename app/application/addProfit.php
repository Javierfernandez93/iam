<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getVarFromPGS();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if($UserSupport->hasPermission('add_ewallet_transaction') === true)
    {
        if($data['user_login_id'])
        {
            if($data['amount'])
            {
                if(!(new DummieTrading\GainPerUser)->hasGainOnWeek($data['user_login_id']))
                {
                    $day = date("Y-m-d");
                    $message = "Profit por Trading del día {$day}";

                    if($transaction_per_wallet_id = send($data['user_login_id'],$data['amount'],$message))
                    {
                        if(DummieTrading\GainPerUser::add([
                            'user_login_id' => $data['user_login_id'],
                            'amount' => $data['amount'],
                            'transaction_per_wallet_id' => $transaction_per_wallet_id
                        ]))
                        {
                            if(DummieTrading\NotificationPerUser::push($data['user_login_id'],$message,DummieTrading\CatalogNotification::GAINS,""))
                            {
                                $data['push_sent'] = true;
                            }

                            if(sendWhatsApp($data['user_login_id'],$data['amount']))
                            {
                                $data['push_send'] = true;
                            }


                            $data['s'] = 1;
                            $data['r'] = "DATA_OK";
                        } else {
                            $data['s'] = 0;
                            $data['r'] = "NOT_SAVE_GAIN";
                        }
                    } else {
                        $data['s'] = 0;
                        $data['r'] = "NOT_SEND"; 
                    }
                } else {
                    $data['s'] = 0;
                    $data['r'] = "HAS_GAIN_ON_WEEK";
                }
            } else {
                $data['s'] = 0;
                $data['r'] = "NOT_AMOUNT";
            }
        } else {
            $data['s'] = 0;
            $data['r'] = "NOT_ADDRESS";
        }
    } else {
        $UserSupport->addLog([
            'transaction' => json_encode(['address'=>$data['address'],'amount'=>$data['amount']]),
            'unix_date' => time(),
        ],DummieTrading\LogType::INVALID_TRANSACTION_PERMISSION);

        $data['s'] = 0;
        $data['r'] = 'INVALID_PERMISSION';
    }
} else {
    $data['s'] = 0;
    $data['r'] = "INVALID_CREDENTIALS";
}

function send(int $user_login_id = null,float $amountToSend = null,string $message = null)
{
    if($ReceiverWallet = BlockChain\Wallet::getWallet($user_login_id))
    {
        if($amountToSend)
        {
            $Wallet = BlockChain\Wallet::getWallet(BlockChain\Wallet::MAIN_EWALLET);
            
            if($transaction_per_wallet_id = $Wallet->createTransaction($ReceiverWallet->public_key,$amountToSend,BlockChain\Transaction::prepareData(['@optMessage'=>$message]),true))
            {
                return $transaction_per_wallet_id;
            } 
        } 
    } 
}

function sendWhatsApp(int $user_login_id = null, float $amount = null) : bool
{
    return DummieTrading\ApiWhatsApp::sendWhatsAppMessage([
        'message' => DummieTrading\ApiWhatsAppMessages::getProfitSendMessage(),
        'image' => null,
        'contact' => [
            "phone" => (new DummieTrading\UserContact)->getWhatsApp($user_login_id),
            "name" => (new DummieTrading\UserData)->getName($user_login_id),
            "amount" => $amount
        ]
    ]);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 