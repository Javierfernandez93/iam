<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if($data['ticket_per_user_id'])
	{
        $TicketPerUser = new DummieTrading\TicketPerUser;

        if($TicketPerUser->loadWhere('ticket_per_user_id = ?',$data['ticket_per_user_id']))
        {
            if($data['status'] == DummieTrading\TicketPerUser::SUPPORTING)
            {
                $TicketPerUser->user_support_id = $UserSupport->getId();
            }

            $TicketPerUser->status = $data['status'];
            
            if($TicketPerUser->save())
            {
                $data['r'] = 'DATA_OK';
                $data['s'] = 1;
            } else {
                $data['r'] = 'NOT_SAVED';
                $data['s'] = 0;
            }
        } else {
            $data['r'] = 'NOT_TICKET_PER_USER';
            $data['s'] = 1;
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