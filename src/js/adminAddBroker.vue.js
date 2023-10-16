import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.5'

/* vue */

Vue.createApp({
    components : { 
    },
    data() {
        return {
            brokerComplete : false,
            UserSupport : null,
            broker : {
                name : null,
                fee : null,
                capital : null,
                gain : null,
            },
        }
    },
    watch : {
        broker : 
        {
            handler() {
                this.brokerComplete = this.broker.name != null && this.broker.fee != null
            },
            deep : true
        }
    },
    methods: {
        saveBroker : function() {
            this.UserSupport.saveBroker(this.broker,(response)=>{
                if(response.s == 1)
                {
                    this.$refs.button.innerText = "Broker guardado"
                }
            })
        },
        getBroker : function() {
            this.UserSupport.getBrokers({},(response)=>{
                if(response.s == 1)
                {
                    this.day = response.day
                    this.totals.capital = response.data.totals.capital
                    this.brokersAux = response.data.brokers
                    this.brokers = this.brokersAux

                    this.calculateData()
                }
            })
        },
    },
    mounted() 
    {
        this.UserSupport = new UserSupport
        // this.getBrokers()
    },
}).mount('#app')