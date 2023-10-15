<?php define("TO_ROOT", "../../../..");

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once TO_ROOT . "/system/core.php";

$returnData = Array();
$UserLogin = new Umbrella\UserLogin();

if($UserLogin->logged === true)
{
	$Cart = new Jcart\Cart();

	if($Cart->loaded)
	{	
		$CatalogQualificationType = new Umbrella\CatalogQualificationType();
		
		if($CatalogQualificationType->loadWhere('user_login_id = ? and status = ?',[$UserLogin->company_id,'1'])){

			$returnData['html'] = getHtml($Cart,$UserLogin,(new Umbrella\BuyPerUser()),$CatalogQualificationType);
			$returnData['success'] = 1;
			$returnData['reason'] = 'DATA_OK';
		}else{
			$returnData['success'] = 0;
			$returnData['reason'] = 'NOT_ACTIVATION_KIND';
		}
	} else {
		$returnData['success'] = 0;
		$returnData['reason'] = 'NOT_CART';
	}
} else {
	$returnData['success'] = 0;
	$returnData['reason'] = 'INVALID_CREDENTIALS';
}

function getHtml($Cart = false,$UserLogin = false,$BuyPerUser = false,$CatalogQualificationType=false)
{
	$html = false;

	if($Cart)
	{
		$Layout = JFStudio\Layout::getInstance();
		$Layout->init("","cart-content","clean",TO_ROOT.'/apps/applications/',TO_ROOT."/");
		return $Layout->getHtml();

		$html .= '<div class="col-md-12 box-title box-total-ammount">';
		$html .= (($Cart->total_ammount) ? number_format($Cart->total_ammount,2) : '0');
		$html .= '</div>';

		$html .= '<div class="box-total-points">';
	    $html .= (($Cart->total_points) ? $Cart->total_points : 0);
		$html .= '</div>';

		if($Cart->getTotalKind('product') > 0)
		{
			$html .= '<div class="col-md-12 box-title box-total-total-products">';
			$html .= $Cart->getTotalKind('product');
			$html .= '</div>';
		}
		if($Cart->getTotalKind('package') > 0)
		{
			$html .= '<div class="col-md-12 box-title box-total-total-packages">';
			$html .= $Cart->getTotalKind('package');
		}
		$html .= '</div>';
		if($Cart->total_ammount)
		{
			$html .= '<div class="col-md-12 box-payment-method">';
			$html .= '<div data-payment_method="1" class="'.(($Cart->payment == 1) ? 'method-selected' : '').' col-md-4 box-payment-button box-payment-paypal-method"></div>';
			$html .= '<div data-payment_method="2" class="'.(($Cart->payment == 2) ? 'method-selected' : '').' col-md-4 box-payment-button box-payment-deposit-method"></div>';
			$html .= '<div data-payment_method="3" class="'.(($Cart->payment == 3) ? 'method-selected' : '').' col-md-4 box-payment-button box-payment-oxxo-method"></div>';
			// $html .= '<div data-payment_method="5" class="'.(($Cart->payment == 5) ? 'method-selected' : '').' col-md-3 box-payment-button box-payment-banwire-method"></div>';
			$html .= '</div>';
		}

		if($Cart->total_ammount)
		{
			$html .= '<div class="col-md-12 box-shipping">';
			
			//delete if the shipping not is add
			$html .= '<div class="col-md-2"></div>';
			
			$html .= '<div data-shipping="1" class="col-md-4 col-md-offset-2 '.(($Cart->shipping) ? 'box-shipping-button-selected' : '').' box-shipping-button">';
			$html .= 'Sí';
			$html .= '</div>';
			// $html .= '<div data-shipping="0" class="'.((!$Cart->shipping) ? 'box-shipping-button-selected' : '').' box-shipping-button">';
			// $html .= 'No';
			// $html .= '</div>';
			$html .= '</div>';
		}

		if($Cart->total_ammount && $Cart->shipping)
		{
			$html .= '<div class="col-md-12 box-shipping-shared">';
			$html .= '<div data-shipping-shared="1" class=" '.(($Cart->shipping_shared) ? 'box-shipping-shared-button-selected' : '').' box-shipping-shared-button">';
			$html .= 'Sí';
			$html .= '</div>';
			$html .= '<div data-shipping-shared="0" class="'.((!$Cart->shipping_shared) ? 'box-shipping-shared-button-selected' : '').' box-shipping-shared-button">';
			$html .= 'No';
			$html .= '</div>';
			$html .= '</div>';
		}

		if($Cart->total_ammount && $Cart->shipping && $Cart->shipping_shared)
		{
			$html .= '<div class="col-md-12 box-shipping-shared-order">';
			$html .= '<div class="form-group">';
			$html .= '<label for="id_order_shared">Id de compra en la cual se enviara:</label>';
			$html .= '<input type="text" class="form-control" id="id_order_shared" placeholder="Orden de compra" value="'.$Cart->shipping_shared_id.'">';
			$html .= '<button type="button" class="btn btn-success send_id_order">Aceptar</button>';
			$html .= '</div>';
			$html .= '</div>';
		}
		if($Cart->total_ammount && $Cart->shipping && $Cart->shipping_shared && $Cart->address_shipping_shared)
		{
			$html .= '<div class="col-md-12 box-address-shipping-shared-order">';
			$html .= '<div>';
			$html .= '<p>Dirección a enviar:</p>';
			$html .= '<p>'.$Cart->address_shipping_shared.'</p>';
			$html .= '</div>';
		}

		if($Cart->total_ammount)
		{
			$html .= '<a class="button-pay-now-a" href="../../apps/ewallet/checkout.php">';
			$html .= '<div class="col-md-12 button-pay-now">';
			$html .= 'Pagar Ahora';
			$html .= '</div>';
			$html .= '</a>';

			$html .= '<div class="col-md-12 box-see-detail">';
			$html .= 'Ver Detalle';
			$html .= '</div>';
			$html .= '<div class="col-md-12 box-items-details">';
			$html .= '</div>';
		}
	
		$html .= '<div class="box-lines '.(($Cart->total_ammount == 0) ? 'box-lines-empty' : '').'">';
		$html .= '<div class="box-lines-title">Líneas</div>';
		$html .= '<div class="box-line" data-kind="0" data-line="Biometrica">Biometrica</div>';
		$html .= '<div class="box-line" data-kind="0" data-line="GanoFood">GanoFood</div>';
		$html .= '<div class="box-line" data-kind="0" data-line="Herramientas">Herramientas</div>';
		$html .= '<div class="box-line" data-kind="0" data-line="Metabolica">Metabolica</div>';
		$html .= '<div class="box-line" data-kind="0" data-line="Organica">Organica</div>';
		$html .= '<div class="box-line" data-kind="0" data-line="Veggie">Veggie</div>';
		if($CatalogQualificationType->MercadoSocial=='0' && $CatalogQualificationType->RotativoMercadeoSocial=='0')
			$html .= '<div class="box-line" data-kind="0" data-line="Mer">Mer</div>';
		
		$html .= '<div class="box-line" data-kind="0" data-line="4Planet">4Planet</div>';
		// if(!$UserLogin->_data['user_account']['qualify'] && !$BuyPerUser->hasBuyOnthisMoth($UserLogin->company_id))
		// if($UserLogin->_data['user_account']['month'] == 0 && $UserLogin->_data['user_account']['franchise'] == '1')	
		// $html .= '<div class="box-line" data-kind="1">Paquetes</div>';
		$html .= '</div>';
		
		// if($CatalogQualificationType->DineroExpress=='0'){
			
		// }
	}
	return $html;
}

echo json_encode($returnData); ?>