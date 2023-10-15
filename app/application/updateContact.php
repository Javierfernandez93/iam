<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if($data['whatsapp_contact_id'])
    {
        $WhatsAppContact = new DummieTrading\WhatsAppContact;

        if($WhatsAppContact->loadWhere('whatsapp_contact_id = ?',$data['whatsapp_contact_id']))
        {
            $WhatsAppContact->phone = $data['phone'];
            $WhatsAppContact->name = $data['name'] ? $data['name'] : $WhatsAppContact->name;
            
            if($WhatsAppContact->save())
            {
                $data['s'] = 1;
                $data['r'] = 'DATA_OK';
            } else {
                $data['s'] = 0;
                $data['r'] = 'NOT_SAVE';
            }
        } else {
            $data['s'] = 0;
            $data['r'] = 'NOT_ICONTACT_PER_WHATSAPP_LIST';
        }
    } else {
        $data['s'] = 0;
        $data['r'] = 'NOT_WHATSAPP_CONTACT_ID';
    }
} else {
	$data['s'] = 0;
	$data['r'] = "INVALID_CREDENTIALS";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 