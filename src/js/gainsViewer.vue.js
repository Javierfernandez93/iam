import { User } from '../../src/js/user.module.js?v=2.6.6'   

const GainsViewer = {
    name : 'gains-viewer',
    data() {
        return {
            User: new User,
            gains: null,
            totals: {
                amount: 0
            },
            columns: { // 0 DESC , 1 ASC 
                create_date: {
                    name: 'create_date',
                    desc: false,
                },
                gain: {
                    name: 'gain',
                    desc: true,
                },
                week: {
                    name: 'week',
                    desc: true,
                    alphabetically: false,
                },
            },
            STATUS: {
                PENDING: 0,
                DEPOSITED: 1,
            },
        }
    },
    methods: {
        sortData(column) {
            this.gains.sort((a, b) => {
                const _a = column.desc ? a : b
                const _b = column.desc ? b : a

                return column.alphabetically ? _a[column.name].localeCompare(_b[column.name]) : _a[column.name] - _b[column.name]
            });

            column.desc = !column.desc
        },
        calculateTotals() {
            if(this.gains.length > 0)
            {
                this.gains.map((gain) => {
                    this.totals.amount += gain.amount ? parseFloat(gain.amount) : 0;
                })
            }
        },
        getGains() {
            return new Promise((resolve,reject) => {
                this.User.getGains({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.gains)   
                    }
                    reject()
                })
            })
        },
    },
    mounted() {
        this.getGains().then((gains) => {
            this.gains = gains

            this.calculateTotals()
        }).catch(() => this.gains = false)
    },
    template : `
        <div class="row">
            <div class="col-12">
                <div v-if="gains"
                    class="card mb-4 overflow-hidden border-radius-xl">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col fw-semibold text-dark">Ganancias</div>
                            <div class="col-auto"><span class="badge bg-primary">Total de dispersiones {{gains.length}}</span></div>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr class="text-center">
                                        <th @click="sortData(columns.week)" class="text-center c-pointer text-uppercase text-xxs text-primary font-weight-bolder opacity-7">
                                            <span v-if="columns.week.desc">
                                                <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                            </span>    
                                            <span v-else>    
                                                <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                            </span>    
                                            <u class="text-sm ms-2">Semana</u>
                                        </th>
                                        <th @click="sortData(columns.create_date)" class="text-center c-pointer text-uppercase text-xxs text-primary font-weight-bolder opacity-7">
                                            <span v-if="columns.create_date.desc">
                                                <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                            </span>    
                                            <span v-else>    
                                                <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                            </span>    
                                            <u class="text-sm ms-2">Fecha</u>
                                        </th>
                                        <th @click="sortData(columns.gain)" class="text-center c-pointer text-uppercase text-xxs text-primary font-weight-bolder opacity-7">
                                            <span v-if="columns.gain.desc">
                                                <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                            </span>    
                                            <span v-else>    
                                                <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                            </span>    
                                            <u class="text-sm ms-2">Monto</u>
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Estatus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="gain in gains" class="text-center">
                                        <td>
                                            <p class="text-secondary mb-0">{{gain.week}}</p>
                                        </td>
                                        <td>
                                            <p class="text-secondary mb-0">{{gain.create_date.formatDate()}}</p>
                                        </td>
                                        <td>
                                            <p class="fw-semibold">
                                                $ {{gain.amount.numberFormat(2)}} {{gain.currency}}
                                            </p>
                                        </td>
                                        <td>
                                            <span v-if="gain.status == STATUS.PENDING" class="badge bg-warning">
                                                Pendiente de dispersar
                                            </span>
                                            <span v-if="gain.status == STATUS.DEPOSITED" class="badge bg-success">
                                                Enviada a cartera electrónica 
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="text-center">
                                        <td></td>
                                        <td>Total</td>
                                        <td><p class="fw-semibold">$ {{totals.amount.numberFormat(2)}}</p></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div v-else-if="gains == false">
                    <div class="alert alert-light text-center">
                        <div>No tenemos información sobre tus ganancias.</div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { GainsViewer } 