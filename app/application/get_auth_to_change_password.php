<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

if($data["token"])
{
    $Token = new HCStudio\Token;

    $token = explode("[",$data['token']);
    $token = [
        'key' => substr($token[1],0,strlen($token[1])-1),
        'token' => $token[0],
    ];
    
    if($Token->checkToken($token))
    {
        $data["email"] = $Token->params['email'];
        $data["s"] = 1;
        $data["r"] = "DATA_OK";
    } else {
        $data["s"] = 0;
        $data["r"] = "INVALID_TOKEN";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 