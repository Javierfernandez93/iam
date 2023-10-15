<?php define("TO_ROOT", "../../../..");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Umbrella\UserLogin;


if($UserLogin->logged === true)
{
	$data["cantidad"] = ($data["cantidad"]==0) ? 1 :$data["cantidad"];  

	if($data["product_id"]){
		if($data["cantidad"]){
			$Cart = new Jcart\Cart();
			
			//obtengo la instancia activa para agregar el producto
			$instance_active = $Cart->getInstanceActive();		
			$Cart->init($instance_active);

			if($items = $Cart->getItems()){	
				foreach($items as $key => $item){
					$Cart->deleteItem('product-'.$item['package_number_id']);
				}			
				
					
			}
		
			
			

	// print_r($Cart);
	// die;

			$ClassName = str_replace(' ', '', "Umbrella\ ".ucfirst("product"));
			$Class = new $ClassName;
			$Class->setId($data["product_id"]);
			$can_add_products=true;
			
			$kind=explode('-',$instance_active)[1];		
			
			

					
		

			//agrego los atributos del producto
			$Class->JoinAtributos([
				'ammount' => $Class->price_distributor,
				'product_description' => $Class->products,
				'type' => $Class->getTableName(),
				'discount' =>$Class->discount,
				'id' => $Class->getId()
			]);
			$Cart->setItem($Class,$data["cantidad"]);
			$Cart->buy();

			// $items = $Cart->getItems();

			
			
			//agrego los atributos del producto
			
		

			//checo el peso del total de los productos				
			$weight=0;
			foreach ($Cart->getInstances() as $key => $instance) {
				$Cart->init($instance);
				if($items = $Cart->getItems()){						
					foreach ($items as $key => $item) {										
						$weight+=($item['products']*$item['weight']);							
					}					
				}
			}

			// $weight;
			$Cart->total_weight=$weight;

			$Cart->getTaxByWeight();
			$Cart->putGlobalShippingAmmount($Cart->shipping_ammount);
			$Cart->init($instance_active);
			//checo el peso del total de los productos

			$data["total_ammount"]  = $total_ammount ;
			$data["total_shipping"] = $Cart->getShippingAmmount();
			$data["total_products"] = $real_products;
			$data["total_points"]   = $total_points;				
			$data['s'] = 1;
			$data['r'] = 'PRODUCT_ADD_OK';
					
		}else{
			$data['s'] = 0;
			$data['r'] = 'NOT_PRODUCT_AMMOUNT';
		}
	}else{
		$data['s'] = 0;
		$data['r'] = 'NOT_PRODUCT_ID';
	}	
} else {
	$data['s'] = 0;
	$data['r'] = 'INVALID_CREDENTIALS';
}
function getAmmount($Class,$instance_active)
{
	$explode=explode("-", $instance_active);
	$kind = $explode[1];
	$user_login_id = $explode[0];


	if($Class->line!='Herramientas'){

		if($kind=='1'){
			$ammount = $Class->price_distributor;
		}else if($kind=='2'){
			$ammount = $Class->price_excedente;
		}else if($kind=='super_puntos'){
			$ammount = round(($Class->price_public-($Class->price_public * 10 / 100)),0, PHP_ROUND_HALF_UP);
		}
	}else{

		$ammount = $Class->price_distributor;
	}	
	return $ammount;
}
echo json_encode(HCStudio\Util::compressDataForPhone($data)); ?>