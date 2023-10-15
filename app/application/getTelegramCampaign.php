<?php define('TO_ROOT', '../../');

require_once TO_ROOT. '/system/core.php';

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if($data['campaign_telegram_id'])
    {
        $CampaignTelegram = new DummieTrading\CampaignTelegram;
        $CampaignTelegram->connection()->stmtQuery("SET NAMES utf8mb4");
        
        if($campaign = $CampaignTelegram->get($data['campaign_telegram_id']))
        {
            $data['campaign'] = $campaign;
            $data['campaign']['content'] = json_decode($campaign['content'],true);
            $data['s'] = 1;
            $data['r'] = 'DATA_OK';
        } else {
            $data['s'] = 0;
            $data['r'] = 'NOT_CAMPAIGN';
        }
    } else {
        $data['s'] = 0;
        $data['r'] = 'NOT_CAMPAIGN_TELEGRAM_ID';
    }
} else {
    $data['s'] = 0;
    $data['r'] = 'NOT_FIELD_SESSION_DATA';
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 