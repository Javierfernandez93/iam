<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if($data['name'])
    {
        if($data['description'])
        {
            $WhatsAppListPerUser = new DummieTrading\WhatsAppListPerUser;
            
            if($data['whatsapp_list_per_user_id'])
            {
                $WhatsAppListPerUser->loadWhere("whatsapp_list_per_user_id = ?",$data['whatsapp_list_per_user_id']);
            }
            
            $WhatsAppListPerUser->user_login_id = $UserLogin->company_id;
            $WhatsAppListPerUser->name = $data['name'];
            $WhatsAppListPerUser->description = $data['description'];
            $WhatsAppListPerUser->create_date = time();

            if($WhatsAppListPerUser->save())
            {
                $data['r'] = 'SAVE_OK';
                $data['s'] = 1;
            }  else {
                $data['r'] = 'NOT_SAVE';
                $data['s'] = 0;
            }
        } else {
            $data['r'] = 'NOT_DESCRIPTION';
            $data['s'] = 0;
        }
    } else {
        $data['r'] = 'NOT_NAME';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}


echo json_encode(HCStudio\Util::compressDataForPhone($data)); 