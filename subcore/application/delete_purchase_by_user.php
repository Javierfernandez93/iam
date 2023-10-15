<?php define('TO_ROOT', '../../');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once TO_ROOT . '/system/core.php';

$returnData = Array();


$returnData['buy_per_user_login'] = HCStudio\Util::getVarFromPGS('buy_per_user_login');
$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
	if($returnData["buy_per_user_login"]){
		$BuyPerUser = new DummieTrading\BuyPerUser();

		$BuyPerUser->setId($returnData["buy_per_user_login"]);

		if($BuyPerUser->user_login_id == $UserLogin->atributos()['company_id']){

			$date = date('Y-m-d H:i:s');

			$BuyPerUser->payment_date    = $date;
			$BuyPerUser->date_reg  		 = $date;
			$BuyPerUser->date_validation = $date;
			$BuyPerUser->verified		 = '-1';
			if($BuyPerUser->guardar()){
				$returnData["success"] = "1";
				$returnData["reason"] = "PURCHASE_UPDATE_OK";
			}else{
				$returnData["success"] = "0";
				$returnData["reason"] = "UPDATE_PURCHASE_FAIL";
			}

		}else{
			$returnData["success"] = "0";
			$returnData["reason"] = "USER_LOGIN_NOT_MATCH";
		}
	}else {
		$returnData["success"] = "0";
		$returnData["reason"] = "NOT_BUY_PER_USER_LOGIN";
	}

}else{
	$returnData['success'] = '0';
	$returnData['reason'] = 'INVALID_CREDENTIALS';
}

echo json_encode($returnData);