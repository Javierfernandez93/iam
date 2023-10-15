import { User } from '../../src/js/user.module.js?v=2.6.4'   

Vue.createApp({
    components : { 
        
    },
    data() {
        return {
            feedback : null,
            hasValidPasswords : false,
            paswordReseted : false,
            user: {
                email: null,
                password: null,
                passwordVerificator: null,
            },
            User : null
        }
    },
    watch : {
        user : {
            handler() {
                this.checkHasValidPasswords()
            },
            deep: true
        },
    },
    methods: {
        checkHasValidPasswords : function() {
            let valid = false

            if(this.user.password != undefined && this.user.passwordVerificator != undefined) 
            {
                valid = (this.user.password == this.user.passwordVerificator) && (this.user.password.length > 0 && this.user.passwordVerificator.length > 0)
            }

            this.hasValidPasswords = valid
        },
        changePassword : function() {
            
            this.User.changePassword(Object.assign(this.user,{token:getParam('token')}),(response)=>{
                if(response.s == 1)
                {
                    this.paswordReseted = true
                } else if(response.r == "") {
                    this.feedback = ''
                }
            })
        },
        getAuthToChangePassword : function() {
            this.User.getAuthToChangePassword({token:getParam('token')},(response)=>{
                if(response.s == 1)
                {
                   this.user.email = response.email
                }
            })
        },
    },
    mounted() 
    {
        this.User = new User

        if(getParam('token'))
        {
            this.getAuthToChangePassword()
        }
    },
}).mount('#app')