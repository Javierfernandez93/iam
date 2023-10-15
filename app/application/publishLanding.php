<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if(isset($data['landing_id']) === true)
    {
        $data['status'] = JFStudio\Constants::AVIABLE;

        if(DummieTrading\Landing::setLandingAs($data['landing_id'],$data['status']))
        {
            $data["s"] = 1;
            $data["r"] = "DATA_OK";
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_CRON";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_CRON_ID";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode($data);