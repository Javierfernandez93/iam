import { UserSupport } from './userSupport.module.js?t=9'   

const ReportViewer = {
    name : 'report-viewer',
    data() {
        return {
            UserSupport: new UserSupport,
            deposits: null,
            totals: {
                percentaje: 0,
                ammount: null,
                gains: null,
            },
            dates: {
                start_date: null,
                end_date: null
            },
            columns: { // 0 DESC , 1 ASC 
                create_date: {
                    name: 'create_date',
                    desc: false,
                },
                profit: {
                    name: 'profit',
                    desc: true,
                },
                name: {
                    name: 'name',
                    desc: true,
                    alphabetically: true,
                },
            }
        }
    },
    watch: {
    },
    methods: {
        sortData: function (column) {
            this.profits.sort((a, b) => {
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
        calculateTotals: function () {
            if(this.profits.length > 0)
            {
                this.profits.map((profit) => {
                    this.totals.total_gain += profit.profit ? parseFloat(profit.profit) : 0;
                })
            }
        },
        initTotals: function () {
            this.totals = {
                percentaje: 0,
                ammount: 0,
                gains: 0, 
            }
        },
        getTotals: function () {
            this.initTotals()

            this.deposits.map((deposit)=>{
                this.totals.gains += deposit.gains
                this.totals.percentaje += deposit.percentaje != Infinity ? deposit.percentaje : 0
            })
        },
        formatDeposits: function () {
            this.deposits.map((deposit)=>{
                deposit.percentaje = deposit.gains ? (100*deposit.gains) / deposit.ammount : 0
                
                return deposit
            })
        },
        getReport: function () {
            this.UserSupport.getReport({start_date:this.dates.start_date,end_date:this.dates.end_date}, (response) => {
                if (response.s == 1) {
                    this.deposits = response.deposits

                    this.formatDeposits()
                    this.getTotals()
                }
            })
        },
    },
    mounted() 
    {   
        this.getReport()
    },
    template : `
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-12 col-xl">
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon1">Fecha inicio</span>

                            <input 
                                v-model="dates.start_date"
                                type="date" class="form-control">
                        </div>                                
                    </div>                                
                    <div class="col-12 col-xl">
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon1">Fecha fin</span>
                            <input 
                                v-model="dates.end_date"
                                type="date" class="form-control">

                        </div>
                    </div>
                    <div class="col-12 col-xl-auto">
                        <button 
                            @click="getReport"
                            class="btn btn-primary">Tomar datos</button>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="deposits" 
            class="card">
            <table class="table align-items-center mb-0">
                <thead>
                    <tr class="align-items-center">
                        <th>Día</th>
                        <th class="text-center">Depósitos de usuarios (Inversiones)</th>
                        <th class="text-center">Ganancias usuarios</th>
                        <th class="text-center">Dias del mes</th>
                        <th class="text-center">Porcentaje</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="deposit in deposits">
                        <td>
                            <span class="badge p-0 text-dark">{{deposit.date}}</span>
                            <div>{{deposit.date_unix.formatDateText()}}</div>
                        </td>
                        <td class="text-center text-primary">
                            <span v-if="deposit.ammount">
                                $ {{deposit.ammount.numberFormat(2)}}
                            </span>
                        </td>
                        <td class="text-center text-danger">
                            <span v-if="deposit.gains">
                                $ {{deposit.gains.numberFormat(2)}}
                            </span>
                        </td>
                        <td class="text-center">
                            {{deposit.days}}
                        </td>
                        <td class="text-center text-success">
                            {{deposit.percentaje.numberFormat(2)}} %
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr v-if="totals">
                        <td class="border-0 fw-semibold text-dark">
                            TOTALES
                        </td>
                        <td class="text-center border-0">
                            
                        </td>
                        <td class="text-center border-0 text-danger">
                            <span v-if="totals.gains">
                                $ {{totals.gains.numberFormat(2)}}
                            </span>
                        </td>
                        <td class="text-center border-0">
                            
                        </td>
                        <td class="text-center border-0 text-success">
                            <u>{{totals.percentaje.numberFormat(2)}} %</u>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    `,
}

export { ReportViewer } 