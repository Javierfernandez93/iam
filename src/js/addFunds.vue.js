import { User } from '../../src/js/user.module.js?v=2.6.4'   

Vue.createApp({
    components: {

    },
    data() {
        return {
            User: new User,
            feedback: null,
            transaction: {
                catalog_currency_id: 1,
                ammount: null,
            },
            checkoutData: false,
            lastTransactions: [],
            catalogCurrencies: [],
            loading : false,
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
        createTransactionRequirement: function () {
            this.loading = true
            this.User.createTransactionRequirement(this.transaction, (response) => {
                this.loading = false
                if (response.s == 1) {
                    this.transaction.code = response.code
                    this.checkoutData = response.checkoutData
                    this.getLastTransactionsRequirement()
                }

                resolve()
            })
        },
        getLastTransactionsRequirement: function () {
            this.User.getLastTransactionsRequirement({}, (response) => {
                if (response.s == 1) {
                    this.lastTransactions = response.lastTransactions
                }
            })
        },
        getCurrencies: function () {
            return new Promise((resolve) => {
                this.User.getCurrencies({}, (response) => {
                    if (response.s == 1) {
                        this.catalogCurrencies = response.catalogCurrencies
                    }

                    resolve()
                })
            })
        },
    },
    mounted() {
        this.getCurrencies().then(() => {
            this.getLastTransactionsRequirement()
        })
    },
}).mount('#app')