import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.6'

/* vue */

Vue.createApp({
    components: {
    },
    data() {
        return {
            administratorComplete: false,
            UserSupport: null,
            feedback: null,
            administrator: {
                name: null,
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
                this.administratorComplete = this.administrator.name != null && this.administrator.email != null && this.administrator.password != null
            },
            deep: true
        }
    },
    methods: {
        editAdministrator: function () {
            this.feedback = null
            this.UserSupport.editAdministrator({administrator:this.administrator}, (response) => {
                if (response.s == 1) {
                    this.$refs.button.innerText = "Actualizado"
                } else if (response.r == 'MAIL_ALREADY_EXISTS') {
                    this.feedback = 'El correo proporcionado ya está registrado'
                }
            })
        },
        getAdministrator: function (user_support_id) {
            this.UserSupport.getAdministrator({user_support_id:user_support_id}, (response) => {
                if (response.s == 1) {
                    Object.assign(this.administrator, response.administrator)
                }
            })
        },
    },
    mounted() {
        this.UserSupport = new UserSupport

        if(getParam('usid'))
        {
            this.getAdministrator(getParam('usid'));
        }
    },
}).mount('#app')