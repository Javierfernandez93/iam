<?php define("TO_ROOT","../../../..");

require_once TO_ROOT. "/system/core.php";
$data = HCStudio\Util::getHeadersForWebService();

if($data["sponsor_id"] || $data["colocation_id"]){

	// primero verifico que exista en su red 
	$UserAccount = new DummieTrading\UserAccount();
	$sponsor_id = $data['sponsor_id'];
	$sponsor = $data['colocation_id'];
	$niveles=1;
	$salir=false;
	$success = 1;

	while($sponsor!=$sponsor_id && $sponsor!=0){		
		$UserAccount->cargarDonde("user_login_id=?",$sponsor);
		$sponsor=$UserAccount->sponsor_id;
		$niveles++;
	}
	if($sponsor==$sponsor_id){
	 	
	 	$UserData=new Talento\UserData();
	 	
	 	$data["colocation_name"]= $UserData->getNames($data["colocation_id"]);
	 	$data["sponsor_name"]= $UserData->getNames($data["sponsor_id"]);
	 	$data["s"]=1;	 	
		$data["r"]="Seras colocado debajo de <b>".$data["colocation_name"]."</b> ".$niveles." Niveles debajo de <b>".$data["sponsor_name"]."</b>";
		
	}else{
		$data["s"]=0;
		$data["r"]="El id ".$data["colocation_id"]." no se encuentra en tu red";
	}

}else{
	$data["s"]=0;
	$data["r"]='No se puede colocar con los datos otorgados';

}
echo json_encode(HCStudio\Util::compressDataForPhone($data)); 

?>
