<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php"; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
    if($api = (new DummieTrading\TelegramApi)->getByName('DummieTrading'))
    {
        if((new DummieTrading\UserTelegram)->isConnected([
            'user_login_id' => $UserLogin->company_id,
            'telegram_api_id' => 1
        ])) {
            $data['r'] = 'DATA_OK';
            $data['s'] = 1;
        } else {
            $data['r'] = 'NOT_CONENCTED';
            $data['s'] = 0;
        }
    } else {
        $data['r'] = 'NOT_API';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'INVALID_CREDENTIALS';
	$data['s'] = 0;
}

echo json_encode($data); 