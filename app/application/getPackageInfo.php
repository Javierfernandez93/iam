<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if(true)
{	
    if(isset($data['package_id']))
    {
        if($packageInfo = (new DummieTrading\Package)->getPackage($data['package_id']))
        {
            $data['logged'] = $UserLogin->logged;
            $data['packageInfo'] = $packageInfo;
            $data['r'] = 'DATA_OK';
            $data['s'] = 1;
        } else {
            $data['r'] = 'NOT_PACKAGE';
            $data['s'] = 1;
        }
    } else {
        $data['r'] = 'NOT_PACKAGE_ID';
        $data['s'] = 1;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 