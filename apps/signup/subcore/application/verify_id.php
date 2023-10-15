<?php define("TO_ROOT","../../../..");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

if($data["sponsor_id"])
{	
	$UserData = new DummieTrading\UserData;

	if(is_numeric($data["sponsor_id"]))
	{
		if($user_data=$UserData->getNames($data["sponsor_id"])){
			$data["s"]=1;
			$data["sponsor_name"]=strtoupper($user_data);
			$data["r"]="Patrocinador encontrado";
		}else{
			$data["s"]=0;
			$data["r"]='Patrocinador no encontrado';
		}

	} else {
				$sql = "SELECT
							user_data.user_login_id,
							CONCAT_WS(' ',user_data.names,user_data.last_name,user_data.second_last_name) as names
						FROM
							user_data
						WHERE
							user_data.names 
						LIKE
							'%{$data["sponsor_id"]}%'";

			if($founds=$UserData->connection()->rows($sql)){
				$html="";
				$html.="<div class='form-group'>";
				$html.="<label for='exampleFormControlSelect1'>Selecciona a tu patrocinador</label>";
				$html.="<select class='form-control' id='user_login_id'>";
				foreach($founds as $Key=> $found){
					$html.="<option value=";
					$html.=$found["user_login_id"];
					$html.=">";
					$html.=$found['names'];
					$html.="</option>";
				}
				$html.="</select>";
				$html.="</div>";
				
				$data["s"]=1;
				$data["sponsor_name"]=$html;
				$data["r"]="Patrocinador encontrado";
			}else{
				$data["s"]=0;
				$data["r"]='Patrocinador no encontrado';
			}
	}
	
}else{
	$data["s"]=0;
	$data["r"]='Ingresa un id valido';
}



echo json_encode(HCStudio\Util::compressDataForPhone($data)); 

?>
