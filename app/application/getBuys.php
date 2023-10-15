<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if(isset($data['status']) && !empty($data['status']))
    {
        $data['status'] = isset($data['status']) ? $data['status'] : DummieTrading\BuyPerUser::PENDING;
        
        $filter = " WHERE buy_per_user.status = '{$data['status']}'";
    }

    if($data['query'])
    {
        $connector = isset($data['status']) && !empty($data['status']) ? "AND" : "WHERE";
        $filter .= " {$connector} 
            (
                buy_per_user.buy_per_user_id = '{$data['query']}'
                OR 
                buy_per_user.user_login_id = '{$data['query']}'
                OR 
                buy_per_user.invoice_id = '{$data['query']}'
                OR 
                buy_per_user.checkout_data LIKE '%{$data['query']}%'
            )
        ";   
    }

    if($buys = $UserSupport->getBuysList($filter))
    {
        $data["buys"] = $buys;
        $data["s"] = 1;
        $data["r"] = "DATA_OK";
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_BUYS";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 