<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if($data['broker_id'])
    {
        $Broker = new DummieTrading\Broker;

        if($Broker->cargarDonde('broker_id = ?',$data['broker_id']))
        {
            $CapitalPerBroker = new DummieTrading\CapitalPerBroker;
            $data["capitals"] = $CapitalPerBroker->getAll($Broker->getId());
            $data["broker"] = $Broker->data();
            $data["s"] = 1;
            $data["r"] = "DATA_OK";
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_USER_LOGIN_ID";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_USER_LOGIN_ID";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 