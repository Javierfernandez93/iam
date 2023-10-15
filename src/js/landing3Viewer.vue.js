import { Guest } from '../../src/js/guest.module.js?v=1.1.4'   

const Landing3Viewer = {
    name : 'landing3-viewer',
    props : [],
    emits : [],
    data() {
        return {
            Guest: new Guest,
            showing: false,
            hideCookies: false,
            redirection: {
                page: null,
                route_name: null,
                caption: null
            },
            user: {
                email: null,
                phone: null,
                names: null,
                country_id: 159, // loads by default México
                passwordAgain: null,
                password: null,
                referral: {
                    user_login_id: 0,
                    names: '',
                    image : ''
                },
                utm: false,
            },
            countries : {},
            loading : false,
            User : null,
            feedback : false,
            isValidMail : false,
            passwordsMatch : null,
            fieldPasswordType : 'password',
            userComplete : false,
        }
    },
    watch : {
        user : {
            handler() {
                this.checkEmail()
                this.checkFields()
                this.checkPasswords()
            },
            deep: true
        },
    },
    methods: {
        getReferralData : function(utm) {
            return new Promise((resolve)=>{
                this.feedback = false

                this.Guest.getReferralData({utm},(response)=>{
                    if(response.s == 1)
                    {
                        Object.assign(this.user.referral,response.referral)
                    } else if(response.r == "NOT_DATA") {
                        this.feedback = "No encontramos información del link de referido proporcionado"
                    }

                    resolve()
                })
            })
        },
        addVisitPerLanding : function(utm) {
            this.Guest.addVisitPerLanding({utm:utm,catalog_landing_id:2},(response)=>{
                if(response.s == 1)
                {
                    
                }
            })
        },
        toggleFieldPasswordType : function() {
            this.fieldPasswordType = this.fieldPasswordType == 'password' ? 'text' : 'password'
        },
        doSignup : function() {
            this.loading = true
            this.feedback = false
            
            this.Guest.doSignup(this.user,(response)=>{
                this.loading = false

                if(response.s == 1)
                {
                    if(this.redirection.page)
                    {
                        window.location.href = this.redirection.page
                    } else {
                        window.location.href = MAIN_PATH + '/apps/backoffice'
                    }
                } else if(response.r == "MAIL_ALREADY_EXISTS") {
                    this.feedback = 'El correo proporcionado ya existe'
                }
            })
        },
        getCountries : function() {
            this.Guest.getCountries({},(response)=>{
                if(response.s == 1)
                {
                    this.countries = response.countries
                }
            })
        },
        checkEmail : function() {
            this.isValidMail = isValidMail(this.user.email)
        },
        getUtm : function() {
            if(window.location.pathname.split('/').inArray('join') != -1) {
                this.user.utm = 'join'
            }
        },
        openModal : function() {
            $(this.$refs.modal).modal('show')
        },
        openVideo : function() {
            $(this.$refs.modalVideo).modal('show')
            this.showing = true
        },
        checkPasswords : function() {
            if(this.user.password != null && this.user.passwordAgain != null)
            {
                if(this.user.passwordAgain != this.user.password)   
                {
                    this.passwordFeedback = `<span class="text-danger fw-bold"><i class="bi bi-patch-exclamation"></i> Las contraseñas no coinciden</span>`
                } else {
                    this.passwordFeedback = '<span class="text-success fw-bold"><i class="bi bi-patch-check"></i> Las contraseñas coinciden</span>'
                }
            }
        },
        checkFields : function() {
            this.userComplete = this.isValidMail && this.user.password && this.user.phone && this.user.names
        }
    },
    mounted() 
    {
        this.getReferralData(getLastUrlPart()).then(()=>{
            this.getCountries()
            this.getUtm() // getting campaign

            if(getParam("page"))
            {
                this.redirection.page = getParam('page')
                this.redirection.caption = getParam('caption')

                setTimeout(() => {
                    this.openModal()
                }, 1000);
            }
            
            this.addVisitPerLanding(getLastUrlPart())
        })
    },
    template : `
        <div class="bg-dark">
            <div class="container">
                <header class="d-flex flex-wrap justify-content-center py-3 align-items-center">
                    <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-dark text-decoration-none">
                        <svg class="bi me-2" width="40" height="32"><use xlink:href="#bootstrap"></use></svg>
                        <span class="fs-4">
                            <img src="https://www.iam.com.mx/src/img/logo-horizontal-white.svg" width="202" alt="logo">
                        </span>
                    </a>

                </header>
            </div>
        </div>

        <section class="section-1 cover position-relative bg-dark" data-entrance="from-bottom">
            <div class="row align-items-center section-1 px-5 px-md-0">
                <div class="container">
                    <div class="row align-items-center">
                        
                        <div class="col-xl-12">
                            <div class="display-1 text-center text-white">
                                <div>Tu nueva profesión</div>
                                <div>Tu nuevo negocio</div>
                                <div>En menos de <span class="text-warning">60 días</span></div>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                <button @click="openModal" class="btn btn-warning text-white fw-sembold fs-4 btn-lg me-2 text-uppercase">Quiero saber más</button>
                                <button @click="openVideo" class="btn btn-light fw-sembold fs-4 btn-lg text-uppercase">Ver trailer</button>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                <a href="#section-2"><i class="bi bi-arrow-down-short text-white display-1"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="section-2 cover position-relative bg-dark" id="section-2">
            <div class="row align-items-center section-1 px-5 px-md-0">
                <div class="container">
                    <div class="row align-items-center">
                        
                        <div class="col-xl-12">
                            <div class="display-1 text-center text-white">
                                <div>Tu quieres </div>
                                <div>emprender</div>
                                <div>Nosotros te enseñamos cómo.</div>
                            </div>
                            <div class="d-flex justify-content-center mt-3 fs-3 text-center text-white fst-italic">
                                "DummieTrading es la plataforma online que está ayudando a miles de personas a educarse en negocios, marketing y empredimiento" - RCN radio.
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                <a href="#section-3"><i class="bi bi-arrow-down-short text-white display-1"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="bg-dark py-5" data-entrance="from-bottom" id="section-3">
            <div class="row align-items-center section-1 py-4">
                <div class="container text-white text-center">
                    <div class="row align-items-center justify-content-center pb-3">
                        <div class="col-xl-10">
                            <div class="display-2">ACADEMIA ELITE Y STANDAR </div>
                            <div class="fs-2 text-gradient text-warning">Empieza gratis y expande tu potencial</div>
                        </div>
                    </div>
                    <div class="row align-items-center justify-content-center" data-entrance="from-right">
                        <div class="col-9 col-xl-10">
                            <div class="pb-3 mb-3">
                                <img src="https://www.iam.com.mx/src/img/landing2/macebook.png" alt="pc" class="img-fluid">
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-center justify-content-center">
                        <div class="col-xl-6">
                            <button @click="openModal" class="btn btn-warning fs-3 text-white w-100">EXPLORA TODA <b><u>NUESTRA ACADEMIA</u></b></button>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        <a href="#section-4"><i class="bi bi-arrow-down-short text-white display-1"></i></a>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-3 py-5 py-xl-0" id="section-4">
            <div class="row align-items-center section-1 px-5 px-md-0">
                <div class="container text-white text-center">
                    <div class="row align-items-center">
                        <div class="col-xl-12">
                            <div class="pb-3 mb-3">
                                <div class="fs-6">"Que 1 millón de emprendedores facturen</div>
                                <div class="display-1">1 millón de dólares</div>
                                <div class="fs-6">esa es nuestra misión"</div>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-12 col-xl-4">
                            <div class="card bg-dark border-0">
                                <div class="card-body">
                                    <div class="display-1">+63,000</div>
                                    <div class="fs-4">Emprendedores</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-xl-4">
                            <div class="card bg-dark border-0">
                                <div class="card-body">
                                    <div class="display-1">100%</div>
                                    <div class="fs-4">En Español</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-xl-4">
                            <div class="card bg-dark border-0">
                                <div class="card-body">
                                    <div class="display-1">108 Países<div>
                                    <div class="fs-4">con Estudiantes Actvios</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
       

        <section class="bg-white py-5 py-xl-0" data-entrance="from-bottom">
            <div class="row align-items-center section-1 px-5 px-md-0">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-xl-5">
                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <div class="border-bottom pb-3 mb-3">
                                        <div class="display-1">MILES</div>
                                        <div class="display-4">DE CASOS DE ÉXITO</div>
                                    </div>
                                    <div class="fs-4">
                                        <div>Demuestran que nuestra modelo educativo simplemente <b>FUNCIONA</b></div>

                                        <button @click="openModal" class="btn btn-lg w-100 shadow text-white mt-3 fs-5 btn-warning text-uppercase">Ver casos de éxito</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-7">
                            <div class="row justify-content-center">
                                <div class="col-12 col-xl-10">
                                    <img src="https://www.iam.com.mx/src/img/landing2/phones.png" class="img-fluid">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="bg-dark py-5">
            <div class="row align-items-center section-1 px-5 px-md-0">
                <div class="container">
                    <div class="row align-items-center pb-5">
                        <div class="col-xl-12 text-white text-center">
                            <div class="display-1">CIEN MIL RAZONES</div>
                            <div class="display-3">PARA UNIRTE A <span class="text-warning">DummieTrading</span></div>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-xl-7" data-entrance="from-right">
                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <img src="https://www.iam.com.mx/src/img/landing2/computer.png" class="w-100">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-5">
                            <div class="row justify-content-center">
                                <div class="col-12 col-xl-10 text-white">
                                    <div class="mb-5 fs-4">Todos los días entregando nuevos reconocimientos a los estudiantes que llegan a sus primeros </div>

                                    <div class="fs-5 mb-4">
                                        <div>100K DÓLARES EN FACTURACIÓN CON LO QUE APRENDEN</div>
                                        <div>EN DummieTrading</div>
                                    </div>

                                    <button @click="openModal" class="btn btn-warning w-100 text-white btn-lg fs-5 text-uppercase">Obtén tu <b>reconocimiento</b></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        <a href="#section-6"><i class="bi bi-arrow-down-short text-white display-1"></i></a>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white py-5" data-entrance="from-bottom" id="section-6">
            <div class="row align-items-center section-1 px-5 px-md-0">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-xl-5">
                            <div class="row justify-content-center">
                                <div class="col-5 col-xl-9">
                                    <img src="https://www.iam.com.mx/src/img/landing2/phone-telegram.png" class="img-fluid">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-7">
                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <div class="pb-3 mb-3">
                                        <div class="display-1">APRENDE, CONECTA, <span class="text-warning">INSPÍRATE...</span></div>
                                        <div class="display-4">El mejor lugar para hacerlo es el canal de Telegram                                        </div>
                                    </div>
                                    <div class="fs-4">
                                        <a href="https://zuum.link/TF7" target="_blank" class="btn btn-lg w-100 shadow text-white mt-3 fs-5 btn-info text-uppercase">UNIRME AL <b>TELEGRAM OFICIAL</b></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </section>

        <footer class="bg-dark row text-center text-white py-3">
            <div class="col">
                <div>DummieTrading powered by <a href="https://www.mizuum.com">Zuum</a> 2023® All rights reserved</div>
            </div>
        </footer>
     
        <div class="modal fade" id="exampleModal" ref="modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="card-body">
                            <div class="alert alert-success border-0 text-white fs-4 " v-if="redirection.page">
                                <b>{{ redirection.caption }}</b>
                            </div>

                            <div class="row align-items-center pb-3" v-if="user.referral">
                                <div class="avatar avatar-xl">
                                    <img :src="user.referral.image" class="avatar rounded-circle shadow-lg" alt="Imagen de usuario" />
                                </div>
                                <div class="col">
                                    Te está invitando: <b class="text-dark">{{user.referral.names}}</b> con el email: <b class="text-dark">{{user.referral.email}}</b>. Si no es quien te invitó, comunícate con quien lo hizo.
                                </div>
                            </div>

                            <div class="form-floating mb-3">
                                <input 
                                    :class="user.names ? 'is-valid' : ''"
                                    :autofocus="true" type="text" ref="names" v-model="user.names" class="form-control" @keydown.enter.exact.prevent="$refs.phone.focus()" placeholder="Nombre" aria-label="Nombre" aria-describedby="basic-addon1">
                                <label>Nombre</label>
                            </div>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <div class="form-floating">
                                        <select class="form-select" v-model="user.country_id" aria-label="Selecciona tu país">
                                            <option>Selecciona tu país</option>
                                            <option v-for="country in countries" v-bind:value="country.country_id">
                                                {{ country.nicename }} <span v-if="country.phone_code > 0">+ {{ country.phone_code }}</span>
                                            </option>
                                        </select>
                                        <label>País</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-floating">
                                        <input 
                                            :class="user.phone ? 'is-valid' : ''"
                                            type="text" ref="phone" v-model="user.phone" class="form-control" @keydown.enter.exact.prevent="$refs.email.focus()" placeholder="Teléfono" aria-label="Teléfono" aria-describedby="basic-addon1">
                                        <label>Teléfono</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-floating mb-3">
                                <input 
                                    :class="isValidMail ? 'is-valid' : ''"
                                    type="email" ref="email" v-model="user.email" class="form-control" @keydown.enter.exact.prevent="$refs.password.focus()" placeholder="Email" aria-label="Email" aria-describedby="basic-addon1">
                                    <label>Correo electrónico</label>
                            </div>

                            <div class="form-floating mb-3 position-relative">
                                <input 
                                    :class="user.password ? 'is-valid' : ''"
                                    :type="fieldPasswordType" 
                                    ref="password" 
                                    @keydown.enter.exact.prevent="$refs.passwordAgain.focus()" 
                                    v-model="user.password" 
                                    class="form-control" placeholder="Password" aria-label="Password">
                                <label>Contraseña</label>
                                <button class="btn btn-secondary position-absolute end-0 top-0 mt-2 me-2" type="button" id="button-addon2" @click="toggleFieldPasswordType">
                                    <i v-if="fieldPasswordType == 'password'" class="bi bi-eye"></i>
                                    <i v-else class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="pb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Acepto las políticas de privacidad y las condiciones generales de contratación de escuelas
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button @click="doSignup" type="button" class="btn btn-warning text-white btn-lg w-100 fs-4" data-bs-dismiss="modal">UNIRME A DummieTrading</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="modalVideo" ref="modalVideo" tabindex="-1" aria-labelledby="modalVideoLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-dark">
                    <div class="modal-header border-0">
                        <button type="button" class="btn-close btn-dark" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="card-body" v-if="showing">
                            <div class="row justify-content-center">
                                <div class="col-12 col-xl-8">
                                    <img src="https://www.iam.com.mx/src/img/logo-horizontal-white-letters.png" alt="logo" class="img-fluid">
                                </div>
                            </div>
                            <div style="padding:56.3% 0 0 0;position:relative;"><iframe src="https://player.vimeo.com/video/756991672?h=0aa70af3e0&autoplay=1&color=ffffff&title=0&byline=0&portrait=0" style="position:absolute;top:0;left:0;width:100%;height:100%;" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe></div><script src="https://player.vimeo.com/api/player.js"></script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { Landing3Viewer } 