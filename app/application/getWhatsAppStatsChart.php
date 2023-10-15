<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true) 
{	
    $data['stats'] = [
        'line' => [
            'labels' => ['Jun', 'Jul', 'Ago', 'Sept', 'Oct'],
            'data' => [30, 40, 60, 70, 5],
        ],
        'bar' => [
            'labels' => ['Jun', 'Jul', 'Ago', 'Sept', 'Oct'],
            'data' => [30, 40, 60, 70, 5],
        ]
    ];
    $data['r'] = 'DATA_OK';
    $data['s'] = 1;
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 