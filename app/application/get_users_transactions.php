<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    $WithdrawPerUser = new DummieTrading\WithdrawPerUser;
    
    $filter = " WHERE withdraw_per_user.status = '1'";
    
    if($transactions = $WithdrawPerUser->getAll($filter))
    {
        $data["transactions"] = $transactions;
        $data["s"] = 1;
        $data["r"] = "DATA_OK";
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_WITHDRAWS";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

// function formatData($UserSupport = null, array $transactions = null) : array
// {
//     $WithdrawMethodPerUser = new DummieTrading\WithdrawMethodPerUser;

//     foreach ($transactions as $key => $transaction)
//     {
//         $method = $WithdrawMethodPerUser->getMethod($transaction['user_login_id'],$transaction['catalog_withdraw_method_id']);
//         $transactions[$key]['method'] = $method['method'];
//         $transactions[$key]['account'] = $method['account'];
//         $transactions[$key]['wallet'] = $method['wallet'];

//         $user = $UserSupport->getUser($transaction['user_login_id']);

//         $transactions[$key]['email'] = $user['email'];
//         $transactions[$key]['image'] = $user['image'];
//         $transactions[$key]['names'] = $user['names'];
//     }

//     return $transactions;
// }

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 