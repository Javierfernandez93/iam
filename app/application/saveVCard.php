<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if($data['template_id'])
    {
        $VCardPerUser = new DummieTrading\VCardPerUser;
        $VCardPerUser->user_login_id = $UserLogin->company_id;
        $VCardPerUser->title = DummieTrading\VCardPerUser::DEFAULT_VCARD_NAME;
        $VCardPerUser->template_id = $data['template_id'];
        $VCardPerUser->create_date = time();
        
        if($VCardPerUser->save())
        {
            if($response = assigTemplateToSheet($UserLogin->getPid(),$VCardPerUser->getId(),$data['template_id']))
            {
                $data['response'] = $response;
            }
            
            $data['vcard_per_user_id'] = $VCardPerUser->getId();
            $data['r'] = 'DATA_OK';
            $data['s'] = 1;
        } else {
            $data['r'] = 'NOT_SAVE';
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


function assigTemplateToSheet(array $pid = null,$vcard_per_user_id = null,$template_id = null)
{
	if(isset($pid) === true)
	{   
		$url = HCStudio\Connection::getMainPath()."/app/application/assing_template_to_sheet.php";

		$Curl = new JFStudio\Curl;
		$Curl->get($url, [
		    'pid' => $pid,
		    'vcard_per_user_id' => $vcard_per_user_id,
		    'template_id' => $template_id,
		]);
        
		return $Curl->getResponse(true);
	}
}
echo json_encode(HCStudio\Util::compressDataForPhone($data)); 