<?php define("TO_ROOT", "../../../..");

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once TO_ROOT . "/system/core.php";

$returnData = Array();
$UserLogin = new Umbrella\UserLogin();

if($UserLogin->logged === true)
{
	$Cart = new Jcart\Cart();

	// $Cart->delete();
	// die();
	if($Cart->loaded)
	{

		$CatalogQualificationType = new Umbrella\CatalogQualificationType();
		if($CatalogQualificationType->loadWhere('user_login_id = ? and status = ?',[$UserLogin->company_id,'1'])){

			$Product = new Umbrella\Product();
			$BuyPerUser = new Umbrella\BuyPerUser();

			if($CatalogQualificationType->DineroExpress){
				//busco si tiene una compra de primera fase
				if($BuyPerUser->foundPurchaseBybuyFrom($UserLogin->company_id,5)){
					$products  = $Product->getAll();
					$Cart->buy_type= "extra";
					$Cart->buy();
					if($BuyPerUser->foundPurchaseBybuyFrom($UserLogin->company_id,8))
						$products  = $Product->getDiscounts($products,20,8);
					else
						$products  = $Product->getDiscounts($products,15,5);

				}else if($BuyPerUser->foundPurchaseBybuyFrom($UserLogin->company_id,8)){
					//si tengo una compra de segunda fase le doy el 20%
					$products  = $Product->getAll();
					$products  = $Product->getDiscounts($products,20,8);
					$Cart->buy_type= "extra";
					$Cart->buy();
				}else{
					$products = $Product->getProductsExpressMoney();
					$Cart->buy_type= "qualification";
					$Cart->buy();
				}							
			}else if($CatalogQualificationType->RedRotativa){
				$products = $Product->getAll();
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
			}elseif ($CatalogQualificationType->FastTrack) {
				$products = $Product->getAll();				
			}elseif ($CatalogQualificationType->Franquicia) {
				$products = $Product->getAll();
			}elseif ($CatalogQualificationType->MercadoSocial) {
				$SocialMarket = new Umbrella\SocialMarket();

				$disccounts = $SocialMarket->foundDiscount($UserLogin->company_id);
				$products  = $Product->getAll();
				$products  = $Product->getDiscounts($products,$disccounts["discounts"],6);


			}else if ($CatalogQualificationType->RotativoMercadeoSocial) {
				$products = $Product->getAll("WHERE product.active='1' AND (product.money_express='0' OR product.product_id=216 OR product.product_id=217)  AND product.price_public < 636 ORDER BY product.price_public DESC");
				$products = $Product->getDiscounts($products,0,6);
			}else if($CatalogQualificationType->RotativoDineroExpress){
				
				$products = $Product->getAll();
			}

			$returnData['html'] = getHtml($Cart->getItems(),$products,$packages,$UserLogin,$disccounts,$Cart,$CatalogQualificationType,(new Umbrella\BuyPerUser()));
			$returnData['success'] = 1;
			$returnData['reason'] = 'ITEMS_OK';
		}else{
			$returnData['html'] = getHtmlNotChoice();
			$returnData['success'] = 0;
			$returnData['reason'] = 'DONT_HAS_PACKAGE_CHOICE';
		}
			
	} else {
		$returnData['success'] = 0;
		$returnData['reason'] = 'NOT_CART';
	}
} else {
	$returnData['success'] = 0;
	$returnData['reason'] = 'INVALID_CREDENTIALS';
}

