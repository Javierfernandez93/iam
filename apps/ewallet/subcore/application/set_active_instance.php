<?php define("TO_ROOT", "../../../..");
require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Umbrella\UserLogin;

if($UserLogin->logged === true)
{

	if($data["instance"]){
		// $cartInstance = $data["instance"].'-'.$data["kind"];
		$Cart = new Jcart\Cart();
		
		$instances = $Cart->getInstances();
		if($instances){
			$hasInstance=false;
			foreach ($instances as $key => $instance) {
				if($instance==$data["instance"])
					$hasInstance=true;
			}

			if($hasInstance){
				$Cart = Jcart\Cart::getInstance($data["instance"]);
				$Cart->init($data["instance"]);
				$Cart->setInstanceActive($data["instance"]);

				$data['s'] = 1;
				$data['r'] = 'DATA_OK';
			}else{
				$data['s'] = 0;
				$data['r'] = 'INSTANCE_DOES_NOT_EXIST';
			}

		}else{
			$data['s'] = 0;
			$data['r'] = 'NOT_INSTANCES';
		}
	}else{
		$data['s'] = 0;
		$data['r'] = 'INVALID_INSTANCE';
	}	
} else {
	$data['s'] = 0;
	$data['r'] = 'INVALID_CREDENTIALS';
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); ?>