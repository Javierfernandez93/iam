<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php"; 

$Qr = new JFStudio\Qr; 

$text = rawurldecode(urlencode($_GET['text']));

$Qr->text($text);

$Qr->qrCode();