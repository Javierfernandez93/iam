<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    $Tool = new DummieTrading\Tool;

    $Tool->title = $data['title'];
    $Tool->description = $data['description'];
    $Tool->create_date = time();
    $Tool->user_support_id = $UserSupport->getId();
    $Tool->catalog_tool_id = $data['catalog_tool_id'];
    $Tool->route = $data['route'];

    if($Tool->save())
    {
        $data["s"] = 1;
        $data["r"] = "DATA_OK";
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_CATALOG_NOTICES";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode($data);