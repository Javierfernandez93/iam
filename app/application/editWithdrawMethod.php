<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
    $UserWallet = new DummieTrading\UserWallet;
    
    if($UserWallet->getSafeWallet($UserLogin->company_id))
    {
        $WithdrawMethodPerUser = new DummieTrading\WithdrawMethodPerUser;
        
        if(!$WithdrawMethodPerUser->cargarDonde("catalog_withdraw_method_id = ? AND user_login_id = ?",[$data['catalog_withdraw_method_id'],$UserLogin->company_id]))
        {
            $WithdrawMethodPerUser->catalog_withdraw_method_id = $data['catalog_withdraw_method_id']; 
            $WithdrawMethodPerUser->user_login_id = $UserLogin->company_id; 
            $WithdrawMethodPerUser->create_date = time(); 
        }
        
        $WithdrawMethodPerUser->wallet = $data['wallet'] ? $data['wallet'] : ''; 

        if($WithdrawMethodPerUser->save())
        {
            $data["s"] = 1;
            $data["r"] = "SAVE_OK";
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_SAVE";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_WITHDRAWS";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 