function getHtmlSearchEngine($items = false,$UserLogin = false,$BuyPerUser = false)
{
	$html = false;
	$html .= '<div class="box-search-engine">';
	$html .= '<div class="col-md-3">';
	$html .= '<input type="text" class="form-control search box-input" placeholder="Buscar">';
	$html .= '<select class="search-kind form-control box-select">';
	$html .= '<option value="title" selected>Nombre</option>';
	$html .= '<option value="product_id">Código</option>';
	$html .= '<option value="line">Línea</option>';
	$html .= '</select>';
	$html .= '</div>';
	$html .= '<div class="col-md-3">';
	$html .= '<select class="form-control min box-select">';
	$html .= '<option value="" disabled selected>Precio mínimo</option>';
	$html .= '<option value="100">100</option>';
	$html .= '<option value="200">200</option>';
	$html .= '<option value="300">300</option>';
	$html .= '<option value="500">500</option>';
	$html .= '<option value="1000">1000</option>';
	$html .= '<option value="2000">2000</option>';
	$html .= '</select>';
	$html .= '';
	$html .= '<select class="form-control max box-select">';
	$html .= '<option value="" disabled selected>Precio máximo</option>';
	$html .= '<option value="200">200</option>';
	$html .= '<option value="300">300</option>';
	$html .= '<option value="500">500</option>';
	$html .= '<option value="1000">1000</option>';
	$html .= '<option value="2000">2000</option>';
	$html .= '<option value="3000">3000</option>';
	$html .= '</select>';
	$html .= '</div>';
	$html .= '<div class="col-md-3">';
	$html .= '<div class="radio">';
	$html .= '<label>';
	$html .= '<input type="radio" class="item-kind" name="optionsRadios" id="optionsRadios1" value="2" checked>';
	$html .= 'Productos';
	$html .= '</label>';
	$html .= '</div>';
	if(!$items && !$UserLogin->_data['user_account']['qualify'] && !$BuyPerUser->hasBuyOnthisMoth($UserLogin->company_id))
	{
		$html .= '<div class="radio">';
		$html .= '<label>';
		$html .= '<input type="radio" class="item-kind" name="optionsRadios" id="optionsRadios2" value="1">';
		$html .= 'Paquetes';
		$html .= '</label>';
		$html .= '</div>';

	}
	$html .= '</div>';
	$html .= '<div class="col-md-3">';
	$html .= '<div class="button-search">Buscar</div>';
	$html .= '</div>';
	$html .= '</div>';

	return $html;
}
function getHtmlQualified($UserLogin,$Cart)
{
	$html = false;
	if($UserLogin->_data['user_account']['qualify'])
	{
		if(!$Cart->money_express){

			$html .= '<div class="box-plan-qualified margin-b-20">';
			$html .= '¡Felicidades! Ahora estas calificado y puedes disfrutar de descuentos';
			if($UserLogin->_data['user_account']['package_choice'] == 8)
			{
				$html .= '<div class="box-plan-qualified-plan-minimun">';
				$html .= '¡Ahora tienes el 15% de descuento en precio distribuidor en todas las líneas!';
				$html .= '</div>';
			} else if($UserLogin->_data['user_account']['package_choice'] >= 9) {
				$html .= '<div class="box-plan-qualified-plan-maximum">';
				$html .= '<div class="box-amazing-discount">Organica 50% (exepto 5to elemento y nitro al 40%) en precio público</div>';
				$html .= '<div class="box-amazing-discount">Biometrica 50% en precio público</div>';
				$html .= '<div class="box-amazing-discount">Metabolica 40% (exepto Caraluma y Glucomanano al 50%) en precio público</div>';
				$html .= '<div class="box-amazing-discount">Veggie 50% en precio público</div>';
				$html .= '<div class="box-amazing-discount">Ganofood 33% (exepto Eggs Perfect y Natural Meat al 21%) en precio público</div>';
				$html .= '<div class="box-amazing-discount">4Planet 30% en precio público</div>';
				$html .= '</div>';
			}
			$html .= '</div>';
		}


	}
	return $html;
}
function getHtmlNotChoice()
{
	$html = false;
	$html .= '<div class="box-plan-container box-none-plan">';
	$html .= 'No has seleccionado ningun plan para calificar<br><small>Es necesario saber cúal será tu calificación de este mes</small>';
	// $html .= '<div class="button-get-plans">Seleccionar Plan</div>';
	$html .= '</div>';
	return $html;
}

