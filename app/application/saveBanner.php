<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if(isset($data['banner']))
    {
        $Banner = new DummieTrading\Banner;

        if($Banner->loadArray($data['banner'])->save())
        {
            $data["s"] = 1;
            $data["r"] = "DATA_OK";
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_SAVE_OR_UPDATE";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_SYSTEM_VAR";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "INVALID_CREDENTIALS";
}

echo json_encode($data);