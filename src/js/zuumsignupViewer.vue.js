import { User } from '../../src/js/user.module.js?v=2.6.4'   

const ZuumsignupViewer = {
    name : 'zuumsignup-viewer',
    data() {
        return {
            User: new User,
            status : null,
            STATUS : {
                SIGNUP_CHECKER: {
                    html: '<div class="text-center"><span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span> Validando información</div>'
                },
                USER_EXIST: {
                    html: '<div class="text-center"><span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span> Cuenta creada en Zuum</div>'
                },
                REDIRECTING_TO_TOOL: {
                    html: '<div class="text-center"><span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span> Redirijiendo a herramienta</div>'
                },
                SIGNUP_EXTERNAL: {
                    html: '<div class="text-center"><span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span> Creando cuenta en mizuum.com</div>'
                },
                SIGNUP_EXTSIGNUP_SUCCESSERNAL: {
                    html: '<div class="text-center"><div><i class="bi fs-2 text-success bi-check-circle"></i></div>> Cuenta creada con éxito</div>'
                },
                SUCCESS: {
                    html: '<div class="text-center"><div><i class="bi fs-2 text-success bi-check-circle"></i></div> Herramienta inicializada</div>'
                }
            },
        }
    },
    methods: {
        signupChecker()
        {
            this.status = this.STATUS.SIGNUP_CHECKER

            setTimeout(()=>{
                this.User.signupChecker({},(response)=>{
                    if(response.s == 1)
                    {
                        this.status = this.STATUS.USER_EXIST
                    } else {
                        this.signupExternal().then(()=>{
                            this.makeApiCredentials()
                        })
                    }
                })
            },800)
        },
        makeApiCredentials()
        {
            this.User.makeApiCredentials({},(response)=>{
                this.status = this.STATUS.SIGNUP_SUCCESS

                if(response.s == 1)
                {
                    resolve()
                } 
            })
        },
        signupExternal()
        {
            return new Promise((resolve)=>{
                this.status = this.STATUS.SIGNUP_EXTERNAL

                setTimeout(()=>{
                    this.User.signupExternal({},(response)=>{
                        this.status = this.STATUS.SIGNUP_SUCCESS

                        if(response.s == 1)
                        {
                            resolve()
                        } 
                    })
                },800)
            })
        },
    },
    mounted() {
        this.signupChecker()
    },
    template : `
        <div class="d-none" v-html="status.html"></div>
    `,
}

export { ZuumsignupViewer } 