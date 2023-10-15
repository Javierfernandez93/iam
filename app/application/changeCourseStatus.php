<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{	
    if(isset($data['course_id']))
    {
        if(DummieTrading\Course::setState([
            'course_id' => $data['course_id'],
            'status' => $data['status']
        ]))
        {
            $data['r'] = 'DATA_OK';
            $data['s'] = 1;
        } else {
            $data['r'] = 'NOT_UPDATED';
            $data['s'] = 0;
        }
    } else {
        $data['r'] = 'NOT_COURSE_ID';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 