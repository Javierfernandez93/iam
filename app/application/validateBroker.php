<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
    $data['type'] = (new DummieTrading\CatalogPlatform)->getType($data['catalog_platform_id']);

    if($data['broker'])
    {
        if(Api\MT4::isValidBroker($data['broker'],$data['type']))
        {
            $data["s"] = 1;
            $data["r"] = "DATA_OK";
        } else {
            $data["s"] = 0;
            $data["r"] = "INVALID_BROKER";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_BROKER";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 