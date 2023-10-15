<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$Country = new World\Country;
$data["countries"] = $Country->getAllByWeb();
$data["s"] = 1;
$data["r"] = "DATA_OK";

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 