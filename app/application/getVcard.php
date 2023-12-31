<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php"; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
	if($data['vcard_per_user_id'])
	{
        $VCardPerUser = new DummieTrading\VCardPerUser;

        if($VCardPerUser->loadWhere('vcard_per_user_id =?',$data['vcard_per_user_id']))
        {
            $data['vcard'] = $VCardPerUser->data();
            $data['r'] = 'DATA_OK';
            $data['s'] = 1;
        } else {
            $data['r'] = 'NOT_TEMPLATE_ID';
            $data['s'] = 0;
        }
	} else {
		$data['r'] = 'NOT_VCARD_PER_USER_id';
		$data['s'] = 0;
	}
} else {
	$data['r'] = 'INVALID_CREDENTIALS';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 