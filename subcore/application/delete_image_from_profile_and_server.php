<?php define("TO_ROOT", "../../"); = new GranCapital

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once TO_ROOT . "/system/core.php";

$returnData = Array();

$returnData['image'] = HCStudio\Util::getVarFromPGS('image');

$UserLogin = new Talento\UserLogin(true);

if($UserLogin->logged === true)
{
	if($returnData['image']){
		// /apps/backoffice/subcore/media/images/

		//Primero liminas la imagen que esta seteada
		$image     = explode("/", $UserLogin->_data["user_settings"]["image"]);
		$imageName = $image[6];

		if(file_exists('../..'.$UserLogin->_data["user_settings"]["image"])){
			if(unlink( '../..'.$UserLogin->_data["user_settings"]["image"]))
				$returnData['deleteImage'] = '1';
		}

		if(file_exists("../../apps/backoffice/subcore/media/images/thumbnail/".$imageName)){
			if(unlink("../../apps/backoffice/subcore/media/images/thumbnail/".$imageName))
				$returnData['deletethumbnail'] = '1';
		}

		$image     = explode("/", $returnData['image']);
		$imageName = $image[7];

		// una vez que elimino las antiguas imagenes paso a actualizar la nueva a la cuenta
		$UserLogin->_parent["user_settings"]->image = "/apps/backoffice/subcore/media/images/".$imageName;

		if($UserLogin->_parent["user_settings"]->guardar()){
			$returnData['success'] = 1;
			$returnData['reason']  = 'UPDATE_IMAGE_PROFILE_OK';
			$returnData['image']   = $UserLogin->_parent["user_settings"]->image;
		}else{
			$returnData['success'] = 0;
			$returnData['reason'] = 'FAIL_UPDATE_IMAGE_PROFILE';
		}
	}
}else {
	$returnData['success'] = 0;
	$returnData['reason'] = 'INVALID_CREDENTIALS';
}

echo json_encode($returnData);