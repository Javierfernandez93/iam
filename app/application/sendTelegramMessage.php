<?php define("TO_ROOT", "../../");

require_once TO_ROOT . '/vendor/autoload.php';
require_once TO_ROOT . 'system/core.php';

$data = HCStudio\Util::getHeadersForWebService();

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram(JFStudio\ApiTelegram::BOT_API_KEY, JFStudio\ApiTelegram::BOT_USERNAME);

    // Handle telegram webhook request
    if($response = $telegram->handle())
    {
        DummieTrading\IpnTelegram::add($data);

        $result = Longman\TelegramBot\Request::sendMessage([
            'chat_id' => $data['message']['from']['id'],
            'text' => JFStudio\ApiTelegram::getResponse([],$data['message']['text']),
        ]);
    } else {
        DummieTrading\IpnTelegram::add(["response"=>"NOT_HANDLED"]);
    }
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    DummieTrading\IpnTelegram::add(["response"=>$e->getMessage()]);
}