<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

if(true)
{	
    if($response = json_decode(file_get_contents("https://vimeo.com/api/oembed.json?url={$data['url']}"),true))
    {
        $data['response'] = $response;
        $data['r'] = 'DATA_OK';
	    $data['s'] = 1;
    } else {
        $data['r'] = 'NOT_SESSION';
	    $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 