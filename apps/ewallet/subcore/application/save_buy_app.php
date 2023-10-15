<?php define("TO_ROOT", "../../../..");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Umbrella\UserLogin;

if($UserLogin->logged === true)
{
	$Cart = new Jcart\Cart;
	
	addProductToCart($Cart,$data['product_id'],$data['amount']); // Method to buy from app

	if(!empty($instances = $Cart->getInstances()))
	{	
		//checo el peso del total de los productos				
		$weight = 0;

		foreach ($Cart->getInstances() as $key => $instance) {
			$Cart->init($instance);
			$shipping_company = ($key==0) ? $Cart->shipping_company : $shipping_company;
			if($items = $Cart->getItems()){						
				foreach ($items as $key => $item) {										
					$weight+=($item['products']*$item['weight']);							
				}					
			}
		}
		
		$last_instance = explode("-",$instance)[1];

		$Cart->total_weight = $weight;

		$Cart->getTaxByWeight();
		$Cart->putGlobalShippingAmmount($Cart->shipping_ammount);		

		// Calculating reissue
		if($Cart->shipping_address == '1') {
				$zip_code = $UserLogin->_data['user_address']['zip_code'];
		} else {
				$zip_code = $UserLogin->_data['user_address_alternative']['zip_code'];
		}

		if($Cart->shipping_company!=3)
			$reissue = $Cart->getReissue($zip_code,$Cart->shipping_company);
		
		$shipping_ammount =	$Cart->takeGlobalShippingAmmount();
		//checo el peso del total de los productos
		
		$purchases=array();

		foreach ($instances as $key => $instance) {
			
			$Cart = Jcart\Cart::getInstance($instance);
			$Cart->init($instance);

			if($items = $Cart->getItems()){
				$explode =explode("-",$instance);
				$user_login_id = $explode[0];
				$kind = $explode[1];

				$buy_type = "qualification";

				if($kind=='1')
					$buy_from = "2";
				else if($kind=='super_puntos')
					$buy_from = "12";
				else if($kind=='2'){
					$buy_from = "2";
					$buy_type = "extra";
				}
					

				$BuyPerUser = new Umbrella\BuyPerUser();
				$date = date('Y-m-d H:i:s');

				$shipping = ($key==0) ? $shipping_ammount : 0;				
				$reissue= ($reissue["reissue"]) ? $reissue["reissue"] : 0;
				$reissue = ($key==0) ? $reissue : 0 ;

				$BuyPerUser->cargarArray([
					'user_login_id' => $user_login_id,
					'buy_type' => $buy_type,
					'goal' => '9',
					'payment_method' => $Cart->payment_method,
					'shipping_address' => $Cart->shipping_address,
					'products' => $Cart->getFormatedItems(),
					'ammount' => $Cart->getTotal(),
					'points' => $Cart->total_points,
					'shipping' => $shipping,
					'reissue' => $reissue,
					'shipping_company'=>$shipping_company,
					'charges' => (($tax = $Cart->getTax()) ? $tax : 0),
					'package_id' => '5',
					'date_create' => $date,
					'virtualPoints' => 1,
					'virtualDiscount' => 0,
					'buy_from' => $buy_from,
				]);

				if($BuyPerUser->save())
				{

					$UserData = new Umbrella\UserData();
					//guardo la compra y obtengo el numero de compra 
					$UserData->loadWhere("user_login_id=?",$user_login_id);
					$buyerName = strtoupper($UserData->getNames($user_login_id));

					$buy_id=$BuyPerUser->getId();

					$purchases[$buy_id]["buy_per_user_login_id"]= $BuyPerUser->getId();
					$purchases[$buy_id]["user_login_id"]= $BuyPerUser->user_login_id;
					$purchases[$buy_id]["buyerName"]= $buyerName;					
					$purchases[$buy_id]["total_to_pay"]= ($BuyPerUser->ammount+$BuyPerUser->shipping+$charges+$reissue);
					$purchases[$buy_id]["kind"]= $kind;
				
					unset($buyerName);
					

					//si el metodo de pago es paypal o OxxO actualizo Fechas
					if($Cart->payment_method == 1 || $Cart->payment_method == 3)
					{
						$prefix	= ($BuyPerUser->payment_method == 1) ? 'Paypal' : 'Oxxo';

						if($Cart->payment_method != 5) 
							$BuyPerUser->payment_reference = $prefix.'-'.$BuyPerUser->getId().'-'.$Cart->total_ammount;

						$BuyPerUser->payment_date = $date;
						$BuyPerUser->date_reg = $date;
						$BuyPerUser->save();
					}

					# Payment Methods
					if($Cart->payment_method == 1){
						$data["payment_html"] = getPayPalForm([
							"buy_per_user_login_id" => $BuyPerUser->getId(),
							"total_ammount" => $Cart->total_ammount+$reissue
						]);

					}else if($Cart->payment_method == 2)
						$data["payment_html"] = getDepositForm($purchases);
					else if($Cart->payment_method == 3) {
						$expiration_date = explode(" ",date('Y-m-d H:i:s', strtotime('+3 day')));
						$total_ammount = $Cart->total_ammount;
						$oxxo_data = array(
							'shipping_number' => $BuyPerUser->getId(),
							'expiration_date' => $expiration_date[0],
							'ammount' => ($Cart->total_ammount+$reissue),
						);

						$data["payment_html"] = getHtmlOxxo();

						$UserData->loadWhere("user_login_id=?",$user_login_id);
						$name = strtoupper($UserData->names." ". $UserData->last_name." ".$UserData->second_last_name);

						$data['oxxo_data'] = [
						    'code_id' => (new Umbrella\Oxxo(46,1))->createCodeId($oxxo_data),
						    'email' => $UserLogin->mail,
							'file_name' => 'Bill_From_'.$user_login_id,
							'buy_date' => $date,
							'shipping_number' => $buy_id,
							'expiration_date' => $expiration_date[0],
							'ammount' => ($total_ammount+$reissue),
							'name' => $name,
						];
					}
					$data['html'] = getHtml($purchases,$Cart->payment_method);
					$data['payment_method'] = $Cart->payment_method;
					

					//limpio la Variable $BuyPerUser
					unset($BuyPerUser);
					unset($UserData);

				}
				

			}

		}
		
		if($Cart->delete()) {

			if(count($purchases)>1){
				$purchases = array_values($purchases);
				
				//creo el envio compartido con la primera compra como principal
				foreach ($purchases as $key => $purchase) {
					if($key==0)
						$send_with_order=$purchase["buy_per_user_login_id"];

					$ShippingShared = new Umbrella\ShippingShared();

					$ShippingShared->buy_per_user_login_id = $purchase["buy_per_user_login_id"];
					$ShippingShared->user_login_id = $purchase["user_login_id"];
					$ShippingShared->send_with_order = $send_with_order;
					$ShippingShared->status = '0';
					$ShippingShared->save();
				}			
			}
			$Cart->deleteAll();
			$data['s'] = 1;
			$data['r'] = 'SAVE_OK';
		} else {
			$data['s'] = 0;
			$data['r'] = 'NOT_DELETE';
		}
	
	} else {
		$data['s'] = 0;
		$data['r'] = 'NOT_CART';
	}
} else {
	$data['s'] = 0;
	$data['r'] = 'INVALID_CREDENTIALS';
}

