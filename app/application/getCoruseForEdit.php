<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if ($UserSupport->logged === true) 
{
     if ($data['course_id']) 
     {
          if ($course = (new DummieTrading\Course)->getCourse($data['course_id'])) 
          {
               $data["course"] = $course;
               $data["s"] = 1;
               $data["r"] = "DATA_OK";
          } else {
               $data["s"] = 0;
               $data["r"] = "NOT_COURSE";
          }
     } else {
          $data["s"] = 0;
          $data["r"] = "NOT_COURSE_ID";
     }
} else {
     $data["s"] = 0;
     $data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data));
