<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if(true)
{	
    $advices = [];

    if($signAdivces = $UserLogin->getLastSigned())
    {
        $advices = array_merge($advices,$signAdivces);
    }
    
    if($buyAdvices = $UserLogin->getBuysForAdvices())
    {
        $advices = array_merge($advices,$buyAdvices);
    }

    shuffle($advices);

    $advices[] = [
        'transactions' => (new DummieTrading\TransactionPerWallet('blockchain'))->getCount(),
        'advice_type' => DummieTrading\AdviceType::TRANSACTIONS,
        'showed' => false
    ];
    
    if($advices)
    {
        $data['advices'] = $advices;
        $data['r'] = 'DATA_OK';
        $data['s'] = 1;
    } else {
        $data['r'] = 'NOT_ADVICES';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 