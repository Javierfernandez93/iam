<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    $Course = new DummieTrading\Course;
    $Course->connection()->stmtQuery("SET NAMES utf8mb4");

    if($courses = $Course->getList())
    {
        $data['courses'] = format(filter($courses,$UserLogin->company_id),$UserLogin->company_id);
        $data['r'] = 'DATA_OK';
        $data['s'] = 1;
    } else {
        $data['r'] = 'DATA_OK';
        $data['s'] = 1;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

function filter(array $courses = null,int $user_login_id = null) : array
{
    $BuyPerUser = new DummieTrading\BuyPerUser;

    return array_filter($courses,function($course) use($BuyPerUser,$user_login_id) {
        $aviable = true;
        
        if($course['target'] != DummieTrading\Course::ALL)
        {    
            $aviable = $BuyPerUser->hasPackageBuy($user_login_id,$course['target']);
        }

        return $aviable;
    }); 
}

function format(array $courses = null,int $user_login_id = null) : array
{	
    $SessionTakeByUserPerCourse = new DummieTrading\SessionTakeByUserPerCourse;
    $UserEnrolledInCourse = new DummieTrading\UserEnrolledInCourse;
    
	return array_map(function ($course) use($SessionTakeByUserPerCourse,$UserEnrolledInCourse,$user_login_id) {
        $course['isEnrolled'] = $UserEnrolledInCourse->isEnrolled($course['course_id'],$user_login_id);

        if($course['isEnrolled'])
        {
            $course['hasLessonTaked'] = $SessionTakeByUserPerCourse->hasLessonTaked($course['course_id'],$user_login_id);
            
            if($course['hasLessonTaked'])
            {
                $course['lastCourse'] = $SessionTakeByUserPerCourse->getLastSessionTaked($course['course_id'],$user_login_id);
            }
        }

        return $course;
    },$courses);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 