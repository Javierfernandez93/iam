<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="author" content="<?php echo HCStudio\Connection::proyect_name;?> all rights reserved <?php echo date('Y');?>">
    %metadata%
    <title>{{title}}</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=3.0, user-scalable=no" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="description" content="" />
    <meta name="HandheldFriendly" content="True" />

    <script src="<?php echo HCStudio\Connection::getMainPath();?>/src/js/jquery-3.1.1.js" type="text/javascript"></script>
    <script src="<?php echo HCStudio\Connection::getMainPath();?>/src/js/wow.js" type="text/javascript"></script>
    <?php
      if($UserLogin->logged === true){?>
        <script src="<?php echo HCStudio\Connection::getMainPath();?>/src/js/tooltip.js" type="text/javascript"></script>
    <?php } ?>
    <!-- boostrap v4 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <link rel="stylesheet" type="text/css" media="screen" charset="utf-8" href="<?php echo HCStudio\Connection::getMainPath();?>/src/awesome/css/font-awesome.css" />
    <link rel="stylesheet" type="text/css" media="screen" charset="utf-8" href="<?php echo HCStudio\Connection::getMainPath();?>/src/css/loader.css" />
    <link rel="shortcut icon" type="image/png" href="<?php echo HCStudio\Connection::getMainPath();?>/src/img/favicon.png"/>
    {{js_scripts}}
    <script src="https://npmcdn.com/tether@1.2.4/dist/js/tether.min.js"></script>
    <script src="<?php echo HCStudio\Connection::getMainPath();?>/src/js/alertCtrl.js" type="text/javascript"></script>
    <script src="<?php echo HCStudio\Connection::getMainPath();?>/src/js/loader.js" type="text/javascript"></script>
    <!-- css styles -->
    {{css_scripts}}
  </head>
  <body>
    
    {{content}}
  </body>
</html>