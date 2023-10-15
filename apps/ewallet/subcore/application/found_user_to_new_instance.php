<?php define("TO_ROOT", "../../../..");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Umbrella\UserLogin;

if($UserLogin->logged === true)
{	
	
	if(is_numeric($data["user_login_id"])){
		$UserData = new Umbrella\UserData();
		
		if($UserData->loadWhere("user_login_id=?",$data["user_login_id"])){

			$data['s'] = 1;
			$data['instanceName'] = strtoupper($UserData->getNames($UserData->user_login_id));
			$data['r'] = 'DATA_OK';

		}else{
			$data['s'] = 0;
			$data['r'] = 'USER_LOGIN_ID_NOT_FOUND';
			$data['m'] = 'No se encuentra el Id '.$data["user_login_id"];
		}

	}else{
		$data['s'] = 0;
		$data['r'] = 'USER_LOGIN_ID_IS_NOT_NUMBER';
		$data['m'] = 'Ingresa un Id valido';
	}
} else {
	$data['s'] = 0;
	$data['r'] = 'INVALID_CREDENTIALS';
}
echo json_encode(HCStudio\Util::compressDataForPhone($data)); ?>