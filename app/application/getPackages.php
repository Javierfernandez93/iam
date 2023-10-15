<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
    $filter = "AND package.catalog_package_type_id = '{$data['catalog_package_type_id']}'";

    if($packages = (new DummieTrading\Package)->getAll($filter))
    {
        $data['packages'] = DummieTrading\UserLogin::applyFilterByCatalogCampaignId($packages,$UserLogin->getUtm());
        $data["s"] = 1;
        $data["r"] = "DATA_OK";
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_PACKAGES";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "INVALID_CREDENTIALS";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 