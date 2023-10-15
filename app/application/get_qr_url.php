<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php"; 

$Qr = new JFStudio\Qr; 

if($_GET['kind'] == 'text') {
    $Qr->text($_GET['url']);
} else {
    $Qr->url($_GET['url']);
}

$Qr->qrCode();