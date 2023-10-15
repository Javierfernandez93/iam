<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if($data['contact_per_whatsapp_list_id'])
    {
        $ContactPerWhatsAppList = new DummieTrading\ContactPerWhatsAppList;

        if($ContactPerWhatsAppList->loadWhere('contact_per_whatsapp_list_id = ?',$data['contact_per_whatsapp_list_id']))
        {
            $data['status'] = JFStudio\Constants::AVIABLE;

            $ContactPerWhatsAppList->status = $data['status'];
            
            if($ContactPerWhatsAppList->save())
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
        $data['r'] = 'NOT_CONTACT_PER_WHATSAPP_LIST_ID';
    }
} else {
	$data['s'] = 0;
	$data['r'] = "INVALID_CREDENTIALS";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 