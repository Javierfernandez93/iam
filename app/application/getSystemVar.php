<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;
$UserLogin = new DummieTrading\UserLogin;

if($UserSupport->logged === true || $UserLogin->logged === true)
{	
    if(isset($data['name']))
    {
        if($var = DummieTrading\SystemVar::_getValue($data['name']))
        {
            if(HCStudio\Util::isJson($var))
            {
                $var = json_decode($var,true);
            }
            
            $data['var'] = $var;
            $data["s"] = 1;
            $data["r"] = "DATA_OK";
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_SYSTEM_VAR";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_NAME";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "INVALID_CREDENTIALS";
}

echo json_encode($data);