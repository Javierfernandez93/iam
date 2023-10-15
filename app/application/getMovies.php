<?php

use GPBMetadata\Google\Type\Money;

 define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php"; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if($data['filter'])
    {
        $data['filter'] = DummieTrading\Movie::getFilter($data['filter']);
    }

    if($movies = (new DummieTrading\Movie)->_getAll($data['filter']))
    {
        $data['movies'] = $movies;
        $data['r'] = 'DATA_OK';
        $data['s'] = 1;
    } else {
        $data['r'] = 'NOT_PACKAGE_ID';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 