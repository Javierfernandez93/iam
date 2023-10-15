<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
    $data['fee'] = BlockChain\Transaction::WITHDRAW_FEE;
    $data['withdrawMethods'] = format((new DummieTrading\WithdrawMethodPerUser)->getAll($UserLogin->company_id));
    $data["s"] = 1;
    $data["r"] = "LOGGED_OK";
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

function format(array $withdrawMethods = null) : array {
    $CatalogCurrency = new DummieTrading\CatalogCurrency;

    return array_map(function($withdrawMethod) use ($CatalogCurrency) {
        $withdrawMethod['catalog_currency'] = $CatalogCurrency->getFullCurrency($withdrawMethod['catalog_currency_id']);
        return $withdrawMethod;
    },$withdrawMethods);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 