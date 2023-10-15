<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    $data['day'] = date("Y-m-d");

    $GainPerBroker = new DummieTrading\GainPerBroker;
    $ProfitPerUser = new DummieTrading\ProfitPerUser;
    $CapitalPerBroker = new DummieTrading\CapitalPerBroker;
    
    getBrokersChartData($data,$CapitalPerBroker);
    
    $TransactionPerWallet = new DummieTrading\TransactionPerWallet;
    $WithdrawPerUser = new DummieTrading\WithdrawPerUser;

    $pendingWithdraws = $WithdrawPerUser->getCountPending();

    $data["stats"] = [
        'totalCapital' => $CapitalPerBroker->getTotalCapital(),
        'totalDeposit' => $TransactionPerWallet->getAllDeposits(),
        'totalWithdraws' => $TransactionPerWallet->getAllWithdraws(),
        'totalProfits' => $TransactionPerWallet->getAllProfits(),
        'pendingWithdraws' =>  $pendingWithdraws != false ? $pendingWithdraws : 0,
        'gainsPerDay' => [
            'ammount' => $GainPerBroker->getGainsPerDay($data['day'])
        ],
        'profitsPerDay' => [
            'ammount' => $ProfitPerUser->getProfitsPerDay($data['day'])
        ],
        'gains' => [
            'ammount' => $GainPerBroker->getAllGains()
        ],
        'profits' => [
            'ammount' => $ProfitPerUser->getAllProfits()
        ],
        'totalUsers' => $UserSupport->getCountUsers()
    ];

    $data["s"] = 1;
    $data["r"] = "DATA_OK";
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

function getBrokersChartData(array &$data = null,DummieTrading\CapitalPerBroker $CapitalPerBroker = null)
{
    $Broker = new DummieTrading\Broker;
    $GainPerBroker = new DummieTrading\GainPerBroker;
    
    if($data['brokers'] = $Broker->getActive())
    {
        $days = 5;
        $first_day = strtotime("-{$days} days");

        for($i = 0; $i < $days; $i++)
        {
            $start_date = strtotime(($i+1)." days",$first_day);
            $end_date = strtotime(($i+2)." days",$first_day)-1;

            $data['labels'][] = HCStudio\Util::getDateSimples($start_date);

            foreach ($data['brokers'] as $key => $broker)
            {
                $data['brokers'][$key]['data'][] = $CapitalPerBroker->getLastCapitals($broker['broker_id'],$start_date,$end_date);
            }
        }

        foreach ($data['brokers'] as $key => $broker)
        {
            $capitals = $CapitalPerBroker->getAllPerBroker($broker['broker_id']);
            $gains = $GainPerBroker->getAllGainsPerBroker($broker['broker_id']);

            $gains = $gains ? $gains : [];
            $capitals = $capitals ? $capitals : [];

            $total_capital = array_sum($capitals);
            $total_gain = array_sum($gains);

            $data['brokers'][$key]['averange']['capital'] = sizeof($capitals) ? $total_capital / sizeof($capitals) : 0;
            $data['brokers'][$key]['averange']['gain'] = sizeof($gains) > 0 ? $total_gain / sizeof($gains) : 0;
            $data['pie']['data'][$key] = $total_capital;
        }

        $data['pie']['brokersNames'] = array_column($data['brokers'], 'name');
        $data['pie']['brokersColors'] = array_column($data['brokers'], 'color');
    }
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 