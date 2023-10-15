<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if($data['course_id'])
    {
        $Course = new DummieTrading\Course;
        $Course->connection()->stmtQuery("SET NAMES utf8mb4");

        $data['user_login_id'] = $UserLogin->company_id;

        if($course = $Course->getFormatted($data))
        {
            $data['course'] = $course;
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