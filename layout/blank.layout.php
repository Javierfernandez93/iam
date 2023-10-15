<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="author" content="<?php echo HCStudio\Connection::proyect_name;?> all rights reserved 2016">
    %metadata%
    <title>{{title}}</title>
    <link href="<?php echo HCStudio\Connection::getMainPath();?>/src/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo HCStudio\Connection::getMainPath();?>/src/css/sb-admin-2.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=3.0, user-scalable=no" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="description" content="" />
    <meta name="HandheldFriendly" content="True" />
    <meta name="theme-color" content="#07a07A">    

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
    <script src="<?php echo HCStudio\Connection::getMainPath();?>/src/js/jquery-3.1.1.js" type="text/javascript"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">


    <link rel="stylesheet" media="screen" type="text/css" href="<?php echo HCStudio\Connection::getMainPath();?>/src/css/colorpicker.css" />
    <link rel="stylesheet" media="screen" type="text/css" href="<?php echo HCStudio\Connection::getMainPath();?>/src/css/ruler.css" />
    <script type="text/javascript" src="<?php echo HCStudio\Connection::getMainPath();?>/src/js/colorpicker.js"></script>
    <script src="<?php echo HCStudio\Connection::getMainPath();?>/src/js/wow.js" type="text/javascript"></script>
    <script src="<?php echo HCStudio\Connection::getMainPath();?>/src/js/tooltip.js" type="text/javascript"></script>
    <script src="<?php echo HCStudio\Connection::getMainPath();?>/src/js/http.js" type="text/javascript"></script>
    <script src="<?php echo HCStudio\Connection::getMainPath();?>/src/js/translator.js" type="text/javascript"></script>
    <script src="<?php echo HCStudio\Connection::getMainPath();?>/src/js/ruler.js?v=2.6.4" type="text/javascript"></script>
    <script src="<?php echo HCStudio\Connection::getMainPath();?>/src/js/general.js?v=2.6.4" type="text/javascript"></script>

    <script src="<?php echo HCStudio\Connection::getMainPath();?>/src/js/alertCtrl.js?v=2.6.4" type="text/javascript"></script>
    <script src="<?php echo HCStudio\Connection::getMainPath();?>/src/js/loader.js" type="text/javascript"></script>
    <script src="<?php echo HCStudio\Connection::getMainPath();?>/src/js/boxloader.js" type="text/javascript"></script>
    
    <!-- boostrap v4 -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

    <link rel="stylesheet" type="text/css" media="screen" charset="utf-8" href="<?php echo HCStudio\Connection::getMainPath();?>/src/css/general.css" />
    <link rel="stylesheet" type="text/css" media="screen" charset="utf-8" href="<?php echo HCStudio\Connection::getMainPath();?>/src/awesome/css/font-awesome.css" />
    <link rel="stylesheet" type="text/css" media="screen" charset="utf-8" href="<?php echo HCStudio\Connection::getMainPath();?>/src/css/loader.css" />
    
    <link href="<?php echo HCStudio\Connection::getMainPath();?>/src/css/sb-admin-2.css" rel="stylesheet">
    <link href="<?php echo HCStudio\Connection::getMainPath();?>/src/css/theme.min.css" rel="stylesheet">
    <link href="<?php echo HCStudio\Connection::getMainPath();?>/src/css/loader.css" rel="stylesheet">
    <link href="<?php echo HCStudio\Connection::getMainPath();?>/src/css/notifications.css" rel="stylesheet">
    
    <link rel="shortcut icon" type="image/png" href="<?php echo HCStudio\Connection::getMainPath();?>/src/img/logo-sm.png"/>
    {{js_scripts}}
    <script src="https://npmcdn.com/tether@1.2.4/dist/js/tether.min.js"></script>
    <!-- css styles -->
    {{css_scripts}}
  </head>
  <body>
    <?php if($preloader === true) { ?>
        <div class="preloader-container bg-dark w-100 h-100 d-flex align-items-center justify-content-center" id="preloader">
            <div class="preloader-main">
                <div class="preloader preloader-white">
                    <div class="preloader-logo">
                    </div>
                </div>
                <img src="../../src/img/logo-sm.png">
            </div>
        </div>
    <?php } ?>
    {{content}}
  </body>
</html>