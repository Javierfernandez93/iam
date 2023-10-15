<?php define("TO_ROOT", "../../../..");

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once TO_ROOT . "/system/core.php";

$returnData = Array();
$UserLogin = new Umbrella\UserLogin();

$returnData['id'] = HCStudio\Util::getVarFromPGS('id');
$returnData['type'] = HCStudio\Util::getVarFromPGS('type');
$returnData['ammount'] = HCStudio\Util::getVarFromPGS('ammount');

if($UserLogin->logged === true)
{
	if($returnData['type'])
	{
		if($returnData['id'])
		{
			$BuyPerUser = new Umbrella\BuyPerUser();
			$returnData['ammount'] = ($returnData['ammount'] > 0) ? $returnData['ammount'] : 1;
			$Cart = new Jcart\Cart();

			$ClassName = str_replace(' ', '', "Umbrella\ ".ucfirst($returnData['type']));
			$Class = new $ClassName;
			$Class->setId($returnData['id']);

			$disccounts = false;

			$CatalogQualificationType = new Umbrella\CatalogQualificationType();
			if($CatalogQualificationType->loadWhere('user_login_id = ? and status = ?',[$UserLogin->company_id,'1'])){

				$money_express = false;
				$has_purchase  = false;

				if($CatalogQualificationType->DineroExpress=='1'){
					$disccounts=false; $money_express= true;
				}

				if($money_express){
					if($BuyPerUser->foundPurchaseBybuyFrom($UserLogin->company_id,8) || $BuyPerUser->foundPurchaseBybuyFrom($UserLogin->company_id,5))
						$has_purchase= true;
				}
				
				$totalItems=0;
				if(count($Cart->item)){
					foreach ($Cart->item as $key => $item) {
						if($item["line"]!='Herramientas'){
							$totalItems += $item["products"];
						}						
					}
				}
				

				$totalItems = ($Class->line=='Herramientas') ? $totalItems : ($totalItems+$returnData['ammount']) ;
				
				if($CatalogQualificationType->DineroExpress && $has_purchase==false  &&  $totalItems>1)
				{

					$returnData['success'] = 0;
					$returnData['reason'] = 'ERROR_MONEY_EXPRESS';
					// $returnData['html'] = '<div>No es posible seleccionar más de un producto con activación Cliente Preferente.</div>';
				}else {

					if($Cart->kind){
						$points = $Class->points;
					}else
						$points = ($disccounts) ? 0 : $Class->points;			


					if($CatalogQualificationType->RedRotativa){
						if($UserLogin->_data["user_account"]["qualify"]){
							$disccounts["Organica"]     = '50' ;
					 		$disccounts["Biometrica"]   = '50' ;
					 		$disccounts["Veggie"]       = '50' ;
					 		$disccounts["Metabolica"]   = '40' ;
					 		$disccounts["GanoFood"]     = '33' ;		 		
					 		$disccounts["Mer"]			= '0'  ;
					 		$disccounts["4Planet"]		= '15'  ;
					 		$disccounts["Herramientas"] = '0'  ;
						}
					}


					$points = ($disccounts) ? 0 : $Class->points;	

					if($CatalogQualificationType->DineroExpress)
						$points = 0;

					$Class->JoinAtributos([
						'ammount' => getAmmount($Class,$disccounts,$Cart,$CatalogQualificationType),
						'points' => $points,
						'type' => $Class->getTableName(),
						'discount' =>$Class->discount,
						'id' => $Class->getId()
					]);

					if($returnData['type'] == 'package')
					{
						$Class->JoinAtributos([
							'products_package' => $Class->products,
						]);
					}

					if($Cart->loaded)
					{
						
						$Cart->setItem($Class,$returnData['ammount']);
						$Cart->buy();

						$returnData['success'] = 1;
						$returnData['reason'] = 'ADDED_OK';
					} else {
						$returnData['success'] = 0;
						$returnData['reason'] = 'NOT_CART';
					}
				}

			}else{
				$returnData['success'] = 0;
				$returnData['reason'] = 'DONT_HAS_PACKAGE_CHOICE';
			}
			
		} else {
			$returnData['success'] = 0;
			$returnData['reason'] = 'NOT_ID';
		}
	} else {
		$returnData['success'] = 0;
		$returnData['reason'] = 'NOT_AMMOUNT';
	}
} else {
	$returnData['success'] = 0;
	$returnData['reason'] = 'INVALID_CREDENTIALS';
}

// function getAmmount($Class,$disccounts,$Cart)
// {

	
// 	if($Cart->kind){
// 		// $ammount = $Class->price_public-(($Class->price_public*15)/100);
// 		$ammount = $Class->price_distributor;

// 	}else{

// 		if($disccounts)
// 			$ammount = $Class->price_excedente;		
// 		else $ammount = $Class->price_distributor;
// 	}

// 	if($Cart->buy_fast_track){
// 		if($disccounts)
// 			$ammount = $Class->price_excedente;		
// 		else $ammount = $Class->price_distributor;
// 	}

// 	return $ammount;
// }
function getAmmount($Class,$disccounts,$Cart,$CatalogQualificationType)
{

	if($disccounts)
	{

		if($Class->discount_from=='0'){
			$has_disccount = 2;
			$ammount = round(($Class->price_distributor - ($Class->price_distributor * $Class->discount) / 100),0, PHP_ROUND_HALF_UP);
		}else{
			//precio Publico
			$has_disccount = 1;
			$ammount = round(($Class->price_public - ($Class->price_public * $Class->discount) / 100),0, PHP_ROUND_HALF_UP);
		}
	} else if($CatalogQualificationType->MercadoSocial){
		$SocialMarket = new Umbrella\SocialMarket();
		$discounts = $SocialMarket->foundDiscount($CatalogQualificationType->user_login_id);		
		$discounts = $discounts["discount"];
		
		$ammount = round(($Class->price_public-($Class->price_public*$discounts)/100),0, PHP_ROUND_HALF_UP);

		// $products[$key]["price_distributor"]

	}else if($CatalogQualificationType->RotativoMercadeoSocial){
		
		
		$ammount = round($Class->price_public);

		// $products[$key]["price_distributor"]

	}else{

		if($CatalogQualificationType->DineroExpress){

			if($Cart->buy_type=='qualification')
				if($Class->line!='Herramientas')		
					$ammount = 290;
				else
					$ammount = $Class->price_distributor;
			else{
				$BuyPerUser = new Umbrella\BuyPerUser();
				if($BuyPerUser->foundPurchaseBybuyFrom($CatalogQualificationType->user_login_id,5)){
					$ammount = round(($Class->price_public-($Class->price_public * 15 / 100)),0, PHP_ROUND_HALF_UP);
				}else if($BuyPerUser->foundPurchaseBybuyFrom($CatalogQualificationType->user_login_id,8))
					$ammount = round(($Class->price_public-($Class->price_public * 20 / 100)),0, PHP_ROUND_HALF_UP);
			}
			
		// round(($product["price_distributor"] - ($product["price_distributor"] * 10) / 100),0, PHP_ROUND_HALF_UP)+3;
		}else
			$ammount = $Class->price_distributor;
	}
	
	return $ammount;
}
echo json_encode($returnData); ?>