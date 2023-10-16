import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.5'

const AdmintransactionsViewer = {
    name : 'admintransactions-viewer',
    props : [],
    emits : [],
    data() {
        return {
            UserSupport : new UserSupport,
            transactions : {},
            filters: [
                {
                    name: 'Transferidas',
                    status: 2
                },
                {
                    name: 'Pendientes',
                    status: 1
                }
            ],
            status: 1,
            query : null,
            columns: { // 0 DESC , 1 ASC 
                withdraw_per_user_id : {
                    name: 'withdraw_per_user_id',
                    desc: false,
                },
                user_support_id : {
                    name: 'user_support_id',
                    desc: false,
                },
                names : {
                    name: 'names',
                    desc: false,
                    alphabetically: true,
                },
                ammount : {
                    name: 'ammount',
                    desc: false,
                },
                method : {
                    name: 'method',
                    desc: false,
                },
                account : {
                    name: 'account',
                    desc: false,
                },
                create_date : {
                    name: 'create_date',
                    desc: false,
                },
            }
        }
    },
    watch : {
        status: {
            handler() {
                this.getUsersTransactions()
            },
            deep: true
        }
    },
    methods: {
        sortData: function (column) {
            this.administrators.sort((a,b) => {
                const _a = column.desc ? a : b
                const _b = column.desc ? b : a

                if(column.alphabetically)
                {
                    return _a[column.name].localeCompare(_b[column.name])
                } else {
                    return _a[column.name] - _b[column.name]
                }
            });

            column.desc = !column.desc
        },
        applyWithdraw : function(transaction) {
            this.UserSupport.applyWithdraw({withdraw_per_user_id: transaction.withdraw_per_user_id},(response)=>{
                if(response.s == 1)
                {
                    transaction.status = response.status
                }
            });
        },
        deleteWithdraw : function(transaction) {
            this.UserSupport.deleteWithdraw({withdraw_per_user_id:transaction.withdraw_per_user_id},(response)=>{
                if(response.s == 1)
                {
                    transaction.status = response.status
                }
            })
        },
        getUsersTransactions : function() {
            this.UserSupport.getUsersTransactions({status:this.status},(response)=>{
                if(response.s == 1)
                {
                    this.transactions = response.transactions
                }
            })
        },
    },
    mounted() 
    {
        this.getUsersTransactions()
    },
    template: `
        <div class="card mb-4">
            <div class="card-header pb-0">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <i class="bi bi-pie-chart-fill"></i>
                    </div>
                    <div class="col fw-semibold text-dark">
                        <div class="small">Transacciones</div>
                    </div>
                    <div class="col-auto text-end">
                        <div><span class="badge bg-warning">Total de transacciones {{Object.keys(transactions).length}}</span></div>
                    </div>
                </div>
            </div>
            <div class="card-header pb-0">
                <div class="row">
                    <div class="col">
                        <input :autofocus="true" v-model="query" type="text" class="form-control" placeholder="Buscar..." />
                    </div>
                    <div class="col-auto">
                        <select class="form-control" v-model="status">
                            <option v-for="filter in filters" v-bind:value="filter.status">{{filter.name}}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div v-if="Object.keys(transactions).length > 0" class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th @click="sortData(columns.withdraw_per_user_id)" class="text-center c-pointer text-uppercase text-secondary font-weight-bolder opacity-7">
                                    <span v-if="columns.withdraw_per_user_id.desc">
                                        <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                    </span>
                                    <span v-else>
                                        <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                    </span>
                                    <u class="text-sm ms-2">#</u>
                                </th>
                                <th @click="sortData(columns.user_support_id)" class="text-center c-pointer text-uppercase text-secondary font-weight-bolder opacity-7">
                                    <span v-if="columns.user_support_id.desc">
                                        <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                    </span>
                                    <span v-else>
                                        <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                    </span>
                                    <u class="text-sm ms-2">ID</u>
                                </th>
                                <th @click="sortData(columns.names)" class="text-start c-pointer text-uppercase text-secondary font-weight-bolder opacity-7">
                                    <span v-if="columns.names.desc">
                                        <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                    </span>
                                    <span v-else>
                                        <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                    </span>
                                    <u class="text-sm ms-2">Usuario</u>
                                </th>
                                <th @click="sortData(columns.ammount)" class="text-center c-pointer text-uppercase text-secondary font-weight-bolder opacity-7">
                                    <span v-if="columns.ammount.desc">
                                        <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                    </span>
                                    <span v-else>
                                        <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                    </span>
                                    <u class="text-sm ms-2">Monto retirado</u>
                                </th>
                                <th @click="sortData(columns.method)" class="text-center c-pointer text-uppercase text-secondary font-weight-bolder opacity-7">
                                    <span v-if="columns.method.desc">
                                        <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                    </span>
                                    <span v-else>
                                        <i class="bi text-primary bi-arrow-down-square-filssl"></i>
                                    </span>
                                    <u class="text-sm ms-2">Método</u>
                                </th>
                                <th @click="sortData(columns.account)" class="text-center c-pointer text-uppercase text-secondary font-weight-bolder opacity-7">
                                    <span v-if="columns.account.desc">
                                        <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                    </span>
                                    <span v-else>
                                        <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                    </span>
                                    <u class="text-sm ms-2">Cuenta</u>
                                </th>

                                <th @click="sortData(columns.create_date)" class="text-center c-pointer text-uppercase text-secondary font-weight-bolder opacity-7">
                                    <span v-if="columns.create_date.desc">
                                        <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                    </span>
                                    <span v-else>
                                        <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                    </span>
                                    <u class="text-sm ms-2">Fecha de sol.</u>
                                </th>

                                <th @click="sortData(columns.create_date)" class="text-center c-pointer text-uppercase text-secondary font-weight-bolder opacity-7">
                                    <span v-if="columns.create_date.desc">
                                        <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                    </span>
                                    <span v-else>
                                        <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                    </span>
                                    <u class="text-sm ms-2">Estatus</u>
                                </th>

                                <th v-if="status == 1" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="transaction in transactions">
                                <td class="align-middle text-center text-sm">
                                    {{transaction.withdraw_per_user_id}}
                                </td>
                                <td class="align-middle text-center text-sm">
                                    {{transaction.user_login_id}}
                                </td>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">{{transaction.names}}</h6>
                                            <p class="text-xs text-secondary mb-0">{{transaction.email}}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle text-center">
                                    <p class="fw-semibold text-dark">$ {{transaction.amount.numberFormat(2)}}</p>
                                </td>
                                <td class="align-middle text-center">
                                    <span class="badge border border-primary text-primary">
                                        {{transaction.method}} - 
                                        {{transaction.currency}}
                                    </span>
                                </td>
                                <td class="align-middle text-center">
                                    <p class="fw-semibold text-dark">{{transaction.wallet}}</p>
                                </td>
                                <td class="align-middle text-center">
                                    <p class="mb-0">{{transaction.create_date.formatDate()}}</p>
                                </td>
                                <td class="align-middle text-center">
                                    <span v-if="transaction.status == 1" class="badge border border-warning text-warning">Pendiente</span>
                                    <span v-else-if="transaction.status == 2" class="badge border border-success text-success">Transferida</span>
                                    <span v-else-if="transaction.status == -1" class="badge border border-danger text-danger">Eliminada</span>
                                </td>
                                <td
                                    v-if="status == 1"
                                    class="align-middle text-center text-sm">
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        
                                        </button>
                                        <ul class="dropdown-menu shadow">
                                            <li><button class="dropdown-item" @click="applyWithdraw(transaction)">Aplicada</button></li>
                                        
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li><button class="dropdown-item" @click="deleteWithdraw(transaction)">Eliminar</button></li>
                                        </ul>
                                    </div>
                                </td>

                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="card-body">
                    <div class="alert alert-light fw-semibold text-center">
                        <strong>Aviso</strong>
                        <div>No tenemos transacciones aún</div>
                    </div>
                </div>
            </div>
        </div>
    `
}

export { AdmintransactionsViewer } 