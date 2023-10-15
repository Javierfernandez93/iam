<?php define("TO_ROOT","../../../..");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

if($data["names"])
{
	if($data["last_name"])
	{
		$UserData = new DummieTrading\UserData;

		$sql = "SELECT user_login_id FROM user_data WHERE MATCH (names) AGAINST ('+".$data["names"]."+' IN BOOLEAN MODE)";
		$name_filter = $UserData->connection()->rows($sql);

		$sql = "SELECT user_login_id FROM user_data WHERE MATCH (last_name) AGAINST ('+".$data["last_name"]."+' IN BOOLEAN MODE)";
		$last_name_filter= $UserData->connection()->rows($sql);

		if($data["second_last_name"]){
			$sql = "SELECT user_login_id FROM user_data WHERE MATCH (second_last_name) AGAINST ('+".$data["second_last_name"]."+' IN BOOLEAN MODE)";
			$second_last_name_filter= $UserData->connection()->rows($sql);
		}else{
			$second_last_name_filter;
		}


		unset($UserData);

		if($name_filter && $last_name_filter){
			$exist=false;
			$coincidences=array();
			foreach ($name_filter as $key => $name_user) {					
				foreach ($last_name_filter as $second_key => $second_user) {
					if ($name_user["user_login_id"]==$second_user["user_login_id"]){
						$coincidences[$name_user["user_login_id"]]=$name_user["user_login_id"];						}
				}					
			}


			//creo una variable de coincidencias para checar el 

			if($second_last_name_filter){
				//apellido Materno
				foreach ($second_last_name_filter as $key => $last_name) {
					if(array_key_exists($last_name["user_login_id"], $coincidences))
						$exist=true;
				}

			}

			if($exist)
			{
				$data["s"]=0;
				$data["r"]='NOMBRE YA REGISTRADO SI NO RECUERDA SU CONTRASEÑA COMUNICATE A NUESTRAS OFICINAS';
			} else {
				$data["s"]=1;
				$data["r"]='NOMBRE DISPONIBLE';
			}
		} else {
			$data["s"]=1;
			$data["r"]='NOMBRE DISPONIBLE';
		}
	} else {
		$data["s"]=0;
		$data["r"]='APELLIDO PATERNO REQUERIDO';
	}
} else {
	$data["s"]=0;
	$data["r"]='NOMBRE REQUERIDO';
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 