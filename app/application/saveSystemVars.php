<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if($systemVars = (new DummieTrading\SystemVar)->findAll("status = ?",[1]))
    {
        $data['systemVars'] = $systemVars;
        $data["s"] = 1;
        $data["r"] = "DATA_OK";
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_SYSTEM_VARS";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "INVALID_CREDENTIALS";
}

echo json_encode($data);