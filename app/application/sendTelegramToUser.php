<?php define('TO_ROOT', '../../');

require_once TO_ROOT . '/vendor/autoload.php';
require_once TO_ROOT. '/system/core.php';

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if($data['telegram'])
    {
        if($data['campaign_telegram_id'])
        {
            $CampaignTelegram = new DummieTrading\CampaignTelegram;
            $CampaignTelegram->connection()->stmtQuery("SET NAMES utf8mb4");
            
            if($campaign = $CampaignTelegram->get($data['campaign_telegram_id']))
            {
                $campaign['content'] = json_decode($campaign['content'], true);
                $campaign['content'] = $campaign['content'][rand(0,sizeof($campaign['content'])-1)];
                $campaign['content'] = str_replace('\n', "\n", $campaign['content']);

                $names = 'Socio DummieTrading';

                if($response = sendTelegram($data['telegram'],$campaign['content']))
                {
                    $data['response'] = $response;
                    $data['s'] = 1;
                    $data['r'] = 'DATA_OK';
                } else {
                    $data['s'] = 0;
                    $data['r'] = 'NOT_CAMPAIGN';
                }
            } else {
                $data['s'] = 0;
                $data['r'] = 'NOT_CAMPAIGN';
            }
        } else {
            $data['s'] = 0;
            $data['r'] = 'NOT_CAMPAIGN_telegram_ID';
        }
    } else {
        $data['s'] = 0;
        $data['r'] = 'NOT_EMAIL';
    }
} else {
    $data['s'] = 0;
    $data['r'] = 'NOT_FIELD_SESSION_DATA';
}

function sendTelegram(string $chat_id = null,string $text = null) : bool|array
{
    if($api = (new DummieTrading\TelegramApi)->getByName('DummieTrading'))
	{
		$telegram = new Longman\TelegramBot\Telegram($api['api_key'], $api['user_name']);
        
        $response = \Longman\TelegramBot\Request::sendMessage([
            'chat_id' => $chat_id,
            'text' => $text,
        ]);
    }

    return true;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 