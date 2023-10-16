<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../../src/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../../src/img/favicon.png">
    <title>
        {{title}}
    </title>
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link rel="stylesheet" href="../../src/css/nucleo-icons.css" />
    <link rel="stylesheet" href="../../src/css/nucleo-svg.css" />
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="../../src/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../src/css/general.css?t=1" />
    <link rel="stylesheet" href="../../src/css/nucleo-svg.css" />
    <!-- CSS Files -->

    <link id="pagestyle" href="../../src/css/soft-ui-dashboard.css?v=2.0.8" rel="stylesheet" />
    <link id="pagestyle" href="../../src/css/admin.min.css?v=2.0.8" rel="stylesheet" />
    {{css_scripts}}
</head>

<body class="g-sidenav-show  bg-gray-100">
    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3" id="sidenav-main" data-color="danger">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand text-primary h3 text-gradient m-0" href=" https://iam.com.mx" target="_blank">
                <img src="../../src/img/logo-dark.svg" alt="logo" class="w-100 img-fluid"/>
            </a>
        </div>
        <hr class="horizontal dark mt-0">
        <div class="collapse navbar-collapse w-auto h-100" id="sidenav-collapse-main">
            <ul class="navbar-nav nav-admin">
                <?php if ($UserSupport->hasPermission('list_dash')) { ?>
                    <li class="nav-item">
                        <a class="nav-link <?php if (in_array($route, [JFStudio\Router::AdminDash])) { ?>active<?php } ?>" href="../../apps/admin">
                            <span class="badge me-2 d-flex justify-content-center align-items-center icon"><i class="bi bi-house-door"></i></span>
                            <span class="nav-link-text text-dark ms-1"><?php echo JFStudio\Router::getName(JFStudio\Router::AdminDash); ?></span>
                        </a>
                    </li>
                <?php } ?>

                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Administración</h6>
                </li>

                <?php if ($UserSupport->hasPermission('list_users')) { ?>
                    <li class="nav-item">
                        <a class="nav-link <?php if (in_array($route, [JFStudio\Router::AdminUsers])) { ?>active<?php } ?>" href="../../apps/admin-users">
                            <span class="badge me-2 d-flex justify-content-center align-items-center icon"><i class="bi bi-person-circle"></i></span>
                            <span class="nav-link-text text-dark ms-1"><?php echo JFStudio\Router::getName(JFStudio\Router::AdminUsers); ?></span>
                        </a>
                    </li>
                <?php } ?>

                <?php if ($UserSupport->hasPermission('list_users')) { ?>
                    <li class="nav-item">
                        <a class="nav-link <?php if (in_array($route, [JFStudio\Router::AdminEmails])) { ?>active<?php } ?>" href="../../apps/admin-email/">
                            <span class="badge me-2 d-flex justify-content-center align-items-center icon"><i class="bi bi-view-stacked"></i></span>
                            <span class="nav-link-text text-dark ms-1"><?php echo JFStudio\Router::getName(JFStudio\Router::AdminEmails); ?></span>
                        </a>
                    </li>
                <?php } ?>

                <?php if ($UserSupport->hasPermission('list_users')) { ?>
                    <li class="nav-item">
                        <a class="nav-link <?php if (in_array($route, [JFStudio\Router::AdminPackages])) { ?>active<?php } ?>" href="../../apps/admin-products/packages">
                            <span class="badge me-2 d-flex justify-content-center align-items-center icon"><i class="bi bi-view-stacked"></i></span>
                            <span class="nav-link-text text-dark ms-1"><?php echo JFStudio\Router::getName(JFStudio\Router::AdminPackages); ?></span>
                        </a>
                    </li>
                <?php } ?>

                <?php if ($UserSupport->hasPermission('list_users')) { ?>
                    <li class="nav-item">
                        <a class="nav-link <?php if (in_array($route, [JFStudio\Router::AdminProducts])) { ?>active<?php } ?>" href="../../apps/admin-products/products">
                            <span class="badge me-2 d-flex justify-content-center align-items-center icon"><i class="bi bi-ui-checks-grid"></i></span>
                            <span class="nav-link-text text-dark ms-1"><?php echo JFStudio\Router::getName(JFStudio\Router::AdminProducts); ?></span>
                        </a>
                    </li>
                <?php } ?>

                <li class="nav-item">
                    <a class="nav-link <?php if ($route == JFStudio\Router::AdminIntent) { ?>active<?php } ?>" href="../../apps/admin-intent">
                        <span class="badge me-2 d-flex justify-content-center align-items-center icon"><i class="bi bi-robot"></i></span>
                        <span class="nav-link-text ms-1"><?php echo JFStudio\Router::getName(JFStudio\Router::AdminIntent); ?></span>
                    </a>
                </li>

                <?php if ($UserSupport->hasPermission('list_administrators')) { ?>
                    <li class="nav-item">
                        <a class="nav-link <?php if (in_array($route, [JFStudio\Router::AdminAdministrators, JFStudio\Router::AdminAdministratorsAdd, JFStudio\Router::AdminAdministratorsEdit])) { ?>active<?php } ?>" href="../../apps/admin-administrators">
                            <span class="badge me-2 d-flex justify-content-center align-items-center icon"><i class="bi bi-person-circle"></i></span>
                            <span class="nav-link-text text-dark ms-1"><?php echo JFStudio\Router::getName(JFStudio\Router::AdminAdministrators); ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($UserSupport->hasPermission('list_buys')) { ?>
                    <li class="nav-item">
                        <a class="nav-link <?php if (in_array($route, [JFStudio\Router::AdminBuys])) { ?>active<?php } ?>" href="../../apps/admin-buys">
                            <span class="badge me-2 d-flex justify-content-center align-items-center icon"><i class="bi bi-cart"></i></span>
                            <span class="nav-link-text text-dark ms-1"><?php echo JFStudio\Router::getName(JFStudio\Router::AdminBuys); ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($UserSupport->hasPermission('list_buys')) { ?>
                    <li class="nav-item">
                        <a class="nav-link <?php if (in_array($route, [JFStudio\Router::AdminPaymentMethods])) { ?>active<?php } ?>" href="../../apps/admin-payment-methods">
                            <span class="badge me-2 d-flex justify-content-center align-items-center icon"><i class="bi bi-credit-card"></i></span>
                            <span class="nav-link-text text-dark ms-1"><?php echo JFStudio\Router::getName(JFStudio\Router::AdminPaymentMethods); ?></span>
                        </a>
                    </li>
                <?php } ?>

                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Sesión</h6>
                </li>

                <li class="nav-item">
                    <a class="nav-link  " href="?adminLogout=true">
                        <span class="badge me-2 d-flex justify-content-center align-items-center icon"><i class="bi bi-door-closed"></i></span>
                        <span class="nav-link-text text-dark ms-1">Cerrar sesión</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="sidenav-footer mx-3 ">

        </div>
    </aside>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">

        <nav class="navbar navbar-main navbar-expand-lg position-sticky mt-4 top-1 px-0 mx-4 border-radius-xl z-index-sticky shadow-none" id="navbarBlur" data-scroll="true">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <ol class=" d-none breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                        <li class="breadcrumb-item text-sm">
                            <a class="opacity-3 text-dark" href="javascript:;">
                                <svg width="12px" height="12px" class="mb-1" viewBox="0 0 45 40" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                    <title>shop </title>
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <g transform="translate(-1716.000000, -439.000000)" fill="#252f40" fill-rule="nonzero">
                                            <g transform="translate(1716.000000, 291.000000)">
                                                <g transform="translate(0.000000, 148.000000)">
                                                    <path d="M46.7199583,10.7414583 L40.8449583,0.949791667 C40.4909749,0.360605034 39.8540131,0 39.1666667,0 L7.83333333,0 C7.1459869,0 6.50902508,0.360605034 6.15504167,0.949791667 L0.280041667,10.7414583 C0.0969176761,11.0460037 -1.23209662e-05,11.3946378 -1.23209662e-05,11.75 C-0.00758042603,16.0663731 3.48367543,19.5725301 7.80004167,19.5833333 L7.81570833,19.5833333 C9.75003686,19.5882688 11.6168794,18.8726691 13.0522917,17.5760417 C16.0171492,20.2556967 20.5292675,20.2556967 23.494125,17.5760417 C26.4604562,20.2616016 30.9794188,20.2616016 33.94575,17.5760417 C36.2421905,19.6477597 39.5441143,20.1708521 42.3684437,18.9103691 C45.1927731,17.649886 47.0084685,14.8428276 47.0000295,11.75 C47.0000295,11.3946378 46.9030823,11.0460037 46.7199583,10.7414583 Z"></path>
                                                    <path d="M39.198,22.4912623 C37.3776246,22.4928106 35.5817531,22.0149171 33.951625,21.0951667 L33.92225,21.1107282 C31.1430221,22.6838032 27.9255001,22.9318916 24.9844167,21.7998837 C24.4750389,21.605469 23.9777983,21.3722567 23.4960833,21.1018359 L23.4745417,21.1129513 C20.6961809,22.6871153 17.4786145,22.9344611 14.5386667,21.7998837 C14.029926,21.6054643 13.533337,21.3722507 13.0522917,21.1018359 C11.4250962,22.0190609 9.63246555,22.4947009 7.81570833,22.4912623 C7.16510551,22.4842162 6.51607673,22.4173045 5.875,22.2911849 L5.875,44.7220845 C5.875,45.9498589 6.7517757,46.9451667 7.83333333,46.9451667 L19.5833333,46.9451667 L19.5833333,33.6066734 L27.4166667,33.6066734 L27.4166667,46.9451667 L39.1666667,46.9451667 C40.2482243,46.9451667 41.125,45.9498589 41.125,44.7220845 L41.125,22.2822926 C40.4887822,22.4116582 39.8442868,22.4815492 39.198,22.4912623 Z"></path>
                                                </g>
                                            </g>
                                        </g>
                                    </g>
                                </svg>
                            </a>
                        </li>
                        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
                        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Default</li>
                    </ol>
                    <h6 class="font-weight-bolder mb-0 d-none">Default</h6>
                </nav>
                <div class="sidenav-toggler sidenav-toggler-inner d-xl-block d-none ">
                    <a href="javascript:;" class="nav-link p-0 text-body">
                        <div class="sidenav-toggler-inner">
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                        </div>
                    </a>
                </div>
                <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                    <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                        <div class="input-group">
                            <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                            <input type="text" class="form-control" placeholder="Search here..." onfocus="focused(this)" onfocusout="defocused(this)">
                        </div>
                    </div>
                    <ul class="navbar-nav  justify-content-end">
                        <li class="nav-item d-flex align-items-center">
                            <a href="../../pages/authentication/signin/illustration.html" class="nav-link font-weight-bold px-0 text-body" target="_blank">
                                <i class="fa fa-user me-sm-1" aria-hidden="true"></i>
                                <span class="d-sm-inline d-none">Sign In</span>
                            </a>
                        </li>
                        <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                            <a href="javascript:;" class="nav-link p-0 text-body" id="iconNavbarSidenav">
                                <div class="sidenav-toggler-inner">
                                    <i class="sidenav-toggler-line"></i>
                                    <i class="sidenav-toggler-line"></i>
                                    <i class="sidenav-toggler-line"></i>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item px-3 d-flex align-items-center">
                            <a href="javascript:;" class="nav-link p-0 text-body">
                                <i class="fa fa-cog fixed-plugin-button-nav cursor-pointer" aria-hidden="true"></i>
                            </a>
                        </li>
                        <li class="nav-item dropdown pe-2 d-flex align-items-center">
                            <a href="javascript:;" class="nav-link p-0 text-body" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-bell cursor-pointer" aria-hidden="true"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" aria-labelledby="dropdownMenuButton">
                                <li class="mb-2">
                                    <a class="dropdown-item border-radius-md" href="javascript:;">
                                        <div class="d-flex py-1">
                                            <div class="my-auto">
                                                <img class="avatar avatar-sm  me-3 " alt="user image">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="text-sm font-weight-normal mb-1">
                                                    <span class="font-weight-bold">New message</span> from Laur
                                                </h6>
                                                <p class="text-xs text-secondary mb-0">
                                                    <i class="fa fa-clock me-1" aria-hidden="true"></i>
                                                    13 minutes ago
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        {{content}}

        <footer class="container footer pt-3 fixed-bottom">
            <div class="container-fluid">
                <div class="row align-items-center justify-content-center">
                    <div class="col-auto text-center">
                        <div class="copyright text-center text-sm text-muted text-lg-start">
                            © 2023,
                            made with <i class="fa fa-heart"></i> by
                            <a href="https://www.iam.com.mx/" class="font-weight-bold" target="_blank">IAM - admin site</a>
                            for a better web.
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </main>

    <script src="../../src/js/core/bootstrap.bundle.min.js" type="text/javascript"></script>
    <!--   Core JS Files   -->
    <script src="../../src/js/plugins/perfect-scrollbar.min.js" type="text/javascript"></script>
    <script src="../../src/js/plugins/smooth-scrollbar.min.js" type="text/javascript"></script>
    <script src="../../src/js/plugins/chartjs.min.js" type="text/javascript"></script>
    <script src="../../src/js/42d5adcbca.js" type="text/javascript"></script>
    <script src="../../src/js/jquery-3.1.1.js" type="text/javascript"></script>
    <script src="../../src/js/general.js?v=2.4.1" type="text/javascript"></script>
    <script src="../../src/js/alertCtrl.js?v=2.6.4" type="text/javascript"></script>

    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
    <!-- Github buttons -->
    <script async defer src="../../src/js/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="../../src/js/soft-ui-dashboard.min.js?v=2.6.4.6"></script>

    <script src="../../src/js/vue.js"></script>

    {{js_scripts}}

</body>

</html>