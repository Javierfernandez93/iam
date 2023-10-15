<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if($data['tool_id'])
    {
        $Tool = new DummieTrading\Tool;
        
        if($tool = $Tool->getTool($data['tool_id']))
        {
            $data["tool"] = $tool;
            $data["s"] = 1;
            $data["r"] = "DATA_OK";
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_TOOL";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_TOOL_ID";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode($data);