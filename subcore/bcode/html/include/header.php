<?php
if (!defined('IN_CB')) { die('You are not allowed to access to this page.'); }

if (version_compare(phpversion(), '5.0.0', '>=') !== true) {
    exit('Sorry, but you have to run this script with PHP5... You currently have the version <b>' . phpversion() . '</b>.');
}

if (!function_exists('imagecreate')) {
    exit('Sorry, make sure you have the GD extension installed before running this script.');
}

include_once('function.php');

// FileName & Extension
$system_temp_array = explode('/', $_SERVER['PHP_SELF']);
$filename = $system_temp_array[count($system_temp_array) - 1];
$system_temp_array2 = explode('.', $filename);
$availableBarcodes = listBarcodes();
$barcodeName = findValueFromKey($availableBarcodes, $filename);
$code = $system_temp_array2[0];

// Check if the code is valid
if (file_exists('config' . DIRECTORY_SEPARATOR . $code . '.php')) {
    include_once('config' . DIRECTORY_SEPARATOR . $code . '.php');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $barcodeName; ?> - Barcode Generator</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link type="text/css" rel="stylesheet" href="style.css" />
        <link rel="shortcut icon" href="favicon.ico" />
        <script src="jquery-1.7.2.min.js"></script>
        <script src="barcode.js"></script>
    </head>
    <body class="<?php echo $code; ?>">

<?php

$default_value = array();
$default_value['a1'] = '';
$default_value['a2'] = '';
$default_value['a3'] = '';

$filetype = isset($_POST['filetype']) ? $_POST['filetype'] : false;
$filetype = (!$filetype && isset($_GET['filetype'])) ? $_GET['filetype'] : $filetype;
$filetype = (!$filetype) ? "PNG" : $filetype;

$dpi = isset($_POST['dpi']) ? $_POST['dpi'] : false;
$dpi = (!$dpi && isset($_GET['dpi'])) ? $_GET['dpi'] : $dpi;
$dpi = (!$dpi) ? "72" : $dpi;

$scale = isset($_POST['scale']) ? $_POST['scale'] : false;
$scale = (!$scale && isset($_GET['scale'])) ? $_GET['scale'] : $scale;
$scale = (!$scale) ? "1" : $scale;

$rotation = isset($_POST['rotation']) ? $_POST['rotation'] : false;
$rotation = (!$rotation && isset($_GET['rotation'])) ? $_GET['rotation'] : $rotation;
$rotation = (!$rotation) ? 1 : $rotation;

$font_family = isset($_POST['font_family']) ? $_POST['font_family'] : false;
$font_family = (!$font_family && isset($_GET['font_family'])) ? $_GET['font_family'] : $font_family;
$font_family = (!$font_family) ? 'Arial.ttf' : $font_family;

$font_size = isset($_POST['font_size']) ? $_POST['font_size'] : false;
$font_size = (!$font_size && isset($_GET['font_size'])) ? $_GET['font_size'] : $font_size;
$font_size = (!$font_size) ? 8 : $font_size;

$text = isset($_POST['text']) ? $_POST['text'] : false;
$text = (!$text && isset($_GET['text'])) ? $_GET['text'] : $text;
$text = (!$text) ? '' : $text;

registerImageKey('filetype', $filetype);
registerImageKey('dpi', $dpi);
registerImageKey('scale', $scale);
registerImageKey('rotation', $rotation);
registerImageKey('font_family', $font_family);
registerImageKey('font_size', $font_size);
registerImageKey('text', stripslashes($text));

// Text in form is different than text sent to the image
$text = convertText($text);
?>

<div class="header">
    <header>
        <img class="logo" src="logo.png" alt="Barcode Generator" />
        <nav>
            <label for="type">Symbology</label>
            <?php echo getSelectHtml('type', $filename, $availableBarcodes); ?>
            <a class="info explanation" href="#"><img src="info.gif" alt="Explanation" /></a>
        </nav>
    </header>
</div>

<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
    <h1>Barcode Generator</h1>
    <h2><?php echo $barcodeName; ?></h2>
    <div class="configurations">
        <section class="configurations">
            <h3>Configurations</h3>
            <table>
                <colgroup>
                    <col class="col1" />
                    <col class="col2" />
                </colgroup>
                <tbody>
                    <tr>
                        <td><label for="filetype">File type</label></td>
                        <td><?php echo getSelectHtml('filetype', $filetype, array('PNG' => 'PNG - Portable Network Graphics', 'JPEG' => 'JPEG - Joint Photographic Experts Group', 'GIF' => 'GIF - Graphics Interchange Format')); ?></td>
                    </tr>
                    <tr>
                        <td><label for="dpi">DPI</label></td>
                        <td><?php echo getInputTextHtml('dpi', $dpi, array('type' => 'number', 'min' => 72, 'max' => 300)); ?> <span id="dpiUnavailable">DPI is available only for PNG and JPEG.</span></td>
                    </tr>
<?php
if (isset($baseClassFile) && file_exists('include' . DIRECTORY_SEPARATOR . $baseClassFile)) {
    include_once('include' . DIRECTORY_SEPARATOR . $baseClassFile);
}
?>
                    <tr>
                        <td><label for="scale">Scale</label></td>
                        <td><?php echo getInputTextHtml('scale', $scale, array('type' => 'number', 'min' => 1, 'max' => 4)); ?></td>
                    </tr>
                    <tr>
                        <td><label for="rotation">Rotation</label></td>
                        <td><?php echo getSelectHtml('rotation', $rotation, array(0 => 'No rotation', 90 => '90&deg; clockwise', 180 => '180&deg; clockwise', 270 => '270&deg; clockwise')); ?></td>
                    </tr>
                    <tr>
                        <td><label for="font_family">Font</label></td>
                        <td><?php echo getSelectHtml('font_family', $font_family, listfonts('../font')); ?> <?php echo getInputTextHtml('font_size', $font_size, array('type' => 'number', 'min' => 1, 'max' => 30)); ?></td>
                    </tr>
                    <tr>
                        <td><label for="text">Data</label></td>
                        <td>
                            <div class="generate" style="float: left"><?php echo getInputTextHtml('text', $text, array('type' => 'text')); ?> <input type="submit" value="Generate" /></div>
                            <div class="possiblechars" style="float: right; position: relative;"><a href="#" class="info characters"><img src="info.gif" alt="Help" /></a></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>
    </div>