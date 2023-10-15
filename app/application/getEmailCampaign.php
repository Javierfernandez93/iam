<?php define('TO_ROOT', '../../');

require_once TO_ROOT. '/system/core.php';

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if($data['campaign_email_id'])
    {
        $CampaignEmail = new DummieTrading\CampaignEmail;
        $CampaignEmail->connection()->stmtQuery("SET NAMES utf8mb4");
        
        if($campaign = $CampaignEmail->get($data['campaign_email_id']))
        {
            $data['campaign'] = $campaign;
            $data['campaign']['html'] = getHtml($campaign['content']);
            $data['s'] = 1;
            $data['r'] = 'DATA_OK';
        } else {
            $data['s'] = 0;
            $data['r'] = 'NOT_CAMPAIGN';
        }
    } else {
        $data['s'] = 0;
        $data['r'] = 'NOT_CAMPAIGN_EMAIL_ID';
    }
} else {
    $data['s'] = 0;
    $data['r'] = 'NOT_FIELD_SESSION_DATA';
}

function getHtml(string $content = null)
{
	if(isset($content) == true)
	{
		$Layout = JFStudio\Layout::getInstance();
		$Layout->init("","blank","mail-new",TO_ROOT.'/apps/applications/',TO_ROOT."/");
		$Layout->setVar([
            'content' => $content
        ]);

		return $Layout->getHtml();
	}
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 