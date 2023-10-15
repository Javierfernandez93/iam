<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "/system/core.php";

$LicencePerUser = new DummieTrading\LicencePerUser;

// if(($data['PHP_AUTH_USER'] == HCStudio\Util::USERNAME && $data['PHP_AUTH_PW'] == HCStudio\Util::PASSWORD) || $UserSupport->logged === true)
if(true)
{
    if($licences = $LicencePerUser->getAllLicences(DummieTrading\LicencePerUser::USED))
    {
        array_map(function($licence) use($LicencePerUser) {
            $leftDays = $LicencePerUser->calculateLeftDays($licence['active_date']);

            echo "la licencia {$licence['code']} tiene {$leftDays} días restantes ";

            if($leftDays <= 0)
            {
                echo " - Expiró ";

                if(DummieTrading\LicencePerUser::expire($licence['licence_per_user_id']))
                {
                    echo " Correctamente ";
                }
            } else {
                echo " - Esta activo ";
            }
            
            echo "<br>";
        },$licences);

        $data["s"] = 1;
        $data["r"] = "DATA_OK";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 