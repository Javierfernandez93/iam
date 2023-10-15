<?php define('TO_ROOT', '../../../../');

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$SupportUser = new DummieTrading\SupportUser;	

if(($SupportUser->_loaded === true) || (HCStudio\Util::checkCurlAuthHeaders($data['PHP_AUTH_USER'],$data['PHP_AUTH_PW'])))
{
	if($SupportUser->hasValidPermition(2) || HCStudio\Util::checkCurlAuthHeaders($data['PHP_AUTH_USER'],$data['PHP_AUTH_PW']))
	{
		if($data['buy_per_user_login_id'])
		{
			$response = (new CronosUser\BuyPerUser())->validateBuy($data['buy_per_user_login_id']);

			if($response["success"] == 1)
			{
				$VideoConsultingPerUser = new CronosUser\VideoConsultingPerUser;

		        if($VideoConsultingPerUser->cargarDonde("buy_per_user_login_id = ?",$data['buy_per_user_login_id']))
		        {
					if(sendMail($VideoConsultingPerUser->getId()))
					{
						if(sendPush($VideoConsultingPerUser->user_login_id))
						{
							$data["push_sent"] = true;
						}
						$data["mail_sent"] = true;
					}
				} else {
					$data["mail_sent"] = true;
				}

				$data["s"] = 1;
				$data["r"] = "SAVE_OK";
			} else {      
				$data['success'] = 0;
				$data['r'] = 'VALIDATE_ERROR';
				$data['m'] = 'ERROR AL VALIDAR LA COMPRA '.$data['buy_per_user_login_id'];
			}
		} else {
			$data['s'] = 0;
			$data['r'] = 'NOT_BUY_SELECTED';
		}
	} else {
		$data['s'] = 0;
		$data['r'] = 'NOT_PERMITION';
	}
} else {
	$data['s'] = 0;
	$data['r'] = 'INVALID_CREDENTIALS';
}	

function sendPush($company_id = null)
{
	if(isset($company_id) === true)
	{
		$UserToken = new CronosUser\UserToken;

		if($UserToken->cargarDonde("user_login_id = ?",$company_id))
		{
			$UserData = new CronosUser\UserData;

			if($names = $UserData->getNames($company_id))
			{
				$Push = JFStudio\Push::getInstance();

				$params = [
					'title' => "Videoconsulta confirmada",
					'body' => "Ya hemos hecho un espacio para atenderte",
					'message' => "Ya hemos hecho un espacio para atenderte",
					'tokens' => $UserToken->token,
					'param' => $company_id,
					'activity_id' => JFStudio\ModelNotificationRoutes::$GROUP,
					'turn_on_light' => false,
					'alarm_sound' => false,
					'ajust_sound' => false,
					'vibration_pattern' => false,
					'icon' => 1,
					'url' => 'https://www.sumateahora.com/apps/backoffice/',
					'click_action' => "https://www.youtube.com/watch?v=WoGWPN3DWHk",
				];

				return $Push->sendAlert($params);
			}
		}
	}

	return false;
}

function sendMail($video_consulting_per_user_id = null)
{
	$VideoConsultingPerUser = new CronosUser\VideoConsultingPerUser;
	
	if($video_consulting_per_user = $VideoConsultingPerUser->getInfo($video_consulting_per_user_id))
	{
		// print_r($video_consulting_per_user);die;
		require_once TO_ROOT . '/system/SendGrid/vendor/autoload.php'; // If you're using Composer (recommended)

		$Mailer = new JFStudio\MailerSendGrid(3);
		$SendGrid = new \SendGrid("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");
		$Email = new \SendGrid\Mail\Mail(); 

		$MailLayout = JFStudio\Layout::getInstance();
		$MailLayout->init("Cita confirmada","schedule_confirmated","mail","",TO_ROOT."/",TO_ROOT."/apps/mail/");

		$MailLayout->setScriptPath(TO_ROOT . '/apps/admin/src/');
		$MailLayout->setScript(['']);

		$MailLayout->setVar([
			"programated_date" => date("Y-m-d H:i:0",$video_consulting_per_user['programated_date']),
			"medic_names" => $video_consulting_per_user['medic_names'],
			"names" => $video_consulting_per_user['names'],
			"phone" => $video_consulting_per_user['phone'],
			"email" => $video_consulting_per_user['email']
		]);

		# compania
	  	$data_mailer["mail_from"] = "descubrecronos@gmail.com";
	  	$data_mailer["name_from"] = "Cronos";

		# quien se registra
	  	$data_mailer["name_to"] = $data["name"];
		$data_mailer["mail_to"] = $data["email"];

	  	$data_mailer["subject"] = "Cronos® - Cita confirmada";
		$data_mailer["body"] = html_entity_decode($MailLayout->getViewContent());

		if(!$Mailer->_sendMail($data_mailer,$Email,$SendGrid)) return false;

		unset($SendGrid);
		unset($Email);
		return true;
	}
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 