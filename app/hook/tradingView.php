<?php define("TO_ROOT", "../../");

require_once TO_ROOT . 'system/core.php';

$data = HCStudio\Util::getHeadersForWebService();
// $data = json_decode('{"telegram_api_id":"2","signal_provider_id":"9","exchange":"FX","symbol":"EURUSD","close":"1.09198","volume":"106","gzip":true}',true);

DummieTrading\IpnTradingView::add($data);

if($followers = (new DummieTrading\UserSignalProvider)->getAllFollowing($data['signal_provider_id']))
{
    $url = HCStudio\Connection::getMainPath()."/app/application/senddataToUser.php";

    $Curl = new JFStudio\Curl;
    
    foreach($followers as $follower)
    {
        $url = HCStudio\Connection::getMainPath()."/app/application/sendMessageToUser.php";

        $Curl->post($url,[
            'user' => HCStudio\Util::USERNAME,
            'password' => HCStudio\Util::PASSWORD,
            'chat_id' => $follower['chat_id'],
            'telegram_api_id' => $data['telegram_api_id'],
            'signal_provider_id' => $data['signal_provider_id'],
            'signal' => [
                "market_type" => 1,
                "symbol" => $data['symbol'],
                "quantity" => 0.02,
                "type" => 'market',
                "side" => isset($data['side']) ? $data['side'] : '',
                "price" => 0
            ],
        ]);

        $Curl->getResponse(true);
    }	
}