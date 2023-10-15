<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if($data['whatsapp_list_per_user_id'])
    {
        if($contact_ids = DummieTrading\WhatsAppContact::saveContacts($data['contacts']))
        {
            if(DummieTrading\ContactPerWhatsAppList::saveContacts($contact_ids,$data['whatsapp_list_per_user_id']))
            {
                $data['r'] = 'SAVE_OK';
                $data['s'] = 1;
            }  else {
                $data['r'] = 'NOT_SAVE_CONTACTS_IN_LIST';
                $data['s'] = 0;
            }
        } else {
            $data['r'] = 'NOT_SAVE_CONTACTS';
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