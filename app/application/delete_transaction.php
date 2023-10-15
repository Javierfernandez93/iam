<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if($data['transaction_per_wallet_id'])
    {
        $TransactionPerWallet = new DummieTrading\TransactionPerWallet;
        
        if($TransactionPerWallet->cargarDonde('transaction_per_wallet_id = ?',$data['transaction_per_wallet_id']))
        {
            $TransactionPerWallet->status = DummieTrading\TransactionPerWallet::DELETED;

            if($TransactionPerWallet->save())
            {
                $UserWallet = new DummieTrading\UserWallet;

                if($user_login_id = $UserWallet->getCompanyId($TransactionPerWallet->user_wallet_id))
                {
                    $UserPlan = new DummieTrading\UserPlan;

                    if($UserPlan->setPlan($user_login_id))
                    {
                        $data["s"] = 1;
                        $data["r"] = "DATA_OK";
                    } else {
                        $data["s"] = 0;
                        $data["r"] = "NOT_UPDATE_PLAN";
                    }
                } else {
                    $data["s"] = 0;
                    $data["r"] = "NOT_GETTING_USER_LOGIN_ID";
                }
            } else {
                $data['r'] = "NOT_UPDATE_TRANSACTION_PER_WALLET";
                $data['s'] = 0;
            }
        } else {
            $data['r'] = "NOT_TRANSACTION_PER_WALLET";
            $data['s'] = 0;
        }
    } else {
        $data['r'] = "NOT_TRANSACTION_PER_WALLET_ID";
        $data['s'] = 0;
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 