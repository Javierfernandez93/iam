<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
    $filter = "";
    
    if(isset($data['catalog_trading_account_id']))
    {
        $filter = "AND user_trading_account.catalog_trading_account_id = '{$data['catalog_trading_account_id']}'";
    }

    if($accounts = (new DummieTrading\UserTradingAccount)->_getAllAccountsFromUser($UserLogin->company_id,$filter))
    {
        $data["accounts"] = $accounts;
        $data["s"] = 1;
        $data["r"] = "DATA_OK";
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_ACCOUNTS";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 