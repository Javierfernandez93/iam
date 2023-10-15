<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "/system/core.php";

$ServicePerClient = new DummieTrading\ServicePerClient;

// if(($data['PHP_AUTH_USER'] == HCStudio\Util::USERNAME && $data['PHP_AUTH_PW'] == HCStudio\Util::PASSWORD) || $UserSupport->logged === true)
if(true)
{
    if($services = $ServicePerClient->getAllServices(DummieTrading\ServicePerClient::IN_USE))
    {
        // d($services);

        array_map(function($service) use($ServicePerClient) {
            $leftDays = $ServicePerClient->calculateLeftDays($service['active_date'],$service['day']);

            echo "el servicio {$service['service_per_client_id']} tiene {$leftDays} días restantes ";

            if(!$ServicePerClient->isActive($service['active_date'],$service['day']))
            {
                echo " - Expiró ";

                // if(DummieTrading\ServicePerClient::expireService($service['service_per_client_id']))
                // {
                //     echo " Correctamente ";
                // }
            } else {
                echo " - Esta activo ";
            }
            
            echo "<br>";
        },$services);

        $data["s"] = 1;
        $data["r"] = "DATA_OK";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 