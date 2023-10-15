<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
    if($singalsProviders = (new DummieTrading\UserSignalProvider)->getAllList($UserLogin->company_id,$data['catalog_signal_provider_id'])) 
    {
        $data['singalsProviders'] = DummieTrading\UserLogin::applyFilterByCatalogCampaignId($singalsProviders,$UserLogin->getUtm());
        $data["s"] = 1;
        $data["r"] = "DATA_OK";
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_RESPONSE";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode($data);