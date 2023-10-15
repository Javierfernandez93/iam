<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
    if($data['ticket_per_user_id'])
    {
        if($data['message'])
        {
            if(isset($data['send_from']) === true)
            {
                if(DummieTrading\ItemPerTicket::saveItem($data))
                {
                    $data['s'] = 1;
                    $data['r'] = 'DATA_OK';
                } else {
                    $data['s'] = 1;
                    $data['r'] = 'DATA_OK';
                }
            } else {
                $data['s'] = 0;
                $data['r'] = 'NOT_SEND_FROM';
            }		
        } else {
            $data['s'] = 0;
            $data['r'] = 'NOT_MESSAGE';
        }		
    } else {
        $data['s'] = 0;
        $data['r'] = 'NOT_TICKET_PER_USER_ID';
    }		
} else {
	$data['s'] = 0;
	$data['r'] = 'INVALID_CREDENTIALS';
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 