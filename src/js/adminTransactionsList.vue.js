import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.5'

/* vue */

Vue.createApp({
    components: {
    },
    data() {
        return {
            UserSupport: null,
            transactionsAux: {},
            filters: [
                {
                    name: 'Validadas',
                    status: 2
                },
                {
                    name: 'Todas',
                    status: -2
                },
                {
                    name: 'Expiradas',
                    status: 0
                },
                {
                    name: 'Eliminadas',
                    status: -1
                },
                {
                    name: 'Pendientes',
                    status: 1
                }
            ],
            query: null,
            status: 1,
            transactions: {},
            columns: { // 0 DESC , 1 ASC 
                transaction_requirement_per_user_id: {
                    name: 'transaction_requirement_per_user_id',
                    desc: false
                },
                user_support_id: {
                    name: 'user_support_id',
                    desc: false
                },
                names: {
                    name: 'names',
                    desc: false,
                    alphabetically: true
                },
                ammount: {
                    name: 'ammount',
                    desc: false
                },
                create_date: {
                    name: 'create_date',
                    desc: false
                },
                status: {
                    name: 'status',
                    desc: false,
                    alphabetically: true
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
        },
        status:
        {
            handler() {
                this.getTransactionsList()
            },
            deep: true
        }
    },
    methods: {
        filterData: function () {
            if (this.transactionsAux.length > 0) 
            {
                this.transactions = this.transactionsAux

                this.transactions = this.transactions.filter((transaction) => {
                    return transaction.names.toLowerCase().includes(this.query.toLowerCase()) || transaction.email.toLowerCase().includes(this.query.toLowerCase()) || transaction.user_login_id.toString().includes(this.query.toLowerCase()) || transaction.ammount.toString().includes(this.query)
                })
            }
        },
        sortData: function (column) {
            this.administrators.sort((a, b) => {
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
        viewDeposit: function (transaction) {
            const { txn_id } = JSON.parse(transaction.checkout_data)

            this.UserSupport.viewDeposit({ txn_id: txn_id }, (response) => {
                if (response.s == 1) {
                    transaction.apiResponse = response.apiResponse
                }
            });
        },
        deleteDeposit: function (transaction_requirement_per_user_id) {
            iam.com.mx = alertCtrl.create({
                title: "Aviso",
                subTitle: "¿Estás seguro de eliminar éste depósito?",
                buttons: [
                    {
                        text: "Sí, eliminar",
                        role: "cancel",
                        class: 'btn-danger',
                        handler: (data) => {
                            this.UserSupport.deleteDeposit({ transaction_requirement_per_user_id: transaction_requirement_per_user_id }, (response) => {
                                if (response.s == 1) {
                                    this.getTransactionsList()
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
        reactiveDeposit: function (transaction_requirement_per_user_id) {
            iam.com.mx = alertCtrl.create({
                title: "Aviso",
                subTitle: "¿Estás seguro de reactivar éste depósito?",
                buttons: [
                    {
                        text: "Sí, reactivar",
                        role: "cancel",
                        handler: (data) => {
                            this.UserSupport.reactiveDeposit({ transaction_requirement_per_user_id: transaction_requirement_per_user_id }, (response) => {
                                if (response.s == 1) {
                                    this.getTransactionsList()
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
        applyDeposit: function (transaction_requirement_per_user_id) {
            let alert = alertCtrl.create({
                title: "Aviso",
                subTitle: "¿Estás seguro de aplicar éste depósito?",
                buttons: [
                    {
                        text: "Sí, fondear",
                        class: 'btn-success',
                        role: "cancel",
                        handler: (data) => {
                            this.UserSupport.applyDeposit({ transaction_requirement_per_user_id: transaction_requirement_per_user_id }, (response) => {
                                if (response.s == 1) {
                                    this.getTransactionsList()
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
        getTransactionsList: function () {
            this.transactions = {}
            this.transactionsAux = {}
            this.UserSupport.getTransactionsList({ status: this.status }, (response) => {
                if (response.s == 1) {
                    this.transactionsAux = response.transactions
                    this.transactions = this.transactionsAux
                }
            })
        },
    },
    mounted() {
        this.UserSupport = new UserSupport
        this.getTransactionsList()
    },
}).mount('#app')