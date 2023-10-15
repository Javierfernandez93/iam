<?php define('TO_ROOT', '../../');

require_once TO_ROOT. '/system/core.php';

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if($data['whatsapp'])
    {
        if($data['campaign_whatsapp_id'])
        {
            $seconds = rand(2,5);

            usleep($seconds * 1000000);
            $data['seconds'] = $seconds;
            
            // $data['s'] = 1;
            // $data['r'] = 'DATA_OK';
            // echo json_encode(HCStudio\Util::compressDataForPhone($data)); 
            // die;

            $CampaignWhatsapp = new DummieTrading\CampaignWhatsapp;
            $CampaignWhatsapp->connection()->stmtQuery("SET NAMES utf8mb4");
            
            if($campaign = $CampaignWhatsapp->get($data['campaign_whatsapp_id']))
            {
                $campaign['content'] = json_decode($campaign['content'], true);
                $campaign['content'] = $campaign['content'][rand(0,sizeof($campaign['content'])-1)];
                $campaign['content'] = str_replace('\n', "\n", $campaign['content']);

                $names = 'Socio DummieTrading';

                if($response = sendWhatsApp($data['whatsapp'],$campaign['content']))
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
            $data['r'] = 'NOT_CAMPAIGN_whatsapp_ID';
        }
    } else {
        $data['s'] = 0;
        $data['r'] = 'NOT_EMAIL';
    }
} else {
    $data['s'] = 0;
    $data['r'] = 'NOT_FIELD_SESSION_DATA';
}

function sendWhatsApp(string $whatsapp = null,string $content = null) : bool|array
{
    return DummieTrading\ApiWhatsApp::sendWhatsAppMessage([
        'message' => $content,
        'image' => null,
        'contact' => [
            "phone" => $whatsapp,
            "name" => 'Usuario'
        ]
    ],275);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 