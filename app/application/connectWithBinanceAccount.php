<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
    if($data['api_key'])
    {
        if($data['api_secret'])
        {
            if($response = JFStudio\ApiBinance::accountGet([
                'apiKey' => $data['api_key'],
                'apiSecret' => $data['api_secret']
            ])) {
                if($response['s'] == 1)  
                {
                    $data["s"] = 1;
                    $data["r"] = "DATA_OK";
                } else {
                    $data["s"] = 0;
                    $data["r"] = "INVALID_RESPONSE";
                }
            } else {
                $data["s"] = 0;
                $data["r"] = "NOT_RESPONSE";
            }
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_API_SECRET";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_API_KEY";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode($data);