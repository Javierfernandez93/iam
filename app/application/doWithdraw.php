<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
    if($data['withdraw_method_per_user_id'])
    {
        if($data['amount'])
        {
            if($ReceiverWallet = BlockChain\Wallet::getWallet(BlockChain\Wallet::MAIN_EWALLET))
            {
                if($Wallet = BlockChain\Wallet::getWallet($UserLogin->company_id))
                {
                    $data["s"] = 0;
                    $data["r"] = "NOT_EWALLET";
                } else {
                    $data["s"] = 0;
                    $data["r"] = "NOT_EWALLET";
                }
            } else {
                $data["s"] = 0;
                $data["r"] = "NOT_RECEIVER_WALLET";
            }
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_AMOUNT";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_WITHDRAW_METHOD_PER_USER_ID";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 