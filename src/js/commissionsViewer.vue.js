import { User } from '../../src/js/user.module.js?v=2.6.4'   

const CommissionsViewer = {
    name : 'commissions-viewer',
    data() {
        return {
            User: new User,
            commissions: null,
            commissionsAux: null,
            query: null,
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
                PENDING_FOR_DISPERSION: 1,
                COMPLETED: 2,
            },
        }
    },
    watch: {
        query: {
            handler() {
                this.filterData()
            },
            deep: true
        }
    },
    methods: {
        filterData() {
            this.commissions = this.commissionsAux
            this.commissions = this.commissions.filter((commission) => {
                return commission.names.toLowerCase().includes(this.query.toLowerCase())
                    || commission.amount.toString().includes(this.query)
                    || commission.user_login_id_from.toString().includes(this.query)
            })
        },
        sortData(column) {
            this.commissions.sort((a, b) => {
                const _a = column.desc ? a : b
                const _b = column.desc ? b : a

                return column.alphabetically ? _a[column.name].localeCompare(_b[column.name]) : _a[column.name] - _b[column.name]
            });

            column.desc = !column.desc
        },
        calculateTotals() {
            if(this.commissions.length > 0)
            {
                this.commissions.map((gain) => {
                    this.totals.amount += gain.amount ? parseFloat(gain.amount) : 0;
                })
            }
        },
        getCommissions() {
            return new Promise((resolve,reject) => {
                this.User.getCommissions({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.commissions)   
                    }
                    reject()
                })
            })
        },
    },
    mounted() {
        this.getCommissions().then((commissions) => {
            this.commissions = commissions
            this.commissionsAux = commissions

            this.calculateTotals()
        }).catch(() => this.commissions = false)
    },
    template : `
        <div class="card overflow-hidden">
            <div class="card-header">
                <div class="row">
                    <div class="col-12 col-xl">
                        <div v-if="commissions" class="text-secondary text-xs">{{commissions.lenght}}</div>
                        <div class="fs-4 fw-semibold text-primary">Comisiones</div>
                    </div>
                    <div class="col-12 col-xl-auto">
                        <input type="search" v-model="query" class="form-control" placeholder="buscar..."/>
                    </div>
                </div>

                <div v-if="commissions">
                    <table class="table table-striped table-hover">
                        <thead class="text-center">
                            <tr>
                                <th>#</th>
                                <th>ID usuario</th>
                                <th>Usuario</th>
                                <th>Motivo</th>
                                <th>Monto</th>
                                <th>Fecha</th>
                                <th>Estatus</th>
                            </tr>
                        </thead>
                        <tbody class="text-center text-xs">
                            <tr v-for="commission in commissions">
                            <td>{{commission.commission_per_user_id}}</td>
                            <td>{{commission.user_login_id_from}}</td>
                                <td>{{commission.names}}</td>
                                <td>{{commission.name}}</td>
                                <td class="text-dark fw-semibold">$ {{commission.amount.numberFormat(2)}} USD</td>
                                <td>{{commission.create_date.formatFullDate()}}</td>
                                <td>
                                    <span v-if="commission.status == STATUS.PENDING_FOR_DISPERSION" class="text-secondary text-xs">
                                        Pendiente de envio a ewallet
                                    </span>
                                    <span v-else-if="commission.status == STATUS.COMPLETED" class="text-success text-xs">
                                        Dispersada a ewallet
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="text-center text-xs">
                            <tr>
                                <td></td>
                                <td>Total</td>
                                <td>Total</td>
                                <td class="text-dark fw-semibold">$ {{totals.amount.numberFormat(2)}} USD</td>
                                <td>Total</td>
                                <td>Total</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div v-else-if="commissions == false" class="card-body">
                    <div class="alert alert-info text-white text-center mb-0">
                        Aún no generas ganancias por referidos, comienza compartiendo tu landing page
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { CommissionsViewer } 