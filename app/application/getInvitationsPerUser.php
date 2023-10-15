<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	

    $InvitationPerUser = new DummieTrading\InvitationPerUser;
    $InvitationPerUser->connection()->stmtQuery("SET NAMES utf8mb4");

    if($invitations = $InvitationPerUser->getAll($UserLogin->company_id))
    {
        $data['invitations'] = $invitations;
        $data['r'] = 'DATA_OK';
        $data['s'] = 1;
    } else {
        $data['r'] = 'NOT_INVITATIONS';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 