import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.4'

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
                console.log(this.broker.name != null)
                console.log(this.broker.fee != null)
                this.brokerComplete = this.broker.name != null && this.broker.fee != null
            },
            deep : true
        }
    },
    methods: {
        updateBroker : function() {
            this.UserSupport.updateBroker(this.broker,(response)=>{
                if(response.s == 1)
                {
                    this.$refs.button.innerText = "Actualizado"
                }
            })
        },
        getBroker : function(broker_id) {
            this.UserSupport.getBroker({broker_id:broker_id},(response)=>{
                if(response.s == 1)
                {
                    Object.assign(this.broker, response.broker)
                }
            })
        },
    },
    mounted() 
    {
        this.UserSupport = new UserSupport
        
        if(getParam('bid'))
        {
            this.getBroker(getParam('bid'))
        }
    },
}).mount('#app')