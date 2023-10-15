<?php define("TO_ROOT", "../../../..");

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once TO_ROOT . "/system/core.php";

$returnData = Array();
$UserLogin = new Umbrella\UserLogin();


$returnData['search_kind'] = HCStudio\Util::getVarFromPGS('search_kind');
$returnData['item_kind'] = HCStudio\Util::getVarFromPGS('item_kind');
$returnData['search'] = HCStudio\Util::getVarFromPGS('search');
$returnData['min'] = HCStudio\Util::getVarFromPGS('min');
$returnData['max'] = HCStudio\Util::getVarFromPGS('max');

if($UserLogin->logged === true)
{
	$Cart = new Jcart\Cart();
	if($Cart->loaded)
	{
		$BuyPerUser = new Umbrella\BuyPerUser();
		$CatalogQualificationType = new Umbrella\CatalogQualificationType();

		if($CatalogQualificationType->loadWhere('user_login_id = ? and status = ?',[$UserLogin->company_id,'1'])){
			$Product = new Umbrella\Product();

			if($CatalogQualificationType->DineroExpress){

				if($BuyPerUser->foundPurchaseBybuyFrom($UserLogin->company_id,5)){
					$products = (new Umbrella\Product())->getSearch($returnData['search'],$returnData['search_kind'],$returnData['min'],$returnData['max']);
					$Cart->buy_type= "extra";
					
					if($BuyPerUser->foundPurchaseBybuyFrom($UserLogin->company_id,8))
						$products  = $Product->getDiscounts($products,20,8);
					else
						$products  = $Product->getDiscounts($products,15,5);
					
				}else if($BuyPerUser->foundPurchaseBybuyFrom($UserLogin->company_id,8)){
					//si tengo una compra de segunda fase le doy el 20%
					$products = (new Umbrella\Product())->getSearch($returnData['search'],$returnData['search_kind'],$returnData['min'],$returnData['max']);
					$products  = $Product->getDiscounts($products,20,8);
					$Cart->buy_type= "extra";
				
				}else{
					$products = $Product->getProductsExpressMoney();
					$Cart->buy_type= "qualification";
				}	



			}else if($CatalogQualificationType->RedRotativa){
				$products = $Product->getSearch($returnData['search'],$returnData['search_kind'],$returnData['min'],$returnData['max']);
				if($UserLogin->_data["user_account"]["qualify"]){
					$disccounts["Organica"]     = '50' ;
			 		$disccounts["Biometrica"]   = '50' ;
			 		$disccounts["Veggie"]       = '50' ;
			 		$disccounts["Metabolica"]   = '40' ;
			 		$disccounts["GanoFood"]     = '20' ;		 		
			 		$disccounts["Mer"]			= '0'  ;
			 		$disccounts["4Planet"]		= '15'  ;
			 		$disccounts["Herramientas"] = '0'  ;
				}
			}elseif ($CatalogQualificationType->FastTrack) {
				$products = $Product->getSearch($returnData['search'],$returnData['search_kind'],$returnData['min'],$returnData['max']);			
			}elseif ($CatalogQualificationType->Franquicia) {
				$products = $Product->getSearch($returnData['search'],$returnData['search_kind'],$returnData['min'],$returnData['max']);
			}elseif ($CatalogQualificationType->MercadoSocial) {
				$SocialMarket = new Umbrella\SocialMarket();

				$disccounts = $SocialMarket->foundDiscount($UserLogin->company_id);
				// $disccounts = $disccounts["discounts"];
				// $products = $Product->getAll();
				$products = (new Umbrella\Product())->getSearch($returnData['search'],$returnData['search_kind'],$returnData['min'],$returnData['max']);
				$products =  $Product->getDiscounts($products,$disccounts["discounts"],6);


			}else if ($CatalogQualificationType->RotativoMercadeoSocial) {
				// $products = $Product->getAll();
				$products = (new Umbrella\Product())->getSearch($returnData['search'],$returnData['search_kind'],$returnData['min'],$returnData['max']);
				$products = $Product->getDiscounts($products,0,6);
			}else if($CatalogQualificationType->RotativoDineroExpress){
				// $products = $Product->getAll();
				$products = (new Umbrella\Product())->getSearch($returnData['search'],$returnData['search_kind'],$returnData['min'],$returnData['max']);

			}

			$returnData['success'] = 1;
			$returnData['reason'] = 'DATA_OK';
			// $products = (new Umbrella\Product())->getSearch($returnData['search'],$returnData['search_kind'],$returnData['min'],$returnData['max']);
			$returnData['html'] = getProductsHtml($products,$returnData['search'],$returnData['item_kind'],$returnData['search_kind'],$returnData['min'],$returnData['max'],$UserLogin,$Cart,$CatalogQualificationType,$disccounts);

		}
		
	} else {
		$returnData['success'] = 0;
		$returnData['reason'] = 'NOT_CART';
	}
} else {
	$returnData['success'] = 0;
	$returnData['reason'] = 'INVALID_CREDENTIALS';
}

