<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    $ProfitPerUser = new DummieTrading\ProfitPerUser;
    $TransactionPerWallet = new DummieTrading\TransactionPerWallet;

    $data['first_day'] = strtotime("2022-06-10 00:00:00");
    
    $data['start_date'] = $data['start_date'] ? strtotime(date('Y-m-d 00:00:00',strtotime($data['start_date']))) : false;
    $data['end_date'] = $data['end_date'] ? strtotime(date('Y-m-d 23:59:59',strtotime($data['end_date']))) : false;
    
    $data['days'] = round(($data['end_date'] - $data['start_date']) / (60 * 60 * 24));

    for($i = 0; $i < $data['days']; $i++)
    {
        $end_date = strtotime("+{$i} days",$data['start_date']);

        $end_date_strf = date("Y-m-d H:i:s",$end_date);

        $deposits[] = array_merge($TransactionPerWallet->getAllDepositsByUsers($data['first_day'],$end_date),[
            "date_unix" => $end_date,
            "date" => $end_date_strf,
            'days' => $ProfitPerUser->getWorkingDays($end_date_strf)
        ]);
    }

    $data["deposits"] = format($deposits);
    $data["s"] = 1;
    $data["r"] = "DATA_OK";
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

function format(array $deposits = null) : array 
{
    $sum = 0;
    $ProfitPerUser = new DummieTrading\ProfitPerUser;

    foreach($deposits as $key => $deposit) 
    {
        $deposits[$key]['gains'] = $ProfitPerUser->getAllProfitsByDay($deposit['date']);
    }
    
    return $deposits;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 