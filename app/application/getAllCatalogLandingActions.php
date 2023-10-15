<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getVarFromPGS();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if($UserSupport->hasPermission('add_ewallet_transaction') === true)
    {
        if($catalog_landing_actions = (new DummieTrading\CatalogLandingAction)->getAll()) {
            $data['catalog_landing_actions'] = $catalog_landing_actions;
            $data['s'] = 1;
            $data['r'] = "DATA_OK";
        } else {
            $data['s'] = 0;
            $data['r'] = "NOT_CATALOG_LANDING_ACTIONS";
        }
    } else {
        $data['s'] = 0;
        $data['r'] = 'INVALID_PERMISSION';
    }
} else {
    $data['s'] = 0;
    $data['r'] = "INVALID_CREDENTIALS";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 