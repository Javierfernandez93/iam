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
		$returnData['html'] = getHtml($Cart);
		$returnData['success'] = 1;
		$returnData['reason'] = 'ITEMS_OK';
	} else {
		$returnData['success'] = 0;
		$returnData['reason'] = 'NOT_CART';
	}
} else {
	$returnData['success'] = 0;
	$returnData['reason'] = 'INVALID_CREDENTIALS';
}

function getHtml($Cart = false)
{
	$html = false;

	if($items = $Cart->getItems())
	{
		foreach ($items as $key => $item)
		{
			$html .= '<div class="box-item-detail text-left">';
			$html .= '<div class="box-item-product-count">'.$item['products'].'</div>';
			$html .= $item['title'];
			$html .= '<div class="box-item-detail-points">';
			$html .= $item['products'] * $item['points'] . ' Puntos';
			$html .= '</div>';
			$html .= '<div class="box-item-detail-delete" data-id="'.$item['id'].'" data-type="'.$item['type'].'"></div>';
			$html .= '</div>';
		}
	}

	return $html;
}
echo json_encode($returnData); ?>