<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{				
    if($data['lists'])
    {
        if(DummieTrading\WhatsAppMessageSchedule::pushMessagesInSchedule([
            'lists' => $data['lists'],
            'whatsapp_message_schedule_id' => $data['whatsapp_message_schedule_id']
        ]))
        {
            $data['r'] = 'DATA_OK';
            $data['s'] = 1;
        } else {
            $data['r'] = 'NOT_SAVE';
            $data['s'] = 0;
        }
    } else {
        $data['r'] = 'NOT_LISTS';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 