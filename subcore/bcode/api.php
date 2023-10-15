<?php

require_once('class/BCGFontFile.php');
require_once('class/BCGColor.php');
require_once('class/BCGDrawing.php');
require_once('class/Verification.php');
require_once('class/BCGcode128.barcode.php');

$font_size = isset($_POST['font_size']) ? $_POST['font_size'] : false;
$font_size = (!$font_size && isset($_GET['font_size'])) ? $_GET['font_size'] : $font_size;
$font_size = (!$font_size) ? 18 : $font_size;

$text = isset($_POST['text']) ? $_POST['text'] : false;
$text = (!$text && isset($_GET['text'])) ? $_GET['text'] : $text;
$text = (!$text) ? "Testing" : $text;

$bar_size = isset($_POST['bar_size']) ? $_POST['bar_size'] : false;
$bar_size = (!$bar_size && isset($_GET['bar_size'])) ? $_GET['bar_size'] : $bar_size;
$bar_size = (!$bar_size) ? 2 : $bar_size;

$bar_height = isset($_POST['bar_height']) ? $_POST['bar_height'] : false;
$bar_height = (!$bar_height && isset($_GET['bar_height'])) ? $_GET['bar_height'] : $bar_height;
$bar_height = (!$bar_height) ? 20 : $bar_height;

$verification_type = isset($_POST['verification_type']) ? $_POST['verification_type'] : false;
$verification_type = (!$verification_type && isset($_GET['verification_type'])) ? $_GET['verification_type'] : $verification_type;
$verification_type = (!$verification_type) ? "B10" : $verification_type;

$font = new BCGFontFile('./font/Arial.ttf', $font_size);
$text = Verification::generateVerificatorDigit($text,$verification_type);

$color_black = new BCGColor(0, 0, 0);
$color_white = new BCGColor(255, 255, 255);

$drawException = null;

try {
    $code = new BCGcode128();
    $code->setScale($bar_size); // Resolution
    $code->setThickness($bar_height); // Thickness
    $code->setForegroundColor($color_black); // Color of bars
    $code->setBackgroundColor($color_white); // Color of spaces
    $code->setFont($font); // Font (or 0)
    $code->parse($text); // Text
} catch(Exception $exception) {
    $drawException = $exception;
}

$drawing = new BCGDrawing('', $color_white);

if($drawException)
    $drawing->drawException($drawException);
else
    $drawing->setBarcode($code);
    $drawing->draw();


// Header that says it is an image (remove it if you save the barcode to a file)
header('Content-Type: image/png');
header('Content-Disposition: inline; filename="barcode.png"');

// Draw (or save) the image into PNG format.
$drawing->finish(BCGDrawing::IMG_FORMAT_JPEG);

?>