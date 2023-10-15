<?php define('TO_ROOT', '../../');

require_once TO_ROOT. '/system/core.php';

$data = HCStudio\Util::getHeadersForWebService();

sendWhatsApp(1);

function sendWhatsApp(int $user_login_id = null) : bool
{
    return DummieTrading\ApiWhatsApp::sendWhatsAppMessage([
        'message' => DummieTrading\ApiWhatsAppMessages::getWelcomeMessage(),
        'image' => null,
        'contact' => [
            "phone" => (new DummieTrading\UserContact)->getWhatsApp($user_login_id),
            "name" => (new DummieTrading\UserData)->getName($user_login_id)
        ]
    ]);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 