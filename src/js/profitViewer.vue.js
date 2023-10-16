import { User } from '../../src/js/user.module.js?v=2.6.6'   

const ProfitViewer = {
    name : 'profit-viewer',
    data() {
        return {
            User: new User,
            myChart : null,
            gains : null,
            total_gains: 0
        }
    },
    methods: {
        getProfitStats() {
            this.User.getProfitStats({},(response)=>{
                if(response.s == 1)
                {
                    this.balance = {...response.balance}
                }
            })
        },
        initChart(gains) {
            const ctx = document.getElementById("myChart").getContext("2d");

            let datasets = [];
            let labels = [];
            let profits = [];

            gains.reverse().map((gain)=>{
                labels.push(gain.create_date.formatDateTextChart())

                profits.push(gain.amount)
            })
            
            datasets.push({
                label: "Profits",
                data: profits,
                borderColor: "#7928CA",
                backgroundColor: "#7928CA",
            })

            const data = {
                labels: labels,
                datasets: datasets,
            };

            const config = {
                type: "line",
                data: data,
                options: {
                    tension: 0.5,
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                    },
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                            },
                            grid: {
                                display: false,
                            }                          
                        },
                        y: {
                            display: false,
                            title: {
                                display: true,
                                text: "($) USD",
                            },
                            grid: {
                                display: false,
                            } 
                        },
                    },
                },
            };

            this.myChart = new Chart(ctx, config);
        },
        getGainsChart() {    
            return new Promise((resolve,reject) => {        
                this.User.getGainsChart({},(response)=>{
                    if(response.s == 1)
                    {
                        resolve(response)
                    }

                    reject()
                })
            })
        },
    },
    mounted() 
    {   
        this.getProfitStats()

        this.getGainsChart().then((response)=>{
            this.gains = response.gains
            this.total_gains = response.total_gains


            this.initChart(this.gains)
        }).catch(() => {
            this.gains = false 
        })
    },
    template : `
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-xl">
                        <div><span class="text-secondary">Ganancias generadas</span></div>
                        <div class="align-items-center">
                            <span class="fs-2 text-dark">USD $ {{ total_gains.numberFormat(2) }}</span>
                            <span class="badge fw-sembold text-success">+0%</span>
                        </div>
                    </div>
                    <div class="col-12 col-xl-auto">
                        <a href="../../apps/backoffice/profile" class="btn btn-primary btn-sm px-3 shadow-none mb-0">Ir a mi perfil</a>
                    </div>
                </div>
                <div class="row">
                    <canvas v-show="gains" ref="myChart" id="myChart"></canvas>

                    <div v-if="gains == false" class="d-flex myChart-nogains justify-content-center align-items-center">
                        <div class="row w-100">
                            <div class="col-12 text-center">
                                <div class="mb-3 d-flex justify-content-center text-center">
                                    <span class="badge text-center badge-round rounded-circle bg-light">
                                        <i class="bi fs-3 text-dark bi-bar-chart-fill"></i>
                                    </span>
                                </div>
                                <div>
                                    Aquí podrás ver las ganancias de tus trades
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { ProfitViewer } 