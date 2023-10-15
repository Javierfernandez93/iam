<?php define("TO_ROOT", "../../");

require_once TO_ROOT . 'system/core.php';

$data = HCStudio\Util::getHeadersForWebService();

DummieTrading\IpnTelegram::add($data);

if(isset($data['message']))
{
    $message = $data['message']['text'];
    $chat_id = $data['message']['from']['id'];
    
    if(isset($data['message']['reply_to_message']))
    {
        $message = "copy={$data['message']['reply_to_message']['message_id']},$message";
    } 

    JFStudio\Dispatcher::dispatcher([
        'api' => $api,
        'message' => $message,
        'chat_id' => $chat_id,
    ]);
}