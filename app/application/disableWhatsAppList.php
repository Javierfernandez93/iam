<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if($data['whatsapp_list_per_user_id'])
    {
        $WhatsAppListPerUser = new DummieTrading\WhatsAppListPerUser;
        
        if($WhatsAppListPerUser->loadWhere('whatsapp_list_per_user_id = ?',$data['whatsapp_list_per_user_id']))
        {
            $data['status'] = JFStudio\Constants::DISABLED;

            $WhatsAppListPerUser->status = $data['status'];

            if($WhatsAppListPerUser->save())
            {
                $data['r'] = 'SAVE_OK';
                $data['s'] = 1;
            }  else {
                $data['r'] = 'NOT_SAVE';
                $data['s'] = 0;
            }
        } else {
            $data['r'] = 'NOT_WHATSAPP_LIST_PER_USER';
            $data['s'] = 0;
        }
    } else {
        $data['r'] = 'NOT_WHATSAPP_LIST_PER_USER_ID';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}


echo json_encode(HCStudio\Util::compressDataForPhone($data)); 