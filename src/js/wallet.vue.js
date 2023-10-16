import { User } from '../../src/js/user.module.js?v=2.6.6'   

Vue.createApp({
    components: {

    },
    data() {
        return {
            User: null,
            feedback: null,
            withdrawComplete: false,
            user: {
                names: null,
            },
            all_withdraw_methods: {},
            withdraw_methods: {},
            withdraws: {},
            balance: 0,
            withdraw: {
                catalog_withdraw_method_id: 0,
                ammount: null,
            }
        }
    },
    watch: {
        withdraw: {
            handler() {
                this.withdrawComplete = this.withdraw.catalog_withdraw_method_id != null && (this.withdraw.ammount > 0 && this.withdraw.ammount <= this.balance)
            },
            deep: true,
        },
        user: {
            handler() {

            },
            deep: true
        },
    },
    methods: {
        doWithdraw: function () {
            this.feedback = false

            this.User.doWithdraw(this.withdraw, (response) => {
                if (response.s == 1) {
                    this.feedback = 'Retiro recibido, lo procesaremos en 7 días a partir de hoy'
                    this.getBalance().then(() => {
                        this.getWithdraws()
                    })
                }
            })
        },
        openVideo: function () {
            alertHtml(`<div style="position: relative; padding-bottom: 62.5%; height: 0;"><iframe src="https://www.loom.com/embed/3629c9fae3a843e7a356f000090b7ae5" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></iframe></div>`,'Retiros','modal-lg')
        },
        getWithdraws: function () {
            this.User.getWithdraws({}, (response) => {
                if (response.s == 1) {
                    this.withdraws = response.withdraws
                }
            })
        },
        editWithdrawMethod: function (all_withdraw_method) {
            this.User.editWithdrawMethod(all_withdraw_method, (response) => {
                if (response.s == 1) {
                    this.toggleEditing(all_withdraw_method)
                    
                    this.getBalance().then(() => {
                        this.getProfile()
                        this.getWithdraws()
                    })
                }
            })
        },
        toggleEditing: function (all_withdraw_method) {
            all_withdraw_method.editing = !all_withdraw_method.editing
        },
        getProfile: function () {
            this.User.getProfile({ include_witdraw_methods: true }, (response) => {
                if (response.s == 1) {
                    Object.assign(this.user, response.user)
                    this.all_withdraw_methods = response.withdraw_methods
                    this.withdraw_methods = this.all_withdraw_methods.filter((withdraw_method) => {
                        return withdraw_method.account
                    })
                }
            })
        },
        getBalance: function () {
            return new Promise((resolve) => {
                this.User.getBalance({}, (response) => {
                    if (response.s == 1) {
                        this.balance = response.balance
                    }

                    resolve()
                })
            })
        },
    },
    mounted() {
        this.User = new User

        this.getBalance().then(() => {
            this.getProfile()
            this.getWithdraws()
        })
    },
}).mount('#app')