<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    $data['user_support_id'] = $UserSupport->getId();

    if($course_id = DummieTrading\Course::addCourse($data))
	{
        $data['r'] = 'SAVE_COURSE';
        $data['s'] = 1;
	} else {
		$data['r'] = 'NOT_SAVE_COURSE';
		$data['s'] = 0;
	}
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 