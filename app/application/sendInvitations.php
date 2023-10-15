<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    $data['template']['template'] = DummieTrading\Parser::doParser($data['template']['template'],[
        "names" => trim($UserLogin->getNames()),
        'referral_link' => HCStudio\Connection::getMainPath().$UserLogin->getReferralLanding()
    ]);

    foreach($data['users'] as $user)
    {
        if($invitation_per_user_id = DummieTrading\InvitationPerUser::add([
            'user_login_id' => $UserLogin->company_id,
            'catalog_channel_id' => $data['catalog_channel_id'],
            'catalog_invitation_template_id' => $data['template']['catalog_invitation_template_id'],
            'contact' => $user['contact'],
        ]))
        {
            if(sendWhatsApp([
                'message' => $data['template']['template'],
                'phone' => $user['contact'],
                'name' => 'DummieTrader'
            ]))
            {
                DummieTrading\InvitationPerUser::updateInvitationAsSent($invitation_per_user_id);
            }

            sleep(5);
        }
    }

    $data['r'] = 'DATA_OK';
	$data['s'] = 1;
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

function sendWhatsApp(array $data = null) : bool|array
{
    return DummieTrading\ApiWhatsApp::sendWhatsAppMessage([
        'message' => $data['message'],
        'image' => null,
        'contact' => [
            "phone" => $data['phone'],
            "name" => $data['name']
        ]
    ]);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 