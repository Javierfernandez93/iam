<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
    if($data['recipientAdress'])
    {
        if($data['amountToSend'] > 0)
        {
            if($Wallet = BlockChain\Wallet::getWallet($UserLogin->company_id))
            {
                $message = "";
                if($transaction_per_wallet_id = $Wallet->createTransaction($data['recipientAdress'],$data['amountToSend'],BlockChain\Transaction::prepareData(['@optMessage'=>$message]),true,BlockChain\Transaction::TRANSACTION_FEE))
                {
                    $data["s"] = 1;
                    $data["r"] = "SAVE_OK";
                } else {
                    $data["s"] = 0;
                    $data["r"] = "NOT_EWALLET";
                }
            } else {
                $data["s"] = 0;
                $data["r"] = "NOT_EWALLET";
            }
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_AMOUNT_TO_SEND";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_RECIPIENTADRESS";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 