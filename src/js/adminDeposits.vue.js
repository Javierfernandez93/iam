import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.5'

/* vue */

Vue.createApp({
    components: {
    },
    data() {
        return {
            UserSupport: null,
            transactionsAux: {},
            transactions: {},
            query: null,
            columns: { // 0 DESC , 1 ASC 
                transaction_per_wallet_id: {
                    name: 'transaction_per_wallet_id',
                    desc: false,
                },
                create_date: {
                    name: 'create_date',
                    desc: false,
                },
                ammount: {
                    name: 'ammount',
                    desc: false,
                },
            }
        }
    },
    watch: {
        query:
        {
            handler() {
                this.filterData()
            },
            deep: true
        }
    },
    methods: {
        sortData: function (column) {
            this.transactions.sort((a, b) => {
                const _a = column.desc ? a : b
                const _b = column.desc ? b : a

                if (column.alphabetically) {
                    return _a[column.name].localeCompare(_b[column.name])
                } else {
                    return _a[column.name] - _b[column.name]
                }
            });

            column.desc = !column.desc
        },
        init: function () {
            this.transactionsAux = {};
            this.transactions = {};
        },
        filterData: function () {
            this.transactions = this.transactionsAux

            this.transactions = this.transactions.filter((transaction) => {
                return transaction.transaction_per_wallet_id.toString().includes(this.query) || transaction.ammount.toString().includes(this.query) || transaction.create_date.formatDate().includes(this.query) 
            })
        },
        deleteTransaction: function (transaction_per_wallet_id) {
            iam.com.mx = alertCtrl.create({
                title: "Aviso",
                subTitle: "¿Estás seguro de eliminar éste fondeo?. Se recalculará el plan del usuario",
                buttons: [
                    {
                        text: "Sí, eliminar",
                        role: "cancel",
                        class: 'btn-danger',
                        handler: (data) => {
                            this.UserSupport.deleteTransaction({ transaction_per_wallet_id: transaction_per_wallet_id }, (response) => {
                                if (response.s == 1) {
                                    this.getDeposits(getParam("ulid"))
                                }
                            });
                        },
                    },
                    {
                        text: "Cancelar",
                        role: "cancel",
                        handler: (data) => {
                        },
                    },
                ],
            })

            alertCtrl.present(alert.modal); 
        },
        getDeposits: function (user_login_id) {
            this.init()

            this.UserSupport.getDeposits({user_login_id:user_login_id}, (response) => {
                if (response.s == 1) {
                    this.transactionsAux = response.transactions
                    this.transactions = this.transactionsAux
                }
            })
        },
    },
    mounted() {
        this.UserSupport = new UserSupport

        if(getParam("ulid"))
        {
            this.getDeposits(getParam("ulid"))
        }
    },
}).mount('#app')