function getHtmlFromPurchase($user_login_id,$buy_from,$disccounts=false){
	if($user_login_id && $buy_from){

		$Cart = new Jcart\Cart();
		
		$html =false;
		$BuyPerUser = new Umbrella\BuyPerUser();

		//dinero Express
		if($buy_from=='5'){
			// $response = $BuyPerUser->foundPurchaseBybuyFrom($user_login_id,$buy_from);
			
			if($BuyPerUser->foundPurchaseBybuyFrom($user_login_id,$buy_from)){
				if($BuyPerUser->foundPurchaseBybuyFrom($user_login_id,8))
					$html.="<div class='col-md-12 text-center'>¡Felicidades! Ya cuentas con tu compra de Dinero Express Segunda Fase. Obtén productos con 20% de descuento sobre precio público.</div>";
				else								
					$html.="<div class='col-md-12 text-center'>¡Felicidades! Ya cuentas con tu compra de Dinero Express. Si aún no haces tú consumo de Red Rotativa Dinero Express puedes hacerlo tomando en cuenta esta compra o bien obtén productos con 15% de descuento sobre precio público.</div>";
				// $html.="<div class='col-md-12 text-center'>ó adquiere productos con un 15% de Descuento</div>";
			}else{
				if($BuyPerUser->foundPurchaseBybuyFrom($user_login_id,8))
					$html.="<div class='col-md-12 text-center'>¡Felicidades! Ya cuentas con tu compra de Dinero Express Segunda Fase. Obtén productos con 20% de descuento sobre precio público.</div>";
			}

		}else if($buy_from=='6'){
			//mercadeo Social
			//busco cuantos puntos tiene 
			$UserAccountSocialMarket = new Umbrella\UserAccountSocialMarket();

			$UserAccountSocialMarket->loadWhere("user_login_id=?",$user_login_id);
		
			$html.='<div class="col-md-12 text-left">';
				$html.='<div class="col-md-12">';
					$html.='<div class="col-md-12" >Nombramiento: <span class="social-market-range">'.$disccounts["range"].'</span></div>';
					$html.='<div class="col-md-12" >Volumen Personal: <span class="social-market-range">'.$UserAccountSocialMarket->total_points .' puntos.<span class="social-market-range"></div>';
					$html.='<div class="col-md-12" >Volumen de Grupo: <span class="social-market-range">'.$disccounts["points"] .' puntos.<span class="social-market-range"></div>';
					$html.='<div class="col-md-12" >Descuento: <span class="social-market-range"> '.$disccounts["discount"].'%<span class="social-market-range"></div>							';
				$html.='</div>';
			$html.='</div>';


		}else if($buy_from=='8'){
			//busco si tiene una compra de redExpress
			$response = $BuyPerUser->foundPurchaseBybuyFrom($user_login_id,5);
			
			//si tiene compra Le digo que puede utilizar esa compra para hacer los 990 Pesos
			if($response){
				$response = $BuyPerUser->foundPurchaseBybuyFrom($user_login_id,$buy_from);

				if($response){
					$Cart->minimal_ammount = 999;
					$Cart->buy();
					$html.="<div class='col-md-12 text-center'>Te Recordamos que tu compra de Red Rotativa Dinero Expres es comprando 999 MXN.</div>";
				}else{
					$Cart->minimal_ammount = 709;
					$Cart->buy();
					$html.="<div class='col-md-12 text-center'>¡Felicidades! Ya cuentas con tu compra 1ra Fase de Dinero Express. Puedes hacer tú consumo de Red Rotativa Dinero Expres comprando 709 MXN adicionales.</div>";
				}


			}else{
				if($response = $BuyPerUser->foundPurchaseBybuyFrom($user_login_id,8)){
					$Cart->minimal_ammount = 450;
					$Cart->buy();
					$html.="<div class='col-md-12 text-center'>¡Felicidades! ya te encuentras activo en Dinero Express Segunda Fase obtén otra posición por tan solo 450 MXN.</div>";
				}else{
					$Cart->minimal_ammount = 999;
					$Cart->buy();
					$html.="<div class='col-md-12 text-center'>Te Recordamos que tu compra de Red Rotativa Dinero Express es comprando 999 MXN.</div>";	
				}
			}
		}
		return $html;
	}
}

