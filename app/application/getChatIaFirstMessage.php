<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php"; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
    $items = [
        [
            'message' => '¿Cómo hago mi compra?'
        ],
        [
            'message' => '¿Dónde veo mis compras?'
        ],
        [
            'message' => '¿Cuáles son los números para comunicarme?'
        ],
        [
            'message' => '¿Por qué aún no llega mi paquete?'
        ]
    ];

    shuffle($items);
    array_values($items);
    array_slice($items,0,5);

    $data['message'] = [
        'message' => 'Bienvenido a IAM, por favor escribe una pregunta o selecciona un tema de ayuda rápida',
        'items' => $items
    ];
    $data['r'] = 'DATA_OK';
    $data['s'] = 1;
} else {
	$data['r'] = 'INVALID_CREDENTIALS';
	$data['s'] = 0;
}

echo json_encode($data); 