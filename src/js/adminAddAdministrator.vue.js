import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.6'

/* vue */

Vue.createApp({
    data() {
        return {
            UserSupport: new UserSupport,
            administratorComplete: false,
            feedback: null,
            administrator: {
                names: null,
                password: null,
                email: null,
                permissions: {},
            },
        }
    },
    watch: {
        administrator:
        {
            handler() {
                this.administratorComplete = this.administrator.names != null && this.administrator.email != null && this.administrator.password != null
            },
            deep: true
        }
    },
    methods: {
        saveAdministrator() {
            this.feedback = null
            this.UserSupport.saveAdministrator({administrator:this.administrator}, (response) => {
                if (response.s == 1) {
                    this.$refs.button.innerText = "Guardado"
                } else if (response.r == 'MAIL_ALREADY_EXISTS') {
                    this.feedback = 'El correo proporcionado ya está registrado'
                }
            })
        },
        getAdministratorPermissions () {
            this.UserSupport.getAdministratorPermissions({user:this.user}, (response) => {
                if (response.s == 1) {
                    Object.assign(this.administrator.permissions, response.permissions)
                }
            })
        },
    },
    mounted() {
        this.getAdministratorPermissions();
    },
}).mount('#app')