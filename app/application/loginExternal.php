<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

if($data['token'])
{
    if($data['key'])
    {
        $Token = new HCStudio\Token;
        
        if($Token->checkToken([
            'token' => $data['token'],
            'key' => $data['key'],
        ]))
        {  
            if($Token->params["email"])
            {
                if($Token->params["password"])
                {		
                    $UserLogin = new DummieTrading\UserLogin(false,false);
                    
                    if($UserLogin->login($Token->params["email"],$Token->params["password"]))
                    {
                        if($redirecTo = (new HCStudio\Session())->getFlash('redirecTo')) {
                            $data["redirecTo"] = $redirecTo;
                        }

                        HCStudio\Util::redirectTo("../../apps/backoffice");
                        
                        $data["data"] = $data;
                        $data["s"] = 1;
                        $data["r"] = "LOGGED_OK";
                    } else {
                        $data["s"] = 0;
                        $data["r"] = "INVALID_PASSWORD";
                    }
                } else {
                    $data["s"] = 0;
                    $data["r"] = "NOT_PASSWORD";
                }
            } else {
                $data["s"] = 0;
                $data["r"] = "NOT_FIELD_SESSION_DATA";
            }
        } else {
            HCStudio\Util::redirectTo("../../apps/home");

            $data["s"] = 0;
            $data["r"] = "NOT_KEY";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_KEY";
    }
} else {
    $data["s"] = 0;
    $data["r"] = "NOT_TOKEN";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 