<?php  define("TO_ROOT", "../../");

require_once TO_ROOT . 'system/core.php';

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if($data['telegram_api_id'])
    {
        $TelegramApi = new DummieTrading\TelegramApi;
        
        if($api = $TelegramApi->get($data['telegram_api_id']))
        {
            if($response = JFStudio\ApiTelegramDummieTrading::configureTelegramHook($api['api_key']))
            {
                $data["response"] = $response;
                $data["s"] = 1;
                $data["r"] = "DATA_OK";
            } else {
                $data["s"] = 0;
                $data["r"] = "NOT_CONFIGURED";
            }
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_API";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_TELEGRAM_API_ID";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode($data);