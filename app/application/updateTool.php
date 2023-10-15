<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if($data['tool_id'])
    {
        $Tool = new DummieTrading\Tool;

        if($Tool->cargarDonde("tool_id = ?",$data['tool_id']))
        {
            $Tool->title = $data['title'];
            $Tool->description = $data['description'];
            $Tool->catalog_tool_id  = $data['catalog_tool_id'];
            $Tool->route = $data['route'];

            if($Tool->save())
            {
                $data["s"] = 1;
                $data["r"] = "DATA_OK";
            } else {
                $data["s"] = 0;
                $data["r"] = "NOT_SAVE";
            }
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_TOOL";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_TOOl_ID";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode($data);