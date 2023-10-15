<?php define('TO_ROOT', '../../');

require_once TO_ROOT. '/system/core.php';

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if($data['campaign_whatsapp_id'])
    {
        $CampaignWhatsapp = new DummieTrading\CampaignWhatsapp;
        $CampaignWhatsapp->connection()->stmtQuery("SET NAMES utf8mb4");
        
        if($campaign = $CampaignWhatsapp->get($data['campaign_whatsapp_id']))
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
        $data['r'] = 'NOT_CAMPAIGN_WHATSAPP_ID';
    }
} else {
    $data['s'] = 0;
    $data['r'] = 'NOT_FIELD_SESSION_DATA';
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 