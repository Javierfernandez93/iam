import { User } from '../../src/js/user.module.js?v=2.6.4'   

const LinksViewer = {
    name : 'links-viewer',
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
    },
    template : `
        <div class="row align-items-center justify-content-center">
            <div class="col-12 col-xl animation-fall-down mb-5" style="--delay:250ms">
                <div class="card">
                    <div class="overflow-hidden position-relative border-radius-lg bg-cover h-100" style="background-image:url(../../src/img/shipping.jpg)">
                        <div class="mask bg-gradient-dark"></div>

                        <div class="card-body position-relative z-index-1 h-100 text-white">
                            <div class="fs-3 fw-sembold">Octubre de miedo</div>
                            <div style="line-height:3rem" class="fs-2">Envío gratis a todo México</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl animation-fall-down mb-5" style="--delay:350ms">
                <div class="card">
                    <div class="overflow-hidden position-relative border-radius-lg bg-cover h-100" style="background-image:url(../../src/img/promo.jpg)">
                        <div class="mask bg-gradient-dark"></div>

                        <div class="card-body position-relative z-index-1 h-100 text-white">
                            <div class="fs-3 fw-sembold">Promo de locura</div>
                            <div style="line-height:2rem" class="fs-4">2 aceites</div>
                            <div style="line-height:2rem" class="fs-2 fw-sembold">$59.90</div>
                            <div style="line-height:2rem" class="fs-4">(Tiempo limitado)</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl animation-fall-down mb-5" style="--delay:450ms">
                <div class="card">
                    <div class="overflow-hidden position-relative border-radius-lg bg-cover h-100" style="background-image:url(../../src/img/referral.jpg)">
                        <div class="mask bg-gradient-dark"></div>

                        <div class="card-body position-relative z-index-1 h-100 text-white">
                            <div class="fs-3 fw-sembold">Refiere y gana</div>
                            <div style="line-height:3rem" class="fs-2">Gana un 5% por cada referido</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { LinksViewer } 