import { User } from '../../src/js/user.module.js?v=2.6.4'   

/* vue */ 
import { ProfitViewer } from '../../src/js/profitViewer.vue.js?v=2.6.4'

Vue.createApp({
    components : { 
        ProfitViewer
    },
    data() {
        return {
            User : null,
            plans : {},
            chart : null,
            periods : [
                {
                    name: 'Año',
                    namePlural: 'Años',
                    aportationName: 'Anual',
                    months: 12,
                },
                {
                    name: 'Mes',
                    namePlural: 'Meses',
                    aportationName: 'Mensual',
                    months: 1,
                },
                {
                    name: 'Trimestre',
                    namePlural: 'Trimestres',
                    aportationName: 'Trimestral',
                    months: 3,
                },
                {
                    name: 'Semestre',
                    namePlural: 'Semestres',
                    aportationName: 'Semestral',
                    months: 6,
                }    
            ],
            data : {
                profit : null,
                capitalFinal : null,
                capital : null,
                roi : null,
                duration : {
                    quantity: null,
                    every: 1 // months
                }, // 
                contribution : {
                    ammount: null,
                    every: 1 // months
                }, // 
                result: {
                    profit: 0,
                    capital: 0
                },
            },
            results : [],
            gains : null,
            years : null,
        }
    },
    watch : {
        data : {
            handler() {
                this.calculateData()
            },
            deep: true
        },
    },
    methods: {
        getPlanProfit: function (ammount) {
            let profit = 0
            
            if(ammount >= parseFloat(this.plans[0].name))
            {
                for (let i = 0; i < this.plans.length; i++) {
                    const nextVal = this.plans[i + 1] != undefined ? parseFloat(this.plans[i + 1].name) : Infinity
    
                    if (ammount >= parseFloat(this.plans[i].name) && ammount < nextVal) {
                        profit = this.plans[i].profit
                    }
                }
            } 

            return profit

        },
        calculateDataResults : function() {
            return new Promise((resolve) => {
                this.results = []
                
                for(let i = 0; i < this.data.duration.every * this.data.duration.quantity; i++)
                {
                    let capital = i == 0 ? this.data.capital : this.results[this.results.length - 1].newCapital
                    const profit = this.getPlanProfit(capital)
                    const contribution = (i+1) % this.data.contribution.every == 0 ? this.data.contribution.ammount : 0 
                    
                    const capitalInitial = capital
                    capital += contribution
                    const newCapital = capital * ((profit / 100) + 1)

                    this.results.push({
                        period: i+1,
                        capitalInitial: capitalInitial,
                        contribution: contribution,
                        capital: capital,
                        profit: profit,
                        gain: newCapital - capital,
                        newCapital: newCapital,
                    }) 
                }
                
                this.data.result.capital = this.results[this.results.length - 1].newCapital
                this.data.result.profit = ((this.results[this.results.length - 1].newCapital * 100) / this.data.capital) - 100
                
                resolve()
            })
        },
        getPlans : function() {
            this.User.getPlans({}, (response) => {
                if(response.s == 1)
                {
                    this.plans = response.plans
                }
            })
        },
        calculateData : function() {
            if(this.data.capital > 0)
            {
                // if(this.data.contribution.ammount > 0 && this.data.contribution.every > 0)
                if(true)
                {
                    if(this.data.duration.quantity > 0 && this.data.duration.every > 0)
                    {
                        this.calculateDataResults().then(() => {   
                            this.data.capitalFinal = this.results[this.results.length - 1].result
                            this.data.profit = (this.data.capitalFinal * 100) / this.data.capital
                        })
                    }
                }
            }
        },
    },
    mounted() 
    {
        this.User = new User

        this.getPlans()
    },
}).mount('#app')