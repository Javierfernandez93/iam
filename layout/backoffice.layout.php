<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />

    <title>{{title}}</title>

    <link rel="icon" type="image/x-icon" href="https://www.iam.com.mx/src/img/meta.png">
    <link rel="apple-touch-icon" sizes="76x76" href="https://www.iam.com.mx/src/img/meta.png">
    <link rel="icon" type="image/png" href="https://www.iam.com.mx/src/img/favicon.png">
    <link rel="shortcut icon" href="https://www.iam.com.mx/src/img/favicon.png" />

    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=5" />

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

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;900&display=swap" rel="stylesheet">

    <link href="../../src/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../../src/css/general.min.css?v=2.0.8" />

    <!-- CSS Files -->
    <link id="pagestyle" href="../../src/css/soft-ui-dashboard.min.css?v=2.4.5" rel="stylesheet" />
    <link id="pagestyle" href="../../src/css/soft-ui-dashboard-theme.min.css?v=2.4.5" rel="stylesheet" />

    <link rel="stylesheet" href="../../src/css/general.css?v=2.0.8" />
</head>

<body class="g-sidenav-show">
    <main>
        <nav class="navbar navbar-expand-lg py-3 position-absolute w-100 start-0 top-0 w-100 shadow-none bg-body-tertiary" style="z-index: 1000;" data-bs-theme="dark">
            <div class="container">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item mx-2">
                            <a class="nav-link" aria-current="page" href="../../apps/home">Inicio</a>
                        </li>
                        <li class="nav-item mx-2">
                            <a class="nav-link <?php if($route == JFStudio\Router::Backoffice){?>active text-white<?php } ?>" href="../../apps/backoffice">Mi smart office</a>
                        </li>
                        <li class="nav-item mx-2">
                            <a class="nav-link <?php if($route == JFStudio\Router::StorePackage){?>active text-white<?php } ?>" href="../../apps/store/package">Tienda online</a>
                        </li>
                        <li class="nav-item mx-2">
                            <a class="nav-link <?php if($route == JFStudio\Router::Invoices){?>active text-white<?php } ?>" href="../../apps/store/invoices">Compras</a>
                        </li>
                        <li class="nav-item mx-2">
                            <a class="nav-link <?php if($route == JFStudio\Router::Help){?>active text-white<?php } ?>" href="../../apps/ticket">Ayuda</a>
                        </li>
                        <li class="nav-item mx-2">
                            <a class="nav-link" href="../../apps/backoffice/?logout=true">Salir</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="main-container">
            <div class="container py-5">
                {{content}}
            </div>
        </div>

        <footer class="footer fixesd-bottom p-3 row justify-content-center pt-5">
            <div class="col-12 col-xl-11">
                <div class="row align-items-center justify-content-lg-between">
                    <div class="col-lg-6 mb-lg-0 mb-4">
                        <div class="copyright text-center text-sm text-muted text-lg-start">
                            © 2023,
                            made with <i class="fa fa-heart"></i> by
                            <a href="https://www.Iam.com/" class="font-weight-bold" target="_blank">Iam</a>
                            for a better web.
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <ul class="nav nav-footer justify-content-center justify-content-lg-end">
                            <li class="nav-item">
                                <a href="" class="nav-link text-muted" target="_blank">Iam</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!--   Core JS Files   -->
    <script src="../../src/js/plugins/perfect-scrollbar.min.js" type="text/javascript"></script>
    <script src="../../src/js/plugins/smooth-scrollbar.min.js" type="text/javascript"></script>
    <script src="../../src/js/plugins/chartjs.min.js" type="text/javascript"></script>
    <script src="../../src/js/42d5adcbca.js" type="text/javascript"></script>

    <script src="../../src/js/constants.js?v=2.6.4" type="text/javascript"></script>
    <script src="../../src/js/alertCtrl.min.js?v=2.6.4" type="text/javascript"></script>
    <script src="../../src/js/jquery-3.5.1.min.js" type="text/javascript"></script>
    <script src="../../src/js/general.js?v=2.6.4" type="text/javascript"></script>
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

    <!-- <script src="https://cdn.jsdelivr.net/gh/ethereum/web3.js@1.0.0-beta.36/dist/web3.min.js" integrity="sha256-nWBTbvxhJgjslRyuAKJHK+XcZPlCnmIAAMixz6EefVk=" crossorigin="anonymous"></script> -->
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

    <!-- Github buttons -->
    <script src="../../src/js/buttons.min.js" type="text/javascript"></script>
    <script src="../../src/js/soft-ui-dashboard.min.js?v=2.6.4"></script>

    <script src="../../src/js/cookie.min.js?v=2.6.4" type="text/javascript"></script>
    <script src="../../src/js/vue.js?v=2.6.4" type="text/javascript"></script>

    {{js_scripts}}
    {{css_scripts}}
</body>

</html>