function addProductToCart(&$Cart = null,$product_id = null,$amount = null) 				
	//obtengo la instancia activa para agregar el producto
	$instance_active = $Cart->getInstanceActive();			
	$Cart->init($instance_active);

	$ClassName = str_replace(' ', '', "Umbrella\ ".ucfirst("Package"));
	$Class = new $ClassName;
	$Class->setId($product_id);
	
	$kind = explode('-',$instance_active)[1];			
				
	//agrego los atributos del producto
	$Class->JoinAtributos([
		'ammount' => $Class->price_distributor,
		'points' => ($kind!='super_puntos') ? $Class->points : $Class->super_points_gain,
		'type' => $Class->getTableName(),
		'discount' =>$Class->discount,
		'id' => $Class->getId()
	]);
	$Cart->setItem($Class,$amount);
	$Cart->buy();
}


/* conventional methods */

function getHtml($purchases = false,$payment_method = false){
	$html = false;
	if($purchases)
	{
		$html .= '<div class="col-md-12 box-message ">';
		$html .= '<b style="text-shadow: 4px -2px 8px rgb(121, 121, 121);">Gracias por comprar en Franquicias Veggie</b>';

		if(count($purchases>1)){
			$html .= '<div class="box-message-adversiment">A continuación te mostramos las órdenes de compra que registraste , Te recordamos tomar nota para cualquier duda ó aclaración.</div>';
			$html .='<div class="col-md-12 ">';
				$html .='<table class="table table-responsive-sm table-hover table-detail ">';
						$html .='<thead>';
							$html .='<tr>';
							  $html .='<th class="font-white" scope="col">Nombre </th>';
							  $html .='<th class="font-white" scope="col">Id Distribuidor</th>';
							  $html .='<th class="font-white" scope="col">Id Compra</th>';
							  $html .='<th class="font-white" scope="col">Tipo Compra</th>';
							  $html .='<th class="font-white" scope="col">Total a Pagar</th>';							  
							$html .='</tr>';
						$html .='</thead>';
						$html .='<tbody>';
							foreach ($purchases as $key => $purchase) {

								if($purchase["kind"]=='1')
									$purchaseKind = "Rotativo 300 Puntos";
								else if($purchase["kind"]=='2')
									$purchaseKind = "Excedente";							
								else if($purchase["kind"]=='super_puntos')
									$purchaseKind = "Super Puntos";
								

								$one_payment+=$purchase["total_to_pay"];
								$html .='<tr>';
							      $html .='<th class="font-white" scope="row">'.$purchase["buyerName"].'</th>';
							      $html .='<th class="font-white" >'.$purchase["user_login_id"].'</th>';							      
							      $html .='<th class="font-white" >'.$purchase["buy_per_user_login_id"].'</th>';
							      $html .='<th class="font-white" >'.$purchaseKind.'</th>';							      
							      $html .='<th class="font-white" > $ '.$purchase["total_to_pay"].'</th>';
							    $html .='</tr>';															
							}							
								$html .='<tr>';
							      $html .='<th class="font-white" scope="row" colspan="4" class="text-right">Monto para pago en una sola Exhibición</th>';
							      $html .='<th class="font-white" > $ '.$one_payment.'</th>';
							    $html .='</tr>';
						$html .='<tbody>';
				$html .='</table>';
			$html .='';
			$html .='</div>';
		}else{

			$html .= '<div class="box-message-adversiment">El siguiente número es el de su compra, guardalo para cualquier duda ó aclaración.</div>';
			$html .= '<div class="col-md-12 box-buy-id">'.$BuyPerUser->getId().'</div>';
			if($payment_method == 2) $html .= '<div class="col-md-12 box-sing-buy">¿Necesitas registrar tu compra? <a href="../../apps/backoffice/?profile=m">Da click aquí</a> </div>';
			$html .= '</div>';
		}

	}
	return $html;
}


