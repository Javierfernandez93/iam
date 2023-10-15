<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

if($data['password']) $_POST['password'] = $data['password'];
if($data['password']) $_GET['password'] = $data['password'];

if($data['email']) $_POST['email'] = $data['email'];
if($data['email']) $_GET['email'] = $data['email'];


if($data["email"])
{
	if($data["password"])
	{		
		$UserLogin = new DummieTrading\UserLogin;

		if($UserLogin->logged === true)
		{
			if($redirecTo = (new HCStudio\Session())->getFlash('redirecTo')) {
				$data["redirecTo"] = $redirecTo;
			}

			$data["data"] = array_merge($data, [
				'image' => $UserLogin->_data['user_account']['image'],
				'names' => $UserLogin->_data['user_data']['names'],
			]);

			$data['user_login_id'] = $UserLogin->company_id;

			if(DummieTrading\WhatsAppSessionPerUser::setSavesession($data))
			{
				$data["s"] = 1;
				$data["r"] = "LOGGED_OK";
			} else {
				$data["s"] = 0;
				$data["r"] = "NOT_LOGGED";
			}
		} else {
		    $data["s"] = 0;
	  		$data["r"] = "INVALID_PASSWORD";
		}
	} else {
		$data["s"] = 0;
		$data["r"] = "NOT_PASSWORD";
	}
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 