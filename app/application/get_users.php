<?php

use Psr\Log\Test\DummyTest;

 define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    $filter = "AND user_login.catalog_campaign_id IN(".$UserSupport->campaing.")";

    if($users = $UserSupport->getUsers($filter))
    {
        $data["users"] = format($users);
        $data["s"] = 1;
        $data["r"] = "DATA_OK";
    } else {
        $data['r'] = "DATA_ERROR";
        $data['s'] = 0;
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

function format(array $users = null) : array 
{
    $Country = new World\Country;
    $BuyPerUser = new DummieTrading\BuyPerUser;
    
    $package_ids = (new DummieTrading\Package)->getPackageIds();

    return array_map(function($user) use($Country,$BuyPerUser,$package_ids){
        $user['countryData'] = $Country->getCountryNameAndPhoneArea($user['country_id']);

        return $user;
    },$users);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 