function getPayPalForm($data = false){

	$form_paypal .= '<div class="paypal-box text-center col-md-12"> Completa tu compra pagando en PayPal <b>Pago Seguro</b>.<br><small> ¡Paga con tu tarjeta de débito o crédito!, da click en el siguiente botón</small>';
	$form_paypal .= '<form class="rgt" method="post" action=" https://www.paypal.com/cgi-bin/webscr" id="formTpv">';
	$form_paypal .= '<input type="hidden" name="cmd" value="_xclick">';
	$form_paypal .= '<input type="hidden" id="business" name="business" value="SGC6QBKK6DQF8" />';
	$form_paypal .= '<input type="hidden" id="item_name" name="item_name" value="Umbrella-'.$data["buy_per_user_login_id"].'"/>';
	$form_paypal .= '<input type="hidden" id="amount" name="amount" value="'.$data['total_ammount'].'"/>';
	$form_paypal .= '<input type="hidden" id="item_number" name="item_number" value="'.$data["buy_per_user_login_id"].'"/>';
	$form_paypal .= '<input type="hidden" name="currency_code" value="MXN"/>';
	$form_paypal .= '<input type="submit" value="Pagar con PayPal" class="button-paypal">';
	$form_paypal .= '</form>';
	$form_paypal .= '</div>';

	return $form_paypal;
}

