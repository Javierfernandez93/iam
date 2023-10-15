<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if($data['course_id'])
    {
        $SessionPerCourse = new DummieTrading\SessionPerCourse;
        
        if($sessions = $SessionPerCourse->getList($data['course_id']))
        {
            $data['sessions'] = format($sessions,$UserLogin->company_id);
            $data['r'] = 'DATA_OK';
            $data['s'] = 1;
        } else {
            $data['r'] = 'NOT_SESSIONS';
            $data['s'] = 0;
        }
    } else {
        $data['r'] = 'NOT_SESSION_TAKE_BY_USER_PER_COURSE_ID';
        $data['s'] = 1;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

function format(array $sessions = null,int $user_login_id = null) : array
{	
    $SessionTakeByUserPerCourse = new DummieTrading\SessionTakeByUserPerCourse;
    
	return array_map(function ($session) use($SessionTakeByUserPerCourse,$user_login_id) {
        $session['sessionTaked'] = $SessionTakeByUserPerCourse->getSessionInfo($session['session_per_course_id'],$user_login_id);

        return $session;
    },$sessions);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 