<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    $Notice = new DummieTrading\Notice;

    $Notice->title = $data['title'];
    $Notice->description = $data['description'];
    $Notice->create_date = time();
    $Notice->user_support_id = $UserSupport->getId();
    $Notice->start_date = $data['start_date'] ? strtotime($data['start_date']) : 0;
    $Notice->end_date = $data['end_date'] ? strtotime($data['end_date']) : 0;
    $Notice->catalog_priority_id = $data['catalog_priority_id'];
    $Notice->catalog_notice_id = $data['catalog_notice_id'];

    if($Notice->save())
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

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 