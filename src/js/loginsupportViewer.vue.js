import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.5'

const LoginsupportViewer = {
    name: 'loginsupport-viewer',
    data() {
        return {
            UserSupport : new UserSupport,
            user: {
                email: null,
                password: null,
                rememberMe: true,
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
                this.userComplete = this.user.email && this.user.password && isValidMail(this.user.email)
            },
            deep: true
        },
    },
    methods: {
        toggleFieldPasswordType() {
            this.fieldPasswordType = this.fieldPasswordType == 'password' ? 'text' : 'password'
        },
        doLogin() {
            this.feedback = false
            
            this.UserSupport.doLoginSupport(this.user,(response)=>{
                if(response.s == 1)
                {
                    window.location.href = '../../apps/admin'
                } else if(response.r == "INVALID_PASSWORD") {
                    this.feedback = "Las contraseña indicada no es correcta. Intente nuevamente"
                } else if(response.r == "INVALID_CREDENTIALS") {
                    this.feedback = "Las credenciales proporcionadas no son correctas, intente nuevamente"
                }
            })
        },
    },
    mounted() 
    {
        
    },
    template: `
        <div class="card shadow-lg border-radius-2xl animation-fall-down" style="--delay:200ms">
            <div class="card-header">
                <div class="row justify-content-center">
                    <div class="col-4 col-xl-6">
                        <img src="../../src/img/logo-dark.svg" alt="logo" class="img-fluid"/>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="form-floating mb-3">
                <input 
                    :autofocus="true"
                    :class="isValidMail ? 'is-valid' : ''"
                    @keydown.enter.exact.prevent="$refs.password.focus()"
                    ref="email"
                    v-model="user.email"
                    type="email" class="form-control" placeholder="name@example.com">
                <label for="email">Correo electrónico</label>
                </div>

                <div class="form-floating mb-3">
                <input 
                    :type="fieldPasswordType"
                    :class="user.password ? 'is-valid' : ''"
                    @keydown.enter.exact.prevent="doLogin"
                    ref="password" 
                    v-model="user.password" 
                    type="password" class="form-control" placeholder="Password">
                <label for="password">Contraseña</label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" v-model="user.rememberMe" type="checkbox" id="rememberMe">
                    <label class="form-check-label" for="rememberMe">Recordarme</label>
                </div>

                <div v-show="feedback" class="alert alert-secondary text-white alert-dismissible fade show" role="alert">
                    {{ feedback }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            
                <button 
                :disabled="!userComplete" 
                @click="doLogin"
                class="btn btn-primary w-100 btn-block btn-lg badge-pill mb-0" type="button">Ingresar iam.com.mx</button>
            </div>
        </div>        
    `
}

export { LoginsupportViewer }