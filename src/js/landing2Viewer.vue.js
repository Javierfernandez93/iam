import { Guest } from '../../src/js/guest.module.js?v=2.6.6'   

const Landing2Viewer = {
    name : 'landing2-viewer',
    props : [],
    emits : [],
    data() {
        return {
            Guest: new Guest,
            hideCookies: false,
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
            this.Guest.addVisitPerLanding({utm:utm,catalog_landing_id:1},(response)=>{
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
                    window.location.href = MAIN_PATH + '/apps/backoffice'
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
        $(this.$refs.phone).mask('(00) 0000-0000');

        this.getReferralData(getLastUrlPart()).then(()=>{
            this.getCountries()
            this.getUtm() // getting campaign
            
            this.addVisitPerLanding(getLastUrlPart())
        })
    },
    template : `
        <div class="container" id="main">
            <header class="d-flex flex-wrap justify-content-center py-3 align-items-center">
            <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-dark text-decoration-none">
                <svg class="bi me-2" width="40" height="32"><use xlink:href="#bootstrap"></use></svg>
                <span class="fs-4">
                    <img src="https://www.iam.com.mx/src/img/logo-horizontal-dark-letters.svg" width="222px" alt="logo">
                </span>
            </a>

            <ul class="nav nav-pills">
                <li class="nav-item"><a href="#" class="nav-link active" aria-current="page">INICIO</a></li>
                <li class="nav-item"><a href="https://www.iam.com.mx/apps/login" class="nav-link">Iniciar sesión</a></li>
            </ul>
            </header>
        </div>

        <section class="section-1 cover position-relative" style="background-image: url('https://www.iam.com.mx/src/img/landing-1.jpg')">
            <div class="row align-items-center bg-gradient-warning position-relative text-center fs-3 fw-semibold text-white py-3" style="z-index:1">
                <div class="col-12">
                    Empieza a ganar dinero por internet y mejora tus finanzas
                </div>
            </div>
            <div class="row align-items-center section-1 px-5 px-md-0">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-xl-6 text-white py-5 py-md-0">
                            <div class="fs-2 fw-semibold mb-3">"Inicia un negocio rentable vendiendo productos digitales por internet, de forma fácil, rápida y sin salir de casa"</div>
                            <div class="fs-4">Nuestro equipo te enseñará las habilidades necesarias para vender productos digitales por internet como afiliado</div>
                        </div>
                        <div class="col-xl-6">
                            <div class="row justify-content-center">
                                <div class="col-12 col-xl-10">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row align-items-center pb-3" v-if="user.referral">
                                                <div class="avatar avatar-xl">
                                                    <img :src="user.referral.image" class="avatar rounded-circle shadow-lg" alt="Imagen de usuario" />
                                                </div>
                                                <div class="col">
                                                    Te está invitando: <span class="fw-semibold text-dark">{{user.referral.names}}</span> con el email: <span class="fw-semibold text-dark">{{user.referral.email}}</span>. Si no es quien te invitó, comunícate con quien lo hizo.
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
                                            <button @click="doSignup" class="btn btn-lg btn-warning border-5 border-dark w-100 text-dark fs-4 fw-semibold">Comienza AHORA</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="section-2 py-5 bg-white">
            <div class="row align-items-center py-5 px-5 px-md-0">
                <div class="container text-center">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="fw-semibold text-dark fs-2">¿Qué es DummieTrading?</div>
                        </div>
                    </div>
                    <div class="row py-5">
                        <div class="col-xl-4">
                            <div class="fw-semibold text-dark fs-4">Nuestra misión</div>    
                            <img src="https://www.iam.com.mx/src/img/landing-1-section-0-1.jpg" class="img-fluid" alt="imagen1">
                            
                            <div class="text-dark fs-5">Ayudamos a los emprendedores novatos a iniciar un negocio rentable, vendiendo productos digitales por internet</div>
                        </div>
                        <div class="col-xl-4">
                            <div class="fw-semibold text-dark fs-4">¿Qué enseñamos?</div>
                            <img src="https://www.iam.com.mx/src/img/landing-1-section-0-2.jpg" class="img-fluid" alt="imagen2">
                            <div class="text-dark fs-5">Enseñamos las habilidades necesarias para vender productos digitales por internet: marketing digital, ventas online, tráfico web y conversiones</div>
                        </div>
                        <div class="col-xl-4">
                            <div class="fw-semibold text-dark fs-4">¿Cómo enseñamos?</div>
                            <img src="https://www.iam.com.mx/src/img/landing-1-section-0-3.jpg" class="img-fluid" alt="imagen3">
                            <div class="text-dark fs-5">Tenemos una academia donde ofrecemos cursos online que enseñan paso a paso y desde cero, cómo convertirte en súper afiliado o productor exitoso</div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-12 col-xl-6">
                            <a href="#main" class="btn btn-lg btn-warning border-5 border-dark w-100 text-dark fs-4 fw-semibold">Comienza AHORA!</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="section-2 py-5 bg-gradient-warning text-white">
            <div class="row align-items-center py-5 px-5 px-md-0">
                <div class="container">
                    <div class="row text-center">
                        <div class="col-xl-12">
                            <div class="fw-semibold text-white text-decoration-underline fs-1">¿Para quién es DummieTrading?</div>
                        </div>
                    </div>
                    <div class="row py-5">
                        <div class="col-xl-4">
                            <div class="row mb-3 align-items-center">
                                <div class="col-auto">
                                    <i class="bi bi-patch-check fs-2 text-white"></i>
                                </div>
                                <div class="col fs-5 fw-semibold">
                                    Emprendedores novatos
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <div class="col-auto">
                                    <i class="bi bi-patch-check fs-2 text-white"></i>
                                </div>
                                <div class="col fs-5 fw-semibold">
                                    Emprendedores experimentados
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <div class="col-auto">
                                    <i class="bi bi-patch-check fs-2 text-white"></i>
                                </div>
                                <div class="col fs-5 fw-semibold">
                                    Vendedores
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <div class="col-auto">
                                    <i class="bi bi-patch-check fs-2 text-white"></i>
                                </div>
                                <div class="col fs-5 fw-semibold">
                                    Networkers
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="row mb-3 align-items-center">
                                <div class="col-auto">
                                    <i class="bi bi-patch-check fs-2 text-white"></i>
                                </div>
                                <div class="col fs-5 fw-semibold">
                                    Afiliados
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <div class="col-auto">
                                    <i class="bi bi-patch-check fs-2 text-white"></i>
                                </div>
                                <div class="col fs-5 fw-semibold">
                                    Productores digitales
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <div class="col-auto">
                                    <i class="bi bi-patch-check fs-2 text-white"></i>
                                </div>
                                <div class="col fs-5 fw-semibold">
                                    Youtubers
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <div class="col-auto">
                                    <i class="bi bi-patch-check fs-2 text-white"></i>
                                </div>
                                <div class="col fs-5 fw-semibold">
                                    Influencers
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="row mb-3 align-items-center">
                                <div class="col-auto">
                                    <i class="bi bi-patch-check fs-2 text-white"></i>
                                </div>
                                <div class="col fs-5 fw-semibold">
                                    Freelancers
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <div class="col-auto">
                                    <i class="bi bi-patch-check fs-2 text-white"></i>
                                </div>
                                <div class="col fs-5 fw-semibold">
                                    Profesionales Independientes
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <div class="col-auto">
                                    <i class="bi bi-patch-check fs-2 text-white"></i>
                                </div>
                                <div class="col fs-5 fw-semibold">
                                    Empleados y desempleados
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <div class="col-auto">
                                    <i class="bi bi-patch-check fs-2 text-white"></i>
                                </div>
                                <div class="col fs-5 fw-semibold">
                                    Estudiantes universitarios
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-12 col-xl-6">
                            <a href="#main" class="btn btn-lg btn-danger border-5 border-white shadow-lg w-100 text-white fs-4 fw-semibold">Comienza AHORA!</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="section-2 bg-white py-5">
            <div class="row align-items-center py-5 px-5 px-md-0">
                <div class="container">
                    <div class="row text-center">
                        <div class="col-xl-12">
                            <div class="fw-semibold fs-1">¿Qué te ofrece DummieTrading?</div>
                        </div>
                    </div>
                    <div class="row py-5">
                        <div class="col-xl-6 text-center border-right">
                            <div>
                                <div class="fs-3 text-warning fw-semibold mb-3">
                                    <i class="bi bi-patch-check fs-2 text-white"></i>
                                    Academia
                                </div>
                                <div class="col-12 fs-5">
                                    <p>Te ofrecemos una academia (Standard y Elite) donde encontrarás cursos online:</p>

                                    <p>“Que te enseñan paso a paso y desde cero a vender productos digitales como afiliado o productor”</p>
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <div class="col-12">
                                    <img src="https://www.iam.com.mx/src/img/landing-1-section-1-1.jpg" class="img-fluid" style="height:22rem" alt="imagen4">
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-12 col-xl-10">
                                    <a href="#main" class="btn btn-lg btn-danger border-5 border-white shadow-lg w-100 text-white fs-4 fw-semibold">Comienza AHORA!</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 text-center">
                            <div>
                                <div class="fs-3 text-warning fw-semibold mb-3">
                                    <i class="bi bi-patch-check fs-2 text-white"></i>
                                    Escuelas
                                </div>
                                <div class="col-12 fs-5">
                                    <p>Te ofrecemos la oportunidad de iniciar un negocio rentable de forma inmediata:</p>

                                    <p>“Vendiendo nuestras HERRAMIENTAS y cobrando hasta el 90% de comisión por todas las ventas que realices"”</p>
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <div class="col-12">
                                    <img src="https://www.iam.com.mx/src/img/landing-1-section-1-2.jpg" class="img-fluid" style="height:22rem" alt="imagen5">
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-12 col-xl-10">
                                    <a href="#main" class="btn btn-lg btn-danger border-5 border-white shadow-lg w-100 text-white fs-4 fw-semibold">Comienza AHORA!</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div v-if="user.referral.phone" class="position-fixed top-50 end-0 translate-middle-y pe-4" style="z-index:10">
            <div class="mb-3">
                <a :href="user.referral.phone.getWhatsAppFromText('¡Hola! me gustaría tener más información de DummieTrading')" target="_blank" class="btn btn-success rounded-circle shadow-lg">
                    <i class="bi bi-whatsapp display-1"></i>
                </a>
            </div>
            <div>
                <a :href="user.referral.phone.getTelegramFromPhone()" target="_blank" class="btn btn-info rounded-circle shadow-lg">
                    <i class="bi bi-telegram display-1"></i>
                </a>
            </div>
        </div>

        <div class="position-fixed bg-dark w-100 text-white bottom-0 start-0 text-xxs py-2" v-if="!hideCookies">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col text-xxs">
                        Utilizamos cookies propias y de terceros para realizar estadísticas de uso de la web con la finalidad de identificar fallos y poder mejorar los contenidos y configuración de la página web. También utilizamos cookies propias y de terceros para recordar algunas opciones que hayas elegido y para mostrarte publicidad relacionada con tus preferencias, en base a un perfil elaborado a partir de tus hábitos de navegación (por ejemplo, a partir de las páginas web visitadas).
                    </div>
                    <div class="col-auto">
                        <button @click="hideCookies = true" class="btn btn-sm btn-light">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { Landing2Viewer } 