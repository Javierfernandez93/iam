<?php

use JFStudio\ApiTelegram;

  define("TO_ROOT", "../../");

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
            require_once TO_ROOT . '/vendor/autoload.php';

            try {
                $telegram = new Longman\TelegramBot\Telegram($api['api_key'], $api['user_name']);

                $result = $telegram->setWebhook(JFStudio\ApiTelegram::getHookUrl());

                if ($result->isOk()) {
                    $data['s'] = 1;
                    $data['r'] = 'DATA_OK';
                } else {
                    $data['s'] = 0;
                    $data['r'] = 'NOT_OK';
                }
            } catch (Longman\TelegramBot\Exception\TelegramException $e) {
                $data['s'] = 0;
                $data['r'] = $e->getMessage();
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