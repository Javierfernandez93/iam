<!DOCTYPE html>
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

    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link rel="stylesheet" href="../../src/css/nucleo-icons.css" />
    <link rel="stylesheet" href="../../src/css/nucleo-svg.css" />
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;900&display=swap" rel="stylesheet">

    <link href="../../src/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../src/css/z-loader.css" />
    <link rel="stylesheet" href="../../src/css/general.css?v=2.0.8" />
    <link rel="stylesheet" href="../../src/css/nucleo-svg.css" />
    <!-- CSS Files -->

    <link id="pagestyle" href="../../src/css/soft-ui-dashboard.css?v=2.4.5" rel="stylesheet" />
    <link id="pagestyle" href="../../src/css/soft-ui-dashboard-theme.min.css?v=2.4.5" rel="stylesheet" />

    <?php // if(in_array(array_shift((explode('.', "exma.iam.com.mx"))),["exma"])) { ?> 
    <?php if(in_array(array_shift((explode('.', $_SERVER['HTTP_HOST']))),["exma"])) { ?> 
        <link rel="stylesheet" href="../../src/css/exma.css" />
    <?php } else { ?> 
        <link rel="stylesheet" href="../../src/css/dummie.css" />
    <?php } ?> 
</head>

<body class="g-sidenav-show bd-masthead">
   
    <div class="">
        {{content}}
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    
    <!--   Core JS Files   -->
    <script src="../../src/js/plugins/perfect-scrollbar.min.js" type="text/javascript"></script>
    <script src="../../src/js/plugins/smooth-scrollbar.min.js" type="text/javascript"></script>
    <script src="../../src/js/plugins/chartjs.min.js" type="text/javascript"></script>
    <script src="../../src/js/42d5adcbca.js" type="text/javascript"></script>
    
    <script src="../../src/js/constants.js?v=2.6.5" type="text/javascript"></script>
    <script src="../../src/js/alertCtrl.js?v=2.6.5" type="text/javascript"></script>
    <script src="../../src/js/jquery-3.5.1.min.js" type="text/javascript"></script>
    <script src="../../src/js/general.js?v=2.6.5" type="text/javascript"></script>
    <!-- Github buttons -->
    
    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
    <script src="https://cdn.jsdelivr.net/gh/ethereum/web3.js@1.0.0-beta.36/dist/web3.min.js" integrity="sha256-nWBTbvxhJgjslRyuAKJHK+XcZPlCnmIAAMixz6EefVk=" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

    <!-- Github buttons -->
    <script src="../../src/js/buttons.js" type="text/javascript"></script>
    <script src="../../src/js/soft-ui-dashboard.min.js?v=2.6.5"></script>
    
    <script src="../../src/js/cookie.js?v=2.6.5" type="text/javascript"></script>
    <script src="../../src/js/vue.js?v=2.6.5" type="text/javascript"></script>
    
    {{js_scripts}}
    {{css_scripts}}
</body>
</html>