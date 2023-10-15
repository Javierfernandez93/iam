<?php define("TO_ROOT", "../../");

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once TO_ROOT . "/system/core.php";

$returnData = Array();
$returnData['current_password'] = HCStudio\Util::getVarFromPGS('current_password');
$returnData['password'] = HCStudio\Util::getVarFromPGS('password');
$returnData['confirm_password'] = HCStudio\Util::getVarFromPGS('confirm_password');

$UserLogin = new DummieTrading\UserLogin(true);

if($UserLogin->logged === true)
{
	if($returnData['current_password']){
		if($returnData['password']){
			if($returnData['confirm_password']){
				if($returnData['confirm_password'] == $returnData['password'] ){
					if( sha1($returnData['current_password']) == $UserLogin->password){

						if( sha1($returnData['password']) != $UserLogin->password){

							//UNA VEZ QUE SE VALIDA TODO SE ACTUALIZA
							$UserLogin->password = sha1($returnData['password']);

							$UserLogin->guardar();

							$company_id = $UserLogin->company_id;
							$password   = $returnData['password'];
							$mail       = $UserLogin->mail;

							$UserLogin->deleteSession();


							$returnData['success']    = 1;
							$returnData['mail']       = $mail;
							$returnData['company_id'] = $company_id;
							$returnData['password']   = $password;
							$returnData['reason']     = 'PASSWORD_UPDATE_OK';


						}else{
							$returnData['success'] = 0;
							$returnData['reason'] = 'SAME_PASSWORD';
						}

					}else {
						$returnData['success'] = 0;
						$returnData['reason'] = 'PASSWORDS_NOT_MATCH';
					}
				}else {
					$returnData['success'] = 0;
					$returnData['reason'] = 'PASSWORDS_NOT_MATCH';
				}
			}else {
				$returnData['success'] = 0;
				$returnData['reason'] = 'NOT_CONFIRM_PASSWORD';
			}
		}else {
			$returnData['success'] = 0;
			$returnData['reason'] = 'NOT_NEW_PASSWORD';
		}

	}else {
		$returnData['success'] = 0;
		$returnData['reason'] = 'NOT_CURRENT_PASSWORD';
	}
} else {
	$returnData['success'] = 0;
	$returnData['reason'] = 'INVALID_CREDENTIALS';
}


echo json_encode($returnData); ?>