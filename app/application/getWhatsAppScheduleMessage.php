<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if($data['whatsapp_message_schedule_id'])
    {
        if($schedule = (new DummieTrading\WhatsAppMessageSchedule)->get($data['whatsapp_message_schedule_id']))
        {
            $data['schedule'] = format($schedule,$UserLogin->company_id);
            $data['r'] = 'DATA_OK';
            $data['s'] = 1;
        } else {
            $data['r'] = 'NOT_CAMPAIGNS';
            $data['s'] = 1;
        }
    } else {
        $data['r'] = 'NOT_WHATSAPP_MESSAGE_SCHEDULE_ID';
        $data['s'] = 1;
    }   
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

function format(array $schedule = null,int $user_login_id = null) : array
{
    $schedule['schedule_date'] = date('c', $schedule['schedule_date']);

    return $schedule;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 