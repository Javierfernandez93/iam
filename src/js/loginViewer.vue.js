import { User } from './user.module.js?t=4'

const LoginViewer = {
    name: 'login-viewer',
    data() {
        return {
            User : new User,
            user: {
                email: '',
                rememberMe: true,
                password: null,
            },
            redirection: {
                page: null,
                route_name: null
            },
            feedback : false,
            isValidMail : false,
            fieldPasswordType : 'password',
            userComplete : false,
        }
    },
    watch : {
        user : {
            handler() {
                this.checkFields()
                this.checkEmail()
            },
            deep: true
        },
    },
    methods: {
        toggleFieldPasswordType : function() {
            this.fieldPasswordType = this.fieldPasswordType == 'password' ? 'text' : 'password'
        },
        doLogin : function() {
            this.feedback = false

            // dinamicLoader.showLoader($("#button"))
            
            this.User.doLogin(this.user,(response)=>{
                if(response.s == 1)
                {
                    if(this.redirection.page)
                    {
                        window.location.href = this.redirection.page
                    } else {
                        window.location.href = '../../apps/backoffice'
                    }
                } else if(response.r == "INVALID_PASSWORD") {
                    this.feedback = "Las contraseña indicada no es correcta. Intente nuevamente"
                } else if(response.r == "INVALID_CREDENTIALS") {
                    this.feedback = "Las credenciales proporcionadas no son correctas, intente nuevamente"
                }
            })
        },
        checkEmail : function() {
            this.isValidMail = isValidMail(this.user.email)
        },
        checkFields : function() {
            this.userComplete = this.user.email && this.user.password
        }
    },
    mounted() {
        if(getParam('page'))
        {
            this.redirection.page = getParam('page')
            this.redirection.route_name = getParam('route_name')
        }
    },
    template: `
        <div v-if="redirection.page" class="card animation-fall-down mb-3" style="--delay:600ms">
            <div class="card-body">
                Ingresa a tu cuenta para continuar a: 
                <div><b>{{ redirection.route_name }}</b></div>
                <div><b>{{ redirection.page }}</b></div>
            </div>
        </div>

        <div class="card p-3 text-start cards-plain shadow-none bg-white animation-fall-down mb-3" style="--delay:920ms">
            <div class="card-header pb-0 bg-transparent">
                <h3 class="font-weight-bolder text-dark text-gradient">Bienvenido de nuevo</h3>
            </div>

            <div class="card-body">
                <form role="form">
                    <label class="text-sm" for="email">Correo electrónico</label>
                    <div class="mb-3">
                        <input 
                            :autofocus="true"
                            :class="isValidMail ? 'is-valid' : ''"
                            @keydown.enter.exact.prevent="$refs.password.focus()"
                            type="email" id="email" ref="email" v-model="user.email" class="form-control" placeholder="Corrreo electrónico" aria-label="Corrreo electrónico" aria-describedby="email-addon">
                    </div>
                    <label class="text-sm" for="password">Contraseña</label>
                    <div class="input-group mb-3">
                        <input 
                            :type="fieldPasswordType"
                            :class="user.password ? 'is-valid' : ''"
                            @keydown.enter.exact.prevent="doLogin"
                            style="height:41px"
                            type="password" ref="password" id="password" v-model="user.password" class="form-control" placeholder="Contraseña" aria-label="Contraseña" aria-describedby="password-addon">
                        <button class="btn btn-primary shadow-none" type="button" id="button-addon2" @click="toggleFieldPasswordType">
                            <i v-if="fieldPasswordType == 'password'" class="bi bi-eye"></i>
                            <i v-else class="bi bi-eye-slash"></i>
                        </button>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" v-model="user.rememberMe" id="rememberMe">
                                <label class="form-check-label" for="rememberMe">Recordarme</label>
                            </div>
                        </div>
                        <div class="col-auto text-end">
                            <a class="small" href="../../apps/login/forgotPassword">¿Olvidaste tu contraseña?</a>
                        </div>
                    </div>

                    <div v-show="feedback" class="alert alert-light shadow fw-semibold border-0 alert-dismissible fade show" role="alert">
                        <strong>Aviso</strong>
                        {{ feedback }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <div class="mt-3">
                        <button :disabled="!userComplete"  @click="doLogin" type="button" class="btn btn-primary shadow-none btn-lg">Ingresar a mi cuenta</button>
                    </div>
                </form>
            </div>
            <div class="card-footer pt-0 px-lg-2 px-1">
                <p class="mb-4 text-sm mx-auto">
                    ¿No tienes una cuenta?
                    <a href="../../apps/signup" class="text-info text-primary font-weight-bold">Regístrate aquí</a>
                </p>
            </div>
        </div>
    `
}

export { LoginViewer }