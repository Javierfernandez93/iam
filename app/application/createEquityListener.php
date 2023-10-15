<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if($response = Api\MT4::createEquityListener([
        'id' => $data['id']
    ]))
    {
        if($response['s'] == 1)
        {
            if(DummieTrading\UserTradingAccount::appendListenerId([
                'user_trading_account_id' => $data['user_trading_account_id'],
                'listener_id' => $response['listenerId']
            ]))
            {
                $data['r'] = 'DATA_OK';
                $data['s'] = 1;
            } else {
                $data['r'] = 'SAVE_OK';
                $data['s'] = 0;
            }
        } else {
            $data['r'] = 'NOT_RESPONSE';
            $data['s'] = 0;
        }
    } else {
        $data['r'] = 'NOT_BROKERS';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 