import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.6'

/* vue */
Vue.createApp({
    data() {
        return {
            userComplete: false,
            UserSupport: new UserSupport,
            user: {
                names: null,
                signup_date: null,
                referral: {
                    names: null,
                    commission: 30,
                    user_login_id: null
                },
                password: null,
                email: null,
                phone: null,
            },
        }
    },
    watch: {
        'user.referral.user_login_id': {
            handler() {
                this.getReferral(this.user.referral.user_login_id,this.user.user_login_id)
            },
            deep: true
        },
        user: {
            handler() {
                this.userComplete = this.user.names != null && this.user.email != null

            },
            deep: true
        }
    },
    methods: {
        updateUser() {
            this.UserSupport.updateUser({ user: this.user }, (response) => {
                if (response.s == 1) {
                    this.$refs.button.innerText = "Actualizado"
                }
            })
        },
        getReferral(referral_user_login_id,user_login_id) {
            this.UserSupport.getReferral({referral_user_login_id:referral_user_login_id,user_login_id:user_login_id},(response)=>{
                if(response.s == 1)
                {
                    this.user.referral.names = response.referral.names
                    this.user.referral.commission = response.commission
                }
            })
        },
        getUser(user_login_id) {
            return new Promise( (resolve) => {
                this.UserSupport.getUser({ user_login_id: user_login_id }, (response) => {
                    if (response.s == 1) {
                        this.user = {...this.user,...response.user}

                        this.user.referral.user_login_id = response.user_referral_id
                    }

                    resolve(response.user_referral_id)
                })
            })
        },
    },
    mounted() {
        $(this.$refs.phone).mask('(00) 0000-0000');

        if (getParam('ulid')) {
            this.getUser(getParam('ulid')).then((user_login_id) => {
            })
        }
    },
}).mount('#app')