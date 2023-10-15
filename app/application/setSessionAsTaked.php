<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if($data['session_per_course_id'])
    {
        if(!(new DummieTrading\SessionTakeByUserPerCourse)->isSessionTaked($data['session_per_course_id'],$UserLogin->company_id))
        {
            if($sessionTaked = DummieTrading\SessionTakeByUserPerCourse::setSessionAsTaked($data['session_per_course_id'],$UserLogin->company_id))
            {
                $data['sessionTaked'] = $sessionTaked;
                $data['r'] = 'DATA_OK';
                $data['s'] = 1;
            } else {
                $data['r'] = 'NOT_SAVE_SESSION';
                $data['s'] = 0;
            }   
        } else {
            $data['r'] = 'ALREAD_TAKED';
            $data['s'] = 0;
        }   
    } else {
        $data['r'] = 'DATA_OK';
        $data['s'] = 1;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 