function getHtml($items,$products = false,$packages = false,$UserLogin = false,$disccounts = false,$Cart = false,$CatalogQualificationType = false,$BuyPerUser = false)
{

	$html = false;
	$extraHtml = false;
	$NetworkFastTrack = new Umbrella\NetworkFastTrack();
	$BuyPerUser = new Umbrella\BuyPerUser();
	$qualificationMessage="Actualmente tu pedido es para activarte en";
	if($CatalogQualificationType->DineroExpress){
		if($BuyPerUser->foundPurchaseBybuyFrom($UserLogin->company_id,5) ||  $BuyPerUser->foundPurchaseBybuyFrom($UserLogin->company_id,8))
			$qualificationMessage="Actualmente tu pedido será registrado como compra excedente";

		$qualify_type = "Dinero Express ";
		$extraHtml = getHtmlFromPurchase($UserLogin->company_id,5);
		// $products = ($extraHtml) ? false : $products;

	}else if($CatalogQualificationType->RedRotativa){
		$qualify_type = "Red Rotativa";
	}elseif ($CatalogQualificationType->FastTrack) {
		$qualify_type = "FastTrack";
	}elseif ($CatalogQualificationType->Franquicia) {
		$qualify_type = "Franquicia Veggie";
	}elseif ($CatalogQualificationType->MercadoSocial) {
		$qualify_type = "Mercadeo Social ";
		$extraHtml = getHtmlFromPurchase($UserLogin->company_id,6,$disccounts);
	}elseif ($CatalogQualificationType->RotativoMercadeoSocial) {
		$qualify_type = "Rotativo Mercadeo Social";
	}elseif ($CatalogQualificationType->RotativoDineroExpress) {
		if($BuyPerUser->foundPurchaseBybuyFrom($UserLogin->company_id,8)){
			$qualify_type = "Rotativo Dinero Express.";
		}else
			$qualify_type = "Rotativo Dinero Express";
			
		$extraHtml = getHtmlFromPurchase($UserLogin->company_id,8);
	}

	// $html .= '<div class="box-plan-containers box-change-planes"><div class="col-md-12">¿Selecciona como sera tu activacion?</div>';
	$html .= '<div class="box-plan-containers box-change-planes">';
		$html .= '<div class="col-md-12 ">';
			$html .= '<h3>'.$qualificationMessage.' <b><h1>'.$qualify_type.'</h1></b></h3>';
			$html .= $extraHtml ;
		$html .= ' </div>';	
	$html .= '</div>';
	
	
	if($UserLogin->_data["user_account"]["franchise"]!='1'){
		$html .= '<div class="box-plan-container box-change-plan margin-b-6 col-md-12" style="background-color: #495058 !important;">¿Deseas cambiar el plan que elegiste para calificar?<br>';
		$html .= '<div class="button-change-plan button-get-plans ">Cambiar Plan</div>';
		$html .= '</div>';
		
	}


	// $html .= getHtmlSearchEngine($items,$UserLogin,$BuyPerUser);
	if($CatalogQualificationType->RedRotativa)
		$html .= getHtmlQualified($UserLogin,$Cart);

	if($products)
	{
		
		if($Cart->money_express || $Cart->kind){
			$disccounts=false;
		}
		if($CatalogQualificationType->RotativoMercadeoSocial){
				$html .= '<div class="box-plan-container box-change-plan margin-b-6 col-md-12" style="background-color: #495058 !important;">';
						$html .='<h3>Puedes calificar seleccionando un producto con valor mínimo de $480 MXN</h3>';
				$html .= '</div>';

		}
		foreach ($products as $key => $product)
		{


			$html .= '<div class="col-md-4 wow bounceInUp" data-wow-duration="500ms" data-wow-delay="0s">';
			$html .= '<div class="box-products-points">';
			$html .= $product['points'];
			$html .= '</div>';
			$html .= '<div class="box-products text-center col-md-12">';
			$html .= '<div class="col-md-12 box-products-price">';
			$html .= '<div class="col-md-7 box-products-price-distributor">';
			$has_disccount = false;
			
			
			if($disccounts)
			{
				
				if($CatalogQualificationType->MercadoSocial){
					$has_disccount = 1;
					$ammount = round(($product["price_public"] - ($product["price_public"] * $disccounts["discount"]) / 100),0, PHP_ROUND_HALF_UP);
					
					$html .=$ammount;	
				}else{

					if($product["discount_from"]=='0'){
						$has_disccount = 2;
						$ammount = round(($product["price_distributor"] - ($product["price_distributor"] * $product["discount"]) / 100),0, PHP_ROUND_HALF_UP);
					}else{
						//precio Publico
						$has_disccount = 1;
						$ammount = round(($product["price_public"] - ($product["price_public"] * $product["discount"]) / 100),0, PHP_ROUND_HALF_UP);
					}

					$html .=$ammount;			
				}


			}else{
				if($CatalogQualificationType->DineroExpress)
					$html .= $product['price_distributor'];
					// if($BuyPerUser->foundPurchaseBybuyFrom($CatalogQualificationType->user_login_id,5) || $BuyPerUser->foundPurchaseBybuyFrom($CatalogQualificationType->user_login_id,8))
					// else{
												
					// 	$html .= round(($product["price_distributor"] - ($product["price_distributor"] * 10) / 100),0, PHP_ROUND_HALF_UP)+3; //$product['price_distributor'];
						
					// }
				else
					$html .= $product['price_distributor'];
				
			} 
			$html .= '</div>';
			$html .= '<div class="col-md-5 box-products-others-price">';
			$html .= '<div class="col-md-12 box-products-price-public">';
			$html .= $product['price_public'];
			$html .= '</div>';
			$html .= '<div class="col-md-12 box-products-price-off">';
			
			if($has_disccount == 1){
				if($CatalogQualificationType->MercadoSocial)
					$html .= '<strike>'.$product['price_public'].'</strike> <div class="box-disccount-minimizated">- '.$disccounts["discount"].'% Público</div>';
				else	
					$html .= '<strike>'.$product['price_public'].'</strike> <div class="box-disccount-minimizated">- '.$product["discount"].'% Público</div>';
			}

			else if($has_disccount == 2)
			$html .= '<strike>'.$product['price_distributor'].'</strike> <div class="box-disccount-minimizated">- '.$product["discount"].'% Distrib.</div>';

			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';
			if(!$product['image'])
			$html .= '<div class="box-image box-no-image">';
			else
			$html .= '<div class="box-image" style="background-image:url(../../'.$product['image'].')">';

			$html .= '<div class="box-products-title">';
			$html .= $product['title'];
			$html .= '</div>';
			$html .= '<div class="box-products-line">';
			$html .= $product['line'];
			$html .= '</div>';
			$html .= '<div class="box-products-code">'.$product['product_id'].'</div>';
			$html .= '</div>';
			$html .= '<div class="box-products-description">';
			$html .= $product['presentation'];
			$html .= '</div>';
			$html .= '<div class="box-buttons">';

			$html .= '<div class="col-md-7 box-add">';
			if($product["visible"]=="1")
				$html .= '<button class="button-add" data-type="product" data-id="'.$product['product_id'].'">Añadir</button>';
			else
				$html .= '<button class="btn btn-warning " >Producto Agotado</button>';
			
			$html .= '</div>';
			$html .= '<div class="col-md-5 box-ammount-of-products">';
			$html .= '<input type="text" class="ammount-of-products" placeholder="1">';
			$html .= '</div>';

			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';
		}
	}
	if($packages)
	{
		foreach ($packages as $key => $package)
		{
			$html .= '<div class="col-md-4 wow bounceInUp" data-wow-duration="500ms" data-wow-delay="0s">';
			$html .= '<div class="box-products text-center col-md-12">';
			$html .= '<div class="col-md-12 box-products-price">';
			$html .= '<div class="col-md-7 box-products-price-distributor">';
			$html .= $package['price_distributor'];
			$html .= '</div>';
			$html .= '<div class="col-md-5 box-products-others-price">';
			$html .= '<div class="col-md-12 box-products-price-public">';
			$html .= $package['price_public'];
			$html .= '</div>';
			$html .= '<div class="col-md-12 box-products-price-off">';
			$html .= $package['price_public'];
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';
			if(!$package['image'])
			$html .= '<div class="box-image box-no-image">';
			else
			$html .= '<div class="box-image box-image-package" style="background-image:url(../../'.$package['image'].')">';
			$html .= '<div class="box-products-title">';
			$html .= $package['title'];
			$html .= '</div>';
			$html .= '<div class="box-products-line">Paquete</div>';
			$html .= '<div class="box-products-code">'.$package['package_id'].'</div>';
			$html .= '</div>';
			$html .= '<div class="box-products-description box-package-description">';
			$products = explode(',', $package['products']);
			foreach ($products as $key => $product)
				if($product) $html .= '<div class="box-product-package">'.$product.'</div>';
			$html .= '</div>';
			$html .= '<div class="box-buttons">';
			$html .= '<div class="col-md-7 box-add">';
			$html .= '<button class="button-add" data-type="package" data-id="'.$package['package_id'].'">Añadir</button>';
			$html .= '</div>';
			$html .= '<div class="col-md-5 box-ammount-of-products">';
			$html .= '<input type="text" class="ammount-of-products" placeholder="1">';
			$html .= '</div>';

			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';
		}
	}
	$html .= '</div>';

	return $html;
}

echo json_encode($returnData); ?>