import { UserSupport } from './userSupport.module.js'   

const StatsViewer = {
    name : 'stats-viewer',
    props : [],
    emits : [],
    data() {
        return {
            UserSupport : null,
            performancesAux: {},
            gains: {},
            gainsAux: {},
            viewAll: true,
            totals: {
                total_earn: 0,
                total_profits: 0,
                total_real_gain: 0,
            },
            query: null,
            columns : {
                create_date : {
                    name: 'create_date',
                    desc: false,
                },
                performance : {
                    name: 'performance',
                    desc: false,
                },
            }
        }
    },
    watch : {
        query : 
        {
            handler() {
                this.filterData()
            },
            deep : true
        }
    },
    methods: {
        initChart: function (gains) {
            const ctx = document.getElementById("myChart").getContext("2d");

            let datasets = [];

            /* datas */
            let labels = [];

            let profits = [];
            let earns = [];
            let real_gains = [];

            gains.reverse().map((gain)=>{
                labels.push(gain.unix_day.formatDateTextChart())

                profits.push(gain.total_profit)
                real_gains.push(gain.total_real_gain)
                earns.push(gain.total_earn)
            })
            
            datasets.push({
                label: "Ganancias de usuarios",
                data: profits,
                borderColor: "#7928CA",
                backgroundColor: "#7928CA",
            })
            
            datasets.push({
                label: "Ganancias Brokers",
                data: real_gains,
                borderColor: "#07A07A",
                backgroundColor: "#07A07A",
            })
            
            datasets.push({
                label: "Utilidad",
                data: earns,
                borderColor: "#82d616",
                backgroundColor: "#82d616",
            })

            const data = {
                labels: labels,
                datasets: datasets,
            };

            const config = {
                type: "line",
                data: data,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: "top",
                        },
                        title: {
                            display: false,
                            text: "Gráfica de estadísticas general por mes",
                        },
                    },
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                            },
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: "($) USD",
                            },
                        },
                    },
                },
            };

            const myChart = new Chart(ctx, config);

            gains.reverse()
        },
        filterData : function() {
            this.gains = this.gainsAux
            
            this.gains = this.gainsAux.filter((gain)=>{
                return gain.unix_day.formatDateText().toLowerCase().includes(this.query.toLowerCase())
            })
        },
        getTotals : function() {
            this.gains.map((gain)=>{
                this.totals.total_earn += gain.total_earn 
                this.totals.total_real_gain += gain.total_real_gain 
                this.totals.total_profits += gain.profit_investment + gain.profit_referral_investment 
            })
        },
        showAlert : function(gain) {
            alertMessage(gain)
        },
        toggleViewAll : function() {
            this.viewAll = !this.viewAll
            this.gains.map((gain) => {
                gain.detail = this.viewAll
            })
        },
        toggleView : function(gain) {
            gain.detail = !gain.detail

        },
        getAllGainsByDays : function() {    
            return new Promise((resolve) => {        
                this.UserSupport.getAllGainsByDays({},(response)=>{
                    if(response.s == 1)
                    {
                        this.gainsAux = response.gains.reverse()
                        this.gains = this.gainsAux

                        this.getTotals()

                        resolve(response.gains)
                    }
                })
            })
        },
    },
    updated() {
    },
    mounted() 
    {   
        this.UserSupport = new UserSupport
        this.getAllGainsByDays().then((gains)=>{
            this.initChart(gains)
        })
    },
    template : `
        <div class="row mb-3">
            <div class="col-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="icon icon-floating-top icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                    <i class="bi bi-pie-chart-fill text-lg text-white opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div class="col">
                                Gráfica de estadísticas general
                            </div>
                        </div>
                        <canvas id="myChart" width="100%" height="425"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div
                    :class="totals.total_earn > 0 ? 'bg-gradient-success' : 'bg-gradient-danger'" 
                    class="card mb-3">
                    <div class="card-body">
                        <div class="row text-white">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize cursor-pointer font-weight-bold" @click="showAlert('Ganancias totales se compone de la suma de: GANANCIAS BROKERS - (menos) PROFITS TOTALES.')"><u>Ganancias totales</u></p>
                                    <h5 class="font-weight-bolder text-white mb-0"> $ {{totals.total_earn.numberFormat(2)}}</h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                    <i class="bi bi-wallet text-lg text-dark opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card bg-gradient-danger mb-3">
                    <div class="card-body">
                        <div class="row text-white">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize cursor-pointer font-weight-bold" @click="showAlert('Profits totales se compone de la suma de: las ganancias que los usuarios reciben por inversiones y referidos.')"><u>Profits totales</u></p>
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold"></p>
                                    <h5 class="font-weight-bolder text-white mb-0"> $ {{totals.total_profits.numberFormat(2)}}</h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                    <i class="bi bi-wallet text-lg text-dark opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card bg-gradient-primary mb-3">
                    <div class="card-body">
                        <div class="row text-white">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize cursor-pointer font-weight-bold" @click="showAlert('Ganancias NETA por brokers se compone de: la GANANCIA NETA sumada de todos los dias en cuestión.')"><u>Ganancias NETA por brokers</u></p>
                                    <h5 class="font-weight-bolder text-white mb-0"> $ {{totals.total_real_gain.numberFormat(2)}}</h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                    <i class="bi bi-wallet text-lg text-dark opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <input  
                            :autofocus="true"
                            v-model="query"
                            class="form-control" type="text" placeholder="Buscar por día">
                    </div>
                    <div class="col-auto">
                        <button @click="toggleViewAll" class="btn btn-dark">
                            <span v-if="viewAll">Ocultar todos</span>
                            <span v-else>Mostrar todos</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div
            v-for="gain in gains" 
            class="card mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto"><i class="bi bi-pie-chart-fill"></i></div>
                    <div class="col">
                        <span class="badge bg-secondary text-xxs">{{gain.unix_day.formatDateText()}}</span>

                        <div 
                            :class="gain.total_earn > 0 ? 'text-success' : 'text-danger'"
                            class="mb-0 fw-semibold text-lg">$ {{gain.total_earn.numberFormat(2)}}</div>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-dark btn-sm" @click="toggleView(gain)">
                            <span v-if="gain.detail">Ocultar</span>
                            <span v-else>Ver detalle</span>
                        </button>
                    </div>
                </div>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th><h6 class="mb-0 text-sm">Broker</h6></th>
                        <th class="text-center"><h6 class="mb-0 text-sm">% Fee</h6></th>
                        <th class="text-center"><h6 class="mb-0 text-sm">Ganancia bruta</h6></th>
                        <th class="text-center"><h6 class="mb-0 text-sm">Ganancia neta</h6></th>
                        <th class="text-center"><h6 class="mb-0 text-sm">Profits INV.</h6></th>
                        <th class="text-center"><h6 class="mb-0 text-sm">Profits PAT.</h6></th>
                        <th class="text-center"><h6 class="mb-0 text-sm">Profits Total</h6></th>
                        <th class="text-center">
                            <h6 
                                :class="gain.total_earn > 0 ? 'text-success' : 'text-danger'"
                                class="mb-0 text-sm">
                                <span v-if="gain.total_earn > 0">Ganancia</span>
                                <span v-else>Perdida</span>
                            </h6>
                        </th>
                    </tr>
                </thead>
                <tbody
                    :class="gain.detail ? '' : 'd-none'">
                    <tr v-for="single_gain in gain.gains">
                        <td><h6 class="mb-0 text-sm">{{single_gain.name}}</h6></td>
                        <td class="text-center"><h6 class="mb-0 text-sm">{{single_gain.fee}}</h6></td>
                        <td class="text-center"><h6 class="mb-0 text-sm">$ {{single_gain.gain.numberFormat(2)}}</h6></td>
                        <td class="text-center"><h6 class="mb-0 text-sm">$ {{single_gain.real_gain.numberFormat(2)}}</h6></td>
                        <td class="text-center"><h6 class="mb-0 text-sm">-</h6></td>
                        <td class="text-center"><h6 class="mb-0 text-sm">-</h6></td>
                        <td class="text-center"><h6 class="mb-0 text-sm">-</h6></td>
                        <td class="text-center"><h6 class="mb-0 text-sm">-</h6></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="border-top border-bottom-0"></td>
                        <td class="border-top border-bottom-0 text-center"><h6 class="mb-0 text-sm">Totales</h6></td>
                        <td class="border-top border-bottom-0 text-center"><h6 class="mb-0 text-sm text-primary">$ {{gain.total_gain.numberFormat(2)}}</h6></td>
                        <td class="border-top border-bottom-0 text-center"><h6 class="mb-0 text-sm text-primary">$ {{gain.total_real_gain.numberFormat(2)}}</h6></td>
                        <td class="border-top border-bottom-0 text-center"><h6 class="mb-0 text-sm text-danger">$ {{gain.profit_investment.numberFormat(2)}}</h6></td>
                        <td class="border-top border-bottom-0 text-center"><h6 class="mb-0 text-sm text-danger">$ {{gain.profit_referral_investment.numberFormat(2)}}</h6></td>
                        <td class="border-top border-bottom-0 text-center">
                            <span>
                                <h6 class="mb-0 text-sm text-danger">$ {{gain.total_profit.numberFormat(2)}}</h6>
                            </span>
                        </td>
                        <td class="border-top border-bottom-0 text-center">
                            <h6 
                                :class="gain.total_earn > 0 ? 'text-success' : 'text-danger'"
                                class="mb-0 text-sm">$ {{gain.total_earn.numberFormat(2)}}</h6>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    `,
}

export { StatsViewer } 