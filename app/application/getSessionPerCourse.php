<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if($data['session_take_by_user_per_course_id'])
    {
        $SessionTakeByUserPerCourse = new DummieTrading\SessionTakeByUserPerCourse;
        
        if($SessionTakeByUserPerCourse->isAviableCourse($data['session_take_by_user_per_course_id'],$UserLogin->company_id))
        {
            $data['course'] = $SessionTakeByUserPerCourse->getSessionPerCourseId($data['session_take_by_user_per_course_id'],$UserLogin->company_id);
            $data['r'] = 'DATA_OK';
            $data['s'] = 1;
        } else {
            $data['r'] = 'DATA_OK';
            $data['s'] = 1;
        }
    } else {
        $data['r'] = 'NOT_SESSION_TAKE_BY_USER_PER_COURSE_ID';
        $data['s'] = 1;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 