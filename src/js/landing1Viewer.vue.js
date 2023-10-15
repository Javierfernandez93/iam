import { User } from '../../src/js/user.module.js?v=2.6.4'   

const Landing1Viewer = {
    name : 'landing1-viewer',
    props : [],
    emits : [],
    data() {
        return {
            viewVideo : false,
            landing : null,
        }
    },
    watch : {
        
    },
    methods: {
        getBackofficeVars : function() {
            this.User.getBackofficeVars({},(response)=>{
                if(response.s == 1)
                {
                    this.landing = response.landing
                }
            })
        },
    },
    updated() {
    },
    mounted() 
    {   

    },
    template : `
        <div class="row d-flex justify-content-center">
            <section class="row justify-content-center container section-1 align-items-center py-5 position-relative">
                <div class="square square-right bg-gradient-warning"></div>
                <div class="col-12 col-xl-6 py-5"> 
                    <div class="row justify-content-center">
                        <div class="col-10">
                            <div class="card shadow-xl border-radius-2xl overflow-hidden bg-transparent border-0">
                                <div v-if="!viewVideo" class="cursor-pointer" @click="viewVideo = true">
                                    <img src="../../src/img/video-cover.png" class="img-fluid">
                                </div>
                                <div v-else>
                                    <video width="480" height="320" controls="" class="py-3">
                                        <source src="../../src/files/video/video-DummieTrading.mp4" type="video/mp4">
                                        <source src="movie.ogg" type="video/ogg"> Your browser does not support the video tag.
                                    </video>
                                </div>
                            </div>

                            <button class="btn w-100 bg-gradient-warning btn-danger shadow-lg btn-lg fs-5">Comienza Ahora</button>
                        </div>
                    </div>
                </div>  
                <div class="col-12 col-xl-6 font-responsive"> 
                    <div class="">
                        <span class="badge bg-white text-warning shadow fs-2"><i class="bi bi-funnel-fill"></i></span>
                        <div class="fs-2">Gana <span class="fw-semibold">hasta el 90%</span> por educación <span class="fw-semibold">herramientas y publicidad</span></div>
                    </div>
                </div>  
            </section>

            <section class="row justify-content-center container section-1 align-items-center">
                <div class="col-12"> 
                    <div class="row justify-content-center"> 
                        <div class="col-4 col-xl-auto fs-3"> 
                            <div class="">Nuestros</div>
                            <div class="fw-semibold">Números</div>
                        </div>
                        <div class="col-8 col-xl"> 
                            <div class="row justify-content-end"> 
                                <div class="col-12 col-xl-9"> 
                                    <div class="row"> 
                                        <div class="col-6 col-xl-3"> 
                                            <div class="fs-3 fw-semibold text-warning">70+</div>
                                            <div class="fs-4">Clientes</div>
                                        </div>
                                        <div class="col-6 col-xl-3"> 
                                            <div class="fs-3 fw-semibold text-warning">7+</div>
                                            <div class="fs-4">Cursos</div>
                                        </div>
                                        <div class="col-6 col-xl-3"> 
                                            <div class="fs-3 fw-semibold text-warning">7+</div>
                                            <div class="fs-4">Herramientas</div>
                                        </div>
                                        <div class="col-6 col-xl-3"> 
                                            <div class="fs-3 fw-semibold text-warning">7+</div>
                                            <div class="fs-4">VCards</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="row justify-content-center align-items-center bg-dark">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="#f9fbfd" fill-opacity="1" d="M0,224L18.5,218.7C36.9,213,74,203,111,192C147.7,181,185,171,222,144C258.5,117,295,75,332,90.7C369.2,107,406,181,443,186.7C480,192,517,128,554,138.7C590.8,149,628,235,665,266.7C701.5,299,738,277,775,234.7C812.3,192,849,128,886,101.3C923.1,75,960,85,997,112C1033.8,139,1071,181,1108,181.3C1144.6,181,1182,139,1218,144C1255.4,149,1292,203,1329,197.3C1366.2,192,1403,128,1422,96L1440,64L1440,0L1421.5,0C1403.1,0,1366,0,1329,0C1292.3,0,1255,0,1218,0C1181.5,0,1145,0,1108,0C1070.8,0,1034,0,997,0C960,0,923,0,886,0C849.2,0,812,0,775,0C738.5,0,702,0,665,0C627.7,0,591,0,554,0C516.9,0,480,0,443,0C406.2,0,369,0,332,0C295.4,0,258,0,222,0C184.6,0,148,0,111,0C73.8,0,37,0,18,0L0,0Z"></path></svg>
        </div>

        <section class="row justify-content-center section-1 align-items-center bg-dark text-white">
            <div class="row container align-items-center pb-5">
                <div class="col-12 col-xl-auto fs-2">
                    <div>¿Qué nos hace </div>
                    <div class="fw-semibold">diferentes?</div>
                </div>
                <div class="col-12 col-xl">
                    <div class="row justify-content-end">
                        <div class="col-12 col-xl-9">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item border-secondary py-5">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="badge bg-gradient-warning fs-2"><i class="bi bi-piggy-bank-fill"></i></span>
                                        </div>
                                        <div class="col fs-3 text-white">
                                            <div class="">Gana <span class="fw-semibold">hasta un 90%</span> por educación en marketing dígital</div>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item border-secondary py-5">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="badge bg-gradient-warning fs-2"><i class="bi bi-collection-play-fill"></i></span>
                                        </div>
                                        <div class="col fs-3 text-white">
                                            <div class="">Diversos cursos <span class="fw-semibold">profesionales</span> que te ayudarán a aprender de verdad</div>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item border-secondary py-5">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="badge bg-gradient-warning fs-2"><i class="bi bi-funnel"></i></span>
                                        </div>
                                        <div class="col fs-3 text-white">
                                            <div class="">Sistema de <span class="fw-semibold">publicidad dígital</span> efectiva y justa</div>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item border-secondary py-5">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="badge bg-gradient-warning fs-2"><i class="bi bi-balloon-heart"></i></span>
                                        </div>
                                        <div class="col fs-3 text-white">
                                            <div class="">Más de 7 años de experiencia en <span class="fw-semibold">marketing y redes</span> </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>

                        </div>
                    </div>
                </div>
                <div class="row justify-content-center"> 
                    <div class="col-12 col-xl-6 fs-3"> 
                        <button class="btn w-100 bg-gradient-warning btn-danger shadow-lg btn-lg fs-2">Comienza Ahora</button>
                    </div>
                </div>
            </div>
        </section>

        <div class="row d-flex justify-content-center section-1 py-5">
            <section class="row justify-content-center container align-items-center position-relative">
                <div class="square square-left bg-gradient-warning"></div>

                <div class="col-12 col-xl-6 py-5"> 
                    <div class="row">
                        <div class="col-10">
                            <img src="../../src/img/landing1-bottom.png" class="img-fluid">
                        </div>
                    </div>
                </div>  
                <div class="col-12 col-xl-6 py-5"> 
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="fw-semibold fs-2 mb-3">DummieTrading está dirigido especialmente para tí</div>
                            <div class="fs-4 mb-3">Las <span class="fw-semibold">herrramientas que desarrollamos</span> aunada a la educación que impartimos es la fuente del éxito. <span class="fw-semibold">Nuestra plataforma conjunta 4 poderozas formulas</span> para lograr el <span class="fw-semibold">éxito:</span></div>
                            <div class="fs-4 mb-3">
                                <ul class="list-group list-grou-flush">
                                    <li class="list-group-item border-0 px-0">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="badge bg-gradient-warning fs-4"><i class="bi bi-laptop"></i></span>
                                            </div>
                                            <div class="col">
                                                Herramientas <span class="fw-semibold">digitales</span>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item border-0 px-0">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="badge bg-gradient-warning fs-4"><i class="bi bi-lightning-charge-fill"></i></span>
                                            </div>
                                            <div class="col">
                                                <span class="fw-semibold">Academia de marketing</span> para tú educación
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item border-0 px-0">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="badge bg-gradient-warning fs-4"><i class="bi bi-badge-ad-fill"></i></span>
                                            </div>
                                            <div class="col">
                                                Publicidad <span class="fw-semibold">digital</span>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item border-0 px-0">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="badge bg-gradient-warning fs-4"><i class="bi bi-gift"></i></span>
                                            </div>
                                            <div class="col">
                                                Sistema de afiliados en el que puedes <span class="fw-semibold">ganar hasta un 90%</span>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="row"> 
                        <div class="col-12 col-xl-10"> 
                            <button class="btn w-100 bg-gradient-warning btn-danger shadow-lg btn-lg fs-2">Comienza Ahora</button>
                        </div>
                    </div>
                </div>  
            </section>
        </div>
        <div class="row py-3">
            <div class="col-12 text-center">
                Powered by Mizuum ® All right reserved 2022
            </div>
        </div>
    `,
}

export { Landing1Viewer } 