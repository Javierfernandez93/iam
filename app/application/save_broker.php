<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    $Broker = new DummieTrading\Broker;
    $Broker->name = $data['name'];
    $Broker->fee = $data['fee'];
    $Broker->create_date = time();
    
    if($Broker->save())
    {
        if($data['capital'])
        {
            if(DummieTrading\CapitalPerBroker::addCapital($Broker->getId(),$data['capital']))
            {
                $data['capital_saved'] = true;
            }
        }

        if($data['gain'])
        {
            if(DummieTrading\GainPerBroker::addGain($Broker->getId(),$data['gain']))
            {
                $data['gain_saved'] = true;
            }
        }

        $data["s"] = 1;
        $data["r"] = "SAVE_OK";    
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_SAVE";    
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 