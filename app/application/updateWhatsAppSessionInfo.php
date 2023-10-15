<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();
		
$data['user_login_id'] = (new DummieTrading\WhatsAppSessionPerUser)->getUserBySessionName($data['session_name']);

if($data['session_name'])
{
    if($data['name'])
    {
        if(DummieTrading\WhatsAppSessionPerUser::setSavesession($data))
        {
            $data['s'] = 1;
            $data['r'] = 'DATA_OK';
        } else {
            $data['r'] = 'NOT__UPDATE';
            $data['s'] = 0;
        }
    } else {
        $data['r'] = 'NOT_NAME';
        $data['s'] = 0;
    }
} else {
    $data['r'] = 'NOT_SESSIONNAME';
    $data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 