<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$BuyPerUser = new DummieTrading\BuyPerUser;

if($buys = $BuyPerUser->getBuysByAmount(2))
{
    d($buys);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 