function getHtmlSearchEngine($search = false,$item_kind = false,$search_kind = false,$min = false,$max = false)
{
	$html = false;
	$html .= '<div class="box-search-engine">';
	$html .= '<div class="col-md-3">';
	$html .= '<input type="text" class="form-control search box-input" value="'.$search.'" placeholder="Buscar">';
	$html .= '<select class="search-kind form-control box-select">';
	$html .= '<option value="title" '.(($search_kind == 'title') ? 'selected' :'').'>Nombre</option>';
	$html .= '<option value="product_id" '.(($search_kind == 'product_id') ? 'selected' :'').'>Código</option>';
	$html .= '<option value="line" '.(($search_kind == 'line') ? 'selected' :'').'>Línea</option>';
	$html .= '</select>';
	$html .= '</div>';
	$html .= '<div class="col-md-3">';
	$html .= '<select class="form-control min box-select">';
	$html .= '<option value="" disabled  '.((!$min) ? 'selected' :'').'>Precio mínimo</option>';
	$html .= '<option value="100" '.(($min == '100') ? 'selected' :'').'>100</option>';
	$html .= '<option value="200" '.(($min == '200') ? 'selected' :'').'>200</option>';
	$html .= '<option value="300" '.(($min == '300') ? 'selected' :'').'>300</option>';
	$html .= '<option value="500" '.(($min == '500') ? 'selected' :'').'>500</option>';
	$html .= '<option value="1000" '.(($min == '1000') ? 'selected' :'').'>1000</option>';
	$html .= '<option value="2000" '.(($min == '2000') ? 'selected' :'').'>2000</option>';
	$html .= '</select>';
	$html .= '';
	$html .= '<select class="form-control max box-select">';
	$html .= '<option value="" disabled '.((!$min) ? 'selected' :'').'>Precio máximo</option>';
	$html .= '<option value="200" '.(($max == '200') ? 'selected' :'').'>200</option>';
	$html .= '<option value="300" '.(($max == '300') ? 'selected' :'').'>300</option>';
	$html .= '<option value="500" '.(($max == '500') ? 'selected' :'').'>500</option>';
	$html .= '<option value="1000" '.(($max == '1000') ? 'selected' :'').'>1000</option>';
	$html .= '<option value="2000" '.(($max == '2000') ? 'selected' :'').'>2000</option>';
	$html .= '<option value="3000" '.(($max == '3000') ? 'selected' :'').'>3000</option>';
	$html .= '</select>';
	$html .= '</div>';
	$html .= '<div class="col-md-3">';
	$html .= '<div class="radio">';
	$html .= '<label>';
	$html .= '<input type="radio" class="item-kind" name="optionsRadios" id="optionsRadios1" value="2"  '.(($item_kind == '2') ? 'checked' : '').'>';
	$html .= 'Productos';
	$html .= '</label>';
	$html .= '</div>';
	$html .= '<div class="radio">';
	$html .= '<label>';
	$html .= '<input type="radio" class="item-kind" name="optionsRadios" id="optionsRadios2" value="1" '.(($item_kind == '1') ? 'checked' : '').'>';
	$html .= 'Paquetes';
	$html .= '</label>';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '<div class="col-md-3">';
	$html .= '<div class="button-search">Buscar</div>';
	$html .= '</div>';
	$html .= '</div>';

	return $html;
}
function getPackagesHtml($packages = false,$search = false,$item_kind = false,$search_kind = false,$min = false,$max = false,$disccounts = false)
{
	$UserLogin = new Umbrella\UserLogin();
	$html = false;
	$html .= getHtmlSearchEngine($search,$item_kind,$search_kind,$min,$max);
	if($packages)
	{
		foreach ($packages as $key => $package)
		{
			if ($package['package_id'] != 20 || ($package['package_id'] == 20 && $UserLogin->_data['user_account']['month'] == 0 && $UserLogin->_data['user_account']['franchise'] == '1')) {//si es posible tomar franq. subsidiada
				$html .= '<div class="col-md-4 wow bounceInUp" data-wow-duration="500ms" data-wow-delay="0s">';
				$html .= '<div class="box-products text-center col-md-12">';
				$html .= '<div class="col-md-12 box-products-price">';
				$html .= '<div class="col-md-7 box-products-price-distributor">';
				$html .= $package['price_distributor'];
				$html .= '</div>';
				$html .= '<div class="col-md-5 box-products-others-price">';
				$html .= '<div class="col-md-12 box-products-price-public">';
				$html .= $package['price_distributor'];
				$html .= '</div>';
				$html .= '<div class="col-md-12 box-products-price-off">';
				$html .= $package['price_provedor'];
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
	} else {

	}
	return $html;
}

function getHtmlFromPurchase($user_login_id,$buy_from,$disccounts=false){
	if($user_login_id && $buy_from){

		$Cart = new Jcart\Cart();
		
		$html =false;
		$BuyPerUser = new Umbrella\BuyPerUser();

		//dinero Express
		if($buy_from=='5'){
			if($BuyPerUser->foundPurchaseBybuyFrom($user_login_id,$buy_from)){
				if($BuyPerUser->foundPurchaseBybuyFrom($user_login_id,8))
					$html.="<div class='col-md-12 text-center'>¡Felicidades! Ya cuentas con tu compra de Dinero Express Segunda Fase. Obtén productos con 20% de descuento sobre precio público.</div>";
				else								
					$html.="<div class='col-md-12 text-center'>¡Felicidades! Ya cuentas con tu compra de Dinero Express. Si aún no haces tú consumo de Red Rotativa Dinero Express puedes hacerlo tomando en cuenta esta compra o bien obtén productos con 15% de descuento sobre precio público.</div>";
			}else{
				if($BuyPerUser->foundPurchaseBybuyFrom($user_login_id,8))
					$html.="<div class='col-md-12 text-center'>¡Felicidades! Ya cuentas con tu compra de Dinero Express. obten productos con 20% de descuento sobre precio publico.</div>";
			}

		}else if($buy_from=='6'){
			//mercadeo Social
			$UserAccountSocialMarket = new Umbrella\UserAccountSocialMarket();

			$UserAccountSocialMarket->loadWhere("user_login_id=?",$user_login_id);
			
			$html.='<div class="col-md-12 text-left">';
				$html.='<div class="col-md-12">';
					$html.='<div class="col-md-12" >Nombramiento: <span class="social-market-range">'.$disccounts["range"].'</span></div>';
					$html.='<div class="col-md-12" >Volumen Personal: <span class="social-market-range">'.$UserAccountSocialMarket->total_points .' puntos.<span class="social-market-range"></div>';
					$html.='<div class="col-md-12" >Volumen de Grupo: <span class="social-market-range">'.$disccounts["points"].' puntos.<span class="social-market-range"></div>';
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

function getProductsHtml($products = false,$search = false,$item_kind = false,$search_kind = false,$min = false,$max = false,$UserLogin = false,$Cart = false,$CatalogQualificationType = false,$disccounts=false )
{

	$html = false;
	$html .= getHtmlSearchEngine($search,$item_kind,$search_kind,$min,$max);

	$NetworkFastTrack = new Umbrella\NetworkFastTrack();
	$BuyPerUser = new Umbrella\BuyPerUser();
	$qualificationMessage="Actualmente tu pedido es para activarte en";

	if($CatalogQualificationType->DineroExpress){
		if($BuyPerUser->foundPurchaseBybuyFrom($UserLogin->company_id,5) ||  $BuyPerUser->foundPurchaseBybuyFrom($UserLogin->company_id,8))
			$qualificationMessage="Actualmente tu pedido sera resgistrado como compra excedente";

		$qualify_type = "Dinero Express";
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
		$qualify_type = "Rotativo Dinero Express 2da Fase";
		$extraHtml = getHtmlFromPurchase($UserLogin->company_id,8);
	}

	$html .= '<div class="box-plan-containers box-change-planes">';
		$html .= '<div class="col-md-12 ">';
			$html .= '<h3>'.$qualificationMessage.' <b><h1>'.$qualify_type.'</h1></b></h3>';
			$html .= $extraHtml ;
		$html .= ' </div>';	
	$html .= '</div>';
	
	if($UserLogin->_data["user_account"]["franchise"]!='1'){
		$html .= '<div class="box-plan-container box-change-plan margin-b-6" style="background-color: #495058 !important;">¿Deseas cambiar el plan que elegiste para calificar?';
		$html .= '<div class="button-change-plan button-get-plans">Cambiar Plan</div>';
		$html .= '</div>';
		
	}
	

	$html .= getHtmlQualified($UserLogin,$Cart);
	

	if($products)
	{
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
			} else{
				if($CatalogQualificationType->DineroExpress)
					if($BuyPerUser->foundPurchaseBybuyFrom($CatalogQualificationType->user_login_id,5) || $BuyPerUser->foundPurchaseBybuyFrom($CatalogQualificationType->user_login_id,8))
						$html .= $product['price_distributor'];
					else
						$html .= round(($product["price_distributor"] - ($product["price_distributor"] * 10) / 100),0, PHP_ROUND_HALF_UP)+3; //$product['price_distributor'];
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
			// $html .= '<button class="button-add" data-type="product" data-id="'.$product['product_id'].'">Añadir</button>';
			$html .= '</div>';
			$html .= '<div class="col-md-5 box-ammount-of-products">';
			$html .= '<input type="text" class="ammount-of-products" placeholder="1">';
			$html .= '</div>';

			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';
		}
	} else {

	}
	return $html;
}
echo json_encode($returnData); ?>