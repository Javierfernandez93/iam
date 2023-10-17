<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    
    <title>{{title}}</title>

    <link rel="icon" type="image/x-icon" href="../../src/img/meta.png">
    <link rel="apple-touch-icon" sizes="76x76" href="../../src/img/meta.png">
    <link rel="icon" type="image/png" href="../../src/img/favicon.png">
    <link rel="shortcut icon" href="../../src/img/favicon.png" />

    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1" />
    
    <meta content="Tu Aliado para el Cuidado Personal" name="description" />
    <meta content="Tu Aliado para el Cuidado Personal" name="author" />

    <meta property="og:site_name" content="I am beauty Oil">
    <meta property="og:title" content="I am beauty Oil" />
    <meta property="og:description" content="Tu Aliado para el Cuidado Personal" />
    <meta property="og:image" itemprop="image" content="../../src/img/logo.png">
    <meta property="og:type" content="website" />
    <meta property="og:updated_time" content="1693339145" />
    <meta name="theme-color" content="#99195f">  
    
    <!-- Link Swiper's CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />

    <!-- styles -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link id="pagestyle" href="../../src/css/soft-ui-dashboard.css?v=2.0.8" rel="stylesheet" />
    <link id="pagestyle" href="../../src/css/general.css?v=2.0.8" rel="stylesheet" />
</head>

<body class="">
    <div class="py-5 z-index-3 top-0 w-100 animation-fall-down" style="--delay:1500ms" id="navbar">
        <div class="container">
            <header class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between">
                <a href="../../apps/home" class="d-flex align-items-center col-md-3 mb-2 mb-md-0 text-dark text-decoration-none">
                    <img src="../../src/img/logo.png" id="logo" class="" style="width: 12rem;">
                </a>

                <div class="col-md-auto text-end">
                    <a href="../../apps/login/" type="button" class="btn mb-0 me-2 fs-5 shadow-none btn-outline-primary">Iniciar sesión</a>
                    <a href="../../apps/signup/" type="button" class="btn mb-0 fs-5 shadow btn-light">Crear cuenta</a>
                </div>
            </header>
        </div>
    </div>
    <div class="container vh-100">
        {{content}}
    </div>

    <footer class="bg-dark py-5">
        <div class="container lead text-white">
            <div class="row gx-11 align-items-center">
                <div class="col-12 col-xl-4 mb-3 mb-xl-0">
                <img src="../../src/img/logo.png" id="logo" class="" style="width: 12rem;">
                    <div class="text-white h3 text-light">Descubre la Belleza en lo Natural</div>

                    <?php echo DummieTrading\SystemVar::_getValue('company_address'); ?>
                    <?php if($email = DummieTrading\SystemVar::_getValue('company_email')) { ?>
                        <?php echo $email?>
                    <?php } ?>
                </div>
                <div class="col-12 col-xl-4 mb-3 mb-xl-0">
                    <div class="fs-4 fw-bold text-uppercase">Redes sociales</div>

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item border-0 bg-transparent px-0"><a href="<?php echo DummieTrading\SystemVar::_getValue('social_facebook'); ?>" class="text-white" target="_blank"><i class="bi bi-facebook me-1"></i> Facebook</a></li>
                        <li class="list-group-item border-0 bg-transparent px-0"><a href="<?php echo DummieTrading\SystemVar::_getValue('social_instagra'); ?>" class="text-white" target="_blank"><i class="bi bi-instagram me-1"></i> Instagram</a></li>
                        <li class="list-group-item border-0 bg-transparent px-0"><a href="<?php echo DummieTrading\SystemVar::_getValue('social_whatsapp'); ?>" class="text-white" target="_blank"><i class="bi bi-whatsapp me-1"></i> Whatsapp</a></li>
                    </ul>
                </div>
                <div class="col-12 col-xl-4">
                    <div class="fs-4 fw-bold text-uppercase">Links</div>

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item border-0 bg-transparent px-0"><a class="text-white" href="../../apps/home">Inicio</a></li>
                        <li class="list-group-item border-0 bg-transparent px-0"><a class="text-white" href="../../apps/home">Nosotros</a></li>
                        <li class="list-group-item border-0 bg-transparent px-0"><a class="text-white" href="../../apps/home">Ayuda</a></li>
                        <li class="d-none list-group-item border-0 bg-transparent px-0"><a class="text-white" href="../../apps/home">Contactar</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <script src="../../src/js/jquery-3.5.1.min.js" type="text/javascript"></script>
    <script src="../../src/js/general.js?v=2.6.6" type="text/javascript"></script>
    <script src="../../src/js/alertCtrl.js?v=2.6.6" type="text/javascript"></script>
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

    <script src="../../src/js/vue.js?v=2.6.6" type="text/javascript"></script>
    {{css_scripts}}
    {{js_scripts}}

</body>

</html>