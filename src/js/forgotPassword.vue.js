import { User } from '../../src/js/user.module.js?v=2.6.6'   

Vue.createApp({
    components : { 
        
    },
    data() {
        return {
            mailSent: false,
            // email: 'javier@gmail.com',
            loading: false,
            email: null,
            User : null,
            feedback : false,
            isValidMail : false
        }
    },
    watch : {
        email : {
            handler() {
                this.checkEmail()
            },
            deep: true
        },
    },
    methods: {
        recoverPassword : function() {
            if(this.isValidMail)
            {
                this.feedback = false
                this.loading = true
                
                this.User.recoverPassword({email:this.email},(response)=>{
                    this.loading = false
                    if(response.s == 1)
                    {
                        this.mailSent = true
                    } else if(response.r == "INVALID_PASSWORD") {
                        this.feedback = "Las contraseña indicada no es correcta. Intente nuevamente"
                    } else if(response.r == "NOT_FOUND_MAIL") {
                        this.feedback = "El correo proporcionado no está registrado"
                    } else if(response.r == "INVALID_CREDENTIALS") {
                        this.feedback = "Las credenciales proporcionadas no son correctas, intente nuevamente"
                    }
                })
            }
        },
        checkEmail : function() {
            this.isValidMail = isValidMail(this.email)
        },
    },
    mounted() 
    {
        this.User = new User
    },
}).mount('#app')