function getDepositForm($purchases = false){
	$html = false;
	if($purchases)
	{
		$html .= '<div class="col-md-12 box-deposit">';
			$html .= '<div class="col-md-12 box-deposit-title text-center">';
				$html .= 'Ya puedes hacer tu depósito bancario';
			$html .= '</div>';			
			$html .= '<div class="offset-3 col-md-5">';
				$html .= '<div class="row">';
					$html .= '<div class="col-md-6 text-left"><b>Banco</b></div> <div class="col-md-6 text-left">Bancomer</div>';
				$html .= '</div>';
				$html .= '<div class="row">';
					$html .= '<div class="col-md-6 text-left"><b>Cuenta</b></div> <div class="col-md-6 text-left">0181612115</div>';
				$html .= '</div>';
				$html .= '<div class="row">';
					$html .= '<div class="col-md-6 text-left"><b>CLABE</b></div> <div class="col-md-6 text-left">012320001816121154</div>';
				$html .= '</div>';
				$html .= '<div class="row">';
					$html .= '<div class="col-md-6 text-left"><b>RFC</b></div> <div class="col-md-6 text-left">MTK101103UJ1</div>';
				$html .= '</div>';
				$html .= '<div class="row">';
					$html .= '<div class="col-md-6 text-left"><b>A nombre de:</b></div> <div class="col-md-6 text-left">MARKETING TRUE KINGDOM S.A de C.V</div>';
				$html .= '</div>';
				// $html .= '<div class="row">';
				// 	$html .= '<div class="col-md-6 text-right"><b>Cantidad a pagar</b></div> <div class="col-md-6 text-left">$ '.$Cart->total_ammount.'</div>';
				// $html .= '</div>';
			$html .= '</div>';
			$html .= '<div class="col-md-12 box-deposit-info text-center">';
				$html .= 'Después de hacer tu pago será necesario <b>registrarlo</b>, debes ir a tu sección de <b>"mis compras"</b> y seleccionar tu fecha de pago y referencia.';
			$html .= '</div>';
			$html .= '<div class="col-md-12 box-sing-buy text-center">¿Necesitas registrar tu compra? <a href="../../apps/backoffice/?profile=m"">Da click aquí</a> </div>';
		$html .= '</div>';
	}
	return $html;
}

function getHtmlOxxo(){
	$html .= '<div class="oxxo-box text-center col-md-12">';
		$html .= 'Completa tu compra pagando en Oxxo.<br><small> ¡Paga en efectivo en cualquier tienda Oxxo!</small>';
		$html .= '<div class="text-center pdf_view box-waiting-oxxo-ticket col-md-12">';
			$html .= 'Espera un momento estamos generando tu ticket<br><img style="width:32px" src="../../src/img/status-red.gif">';
		$html .= '</div>';
		// $html .= '<div class="text-center pdf_view"></div>';
	$html .= '</div>';

	return $html;
}
 
echo json_encode(HCStudio\Util::compressDataForPhone($data)); 