import { Guest } from '../../src/js/guest.module.js?v=1.1.4'   

const Landing4Viewer = {
    name : 'landing4-viewer',
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
            this.Guest.addVisitPerLanding({utm:utm,catalog_landing_id:3},(response)=>{
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
        onlineAccess(phone) {
            window.location.href = `https://api.whatsapp.com/send/?phone=${phone}&text=Quiero mi acceso para el curso online de Marketing Automatizado`
        },
        presentialAccess(phone) {
            window.location.href = `https://api.whatsapp.com/send/?phone=${phone}&text=Vivo en Cúcuta y quiero mi entrada para el curso de Marketing Automatizado`
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
        <div class="row justify-content-center vh-100 align-items-center" id="main">
            <div class="col-11 col-xl-6 B612 text-center">
                <div class="row mb-5 text-white">
                    <div class="col-12">
                        <div class="fs-1" style="font-family font-size: 27px; font-weight: 800;">CURSO DE MARKETING AUTOMATIZADO</div>
                        <div class="row justify-content-center">
                            <div class="col-10 col-xl-4"> <img src="https://www.iam.com.mx/src/img/logo-horizontal-white.svg" class="img-fluid"> </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6 col-xl-3">
                        <div class="card shadow-lg bg-dark border-0 text-white">
                            <div class="card-body">
                                <div class="fs-1 fw-bold" id="days">8</div>
                                <div class="fs-4">Días</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-3">
                        <div class="card shadow-lg bg-dark border-0 text-white">
                            <div class="card-body">
                                <div class="fs-1 fw-bold" id="hours">7</div>
                                <div class="fs-4">Horas</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-3">
                        <div class="card shadow-lg bg-dark border-0 text-white">
                            <div class="card-body">
                                <div class="fs-1 fw-bold" id="minutes">7</div>
                                <div class="fs-4">Minutos</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-3">
                        <div class="card shadow-lg bg-dark border-0 text-white">
                            <div class="card-body">
                                <div class="fs-1 fw-bold" id="seconds">4</div>
                                <div class="fs-4">Segundos</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-12 col-xl-8 text-center text-white mb-3">
                        <div class="fw-bold fs-1 mb-3 element-followed">El 31 de octubre Cúcuta y el mundo temblarán</div>
                        <div class="mb-3"> <button @click="onlineAccess(user.referral.phone)" class="btn w-100 text-uppercase btn-danger shadow-lg px-4 rounded-pill fs-4">¡Quiero mi acceso ONLINE!</button> </div>
                        <div> <button @click="presentialAccess(user.referral.phone)" class="btn w-100 text-uppercase btn-danger text-white shadow-lg px-4 rounded-pill fs-4">¡Vivo en Cucúta y Quiero mi entrada!</button> </div>
                    </div>
                </div>
                <div class="row text-center py-3 text-white">
                    <div class="col-12">Powered by Zuum 2022 © All rights reserved</div>
                </div>
            </div>
        </div>
    `,
}

export { Landing4Viewer } 