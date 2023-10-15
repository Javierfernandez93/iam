<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    $TelegramApi = new DummieTrading\TelegramApi;
    
    if($api = $TelegramApi->get($data['telegram_api_id']))
    {
        if($signalProvider = (new DummieTrading\SignalProvider)->get($api['telegram_api_id']))
        {
            if($followers = (new DummieTrading\UserSignalProvider)->getAllFollowing($signalProvider['signal_provider_id']))
            {
                $data["signalProvider"] = $signalProvider;
                $data["followers"] = $followers;
                $data["s"] = 1;
                $data["r"] = "DATA_OK";
            } else {
                $data["s"] = 0;
                $data["r"] = "NOT_CHANNEL";
            } 
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_SIGNAL_PROVIDER";
        } 
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_API";
    } 
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 