<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php"; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if($images = (new DummieTrading\Image)->getAll())
    {
        $data['images'] = format($images);
        $data['r'] = 'DATA_OK';
        $data['s'] = 1;
    } else {
        $data['r'] = 'NOT_IMAGES';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

function format(array $images = null) : array {
    return array_map(function($image) {
        $image['tag'] = json_decode($image['tag'],true);
        return $image;
    },$images);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 