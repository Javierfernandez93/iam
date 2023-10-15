<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=3.0, user-scalable=no" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="author" content="<?php echo HCStudio\Connection::proyect_name;?> all rights reserved <?php echo date('Y');?>">

    {{metadata}}
    
    <title>{{title}}</title>
    
    <link rel="icon" type="image/png" href="<?php echo HCStudio\Connection::getMainPath();?>/src/img/favicon.png">

    <?php if($metas_per_sheet = $MetaPerSheet->getAll($sheet_per_proyect_id)) { ?>
      <?php foreach($metas_per_sheet as $meta_per_sheet): ?>
        <meta name="<?php echo $meta_per_sheet['meta'];?>" content="<?php echo $meta_per_sheet['content'];?>" />
      <?php endforeach ?>
    <?php } ?>
    
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="<?php echo HCStudio\Connection::getMainPath();?>/src/dist/assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>

    <link href="<?php echo HCStudio\Connection::getMainPath();?>/src/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo HCStudio\Connection::getMainPath();?>/src/css/theme.min.css" rel="stylesheet">
    <link href="<?php echo HCStudio\Connection::getMainPath();?>/src/css/loader.css" rel="stylesheet">
    <link href="<?php echo HCStudio\Connection::getMainPath();?>/src/css/vcard.css" rel="stylesheet">
  </head>
  
  <body>
    <div class="vh-100 w-100" id="app">
      {{content}}
    </div>
  
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <link rel="stylesheet" media="screen" type="text/css" href="<?php echo HCStudio\Connection::getMainPath();?>/src/css/colorpicker.css" />  
    
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script> -->
    <script src="<?php echo HCStudio\Connection::getMainPath();?>/src/js/jquery-3.1.1.js" type="text/javascript"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    
    <script type="text/javascript" src="<?php echo HCStudio\Connection::getMainPath();?>/src/js/colorpicker.js"></script>

    <script src="<?php echo HCStudio\Connection::getMainPath();?>/src/js/constants.js?v=2.6.4" type="text/javascript"></script>
    <script src="<?php echo HCStudio\Connection::getMainPath();?>/src/js/wow.js" type="text/javascript"></script>
    <script src="<?php echo HCStudio\Connection::getMainPath();?>/src/js/tooltip.js" type="text/javascript"></script>
    <script src="<?php echo HCStudio\Connection::getMainPath();?>/src/js/http.js" type="text/javascript"></script>
    <script src="<?php echo HCStudio\Connection::getMainPath();?>/src/js/general.js?v=2.6.4" type="text/javascript"></script>

    <script src="<?php echo HCStudio\Connection::getMainPath();?>/src/js/alertCtrl.js?v=2.6.4" type="text/javascript"></script>
    <script src="<?php echo HCStudio\Connection::getMainPath();?>/src/js/loader.js" type="text/javascript"></script>
    <script src="<?php echo HCStudio\Connection::getMainPath();?>/src/js/boxloader.js" type="text/javascript"></script>
    
    <!-- bootstrap v5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <script src="<?php echo HCStudio\Connection::getMainPath();?>/src/js/vue.js"></script>
    {{js_scripts}}
    <!-- css styles -->
    {{css_scripts}}
  </body>
</html>