<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if($data['whatsapp_message_schedule_id'])
    {
        $WhatsAppMessageSchedule = new DummieTrading\WhatsAppMessageSchedule;

        if($WhatsAppMessageSchedule->loadWhere('whatsapp_message_schedule_id = ?',$data['whatsapp_message_schedule_id']))
        {
            $data['status'] = JFStudio\Constants::DISABLED;
            
            $WhatsAppMessageSchedule->status = $data['status'];
            
            if($WhatsAppMessageSchedule->save())
            {
                $data['s'] = 1;
                $data['r'] = 'DATA_OK';
            } else {
                $data['s'] = 0;
                $data['r'] = 'NOT_SAVE';
            }
        } else {
            $data['s'] = 0;
            $data['r'] = 'NOT_WHATSAPP_MESSAGE_SCHEDULE';
        }
    } else {
        $data['s'] = 0;
        $data['r'] = 'NOT_WHATSAPP_MESSAGE_SCHEDULE_ID';
    }
} else {
	$data['s'] = 0;
	$data['r'] = "INVALID_CREDENTIALS";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 