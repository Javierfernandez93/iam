<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8" />
        <title>{{title}}</title>
        
        <link rel="icon" type="image/x-icon" href="https://www.iam.com.mx/src/img/meta.png">
        <link rel="apple-touch-icon" sizes="76x76" href="https://www.iam.com.mx/src/img/meta.png">
        <link rel="icon" type="image/png" href="https://www.iam.com.mx/src/img/favicon.png">
        <link rel="shortcut icon" href="https://www.iam.com.mx/src/img/favicon.png" />

        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1" />
        
        <meta content="Tu Aliado para el Cuidado Personal" name="description" />
        <meta content="Tu Aliado para el Cuidado Personal" name="author" />

        <meta property="og:site_name" content="I am beauty Oil">
        <meta property="og:title" content="I am beauty Oil" />
        <meta property="og:description" content="Tu Aliado para el Cuidado Personal" />
        <meta property="og:image" itemprop="image" content="https://www.iam.com.mx/src/img/logo.png">
        <meta property="og:type" content="website" />
        <meta property="og:updated_time" content="1693339145" />
        <meta name="theme-color" content="#2D2250">  
        
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css">
        
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
        <!-- Nucleo Icons -->
        <link href="../../src/css/nucleo-icons.css" rel="stylesheet" />
        <link href="../../src/css/nucleo-svg.css" rel="stylesheet" />
        <link href="../../src/css/animate.css" rel="stylesheet" />
        
        <link href="<?php echo HCStudio\Connection::getMainPath();?>/src/css/general.css" rel="stylesheet" type="text/css">
        <link id="pagestyle" href="../../src/css/soft-ui-dashboard.min.css?v=2.0.8" rel="stylesheet" />
        <link id="pagestyle" href="../../src/css/soft-ui-dashboard-theme.min.css?v=2.0.8" rel="stylesheet" />
        {{css_scripts}}
    </head>
    <body>
        {{content}}
        
        <script src="<?php echo HCStudio\Connection::getMainPath();?>/src/js/jquery-3.1.1.js" type="text/javascript"></script>
        <script src="<?php echo HCStudio\Connection::getMainPath();?>/src/js/general.js" type="text/javascript"></script>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
        <script src="https://unpkg.com/vue@3"></script>

        {{js_scripts}}

        <footer class="fixed-bottom w-100 text-end p-3 fw-semibold">
            © <script> document.write(new Date().getFullYear()) </script>, made with <i class="fa fa-heart"></i> by <a href="https://www.iam.com.mx/" class="font-weight-bold text-primary" target="_blank">DummieTrading</a> for a better web.
        </footer>
    </body>
</html>