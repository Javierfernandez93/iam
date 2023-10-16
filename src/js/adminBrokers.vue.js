import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.6'

/* vue */
import { StatsViewer } from '../../src/js/statsViewer.vue.js'
import { ProfitViewer } from '../../src/js/profitViewer.vue.js?v=2.6.6'

Vue.createApp({
    components : {
        StatsViewer,ProfitViewer
    },
    data() {
        return {
            UserSupport : null,
            totals : {},
            date : {
                editing: false,
                today: null,
                day: null
            },
            operation_open : false,
            brokers : {},
            brokersAux : {},
            query : null,
            columns: { // 0 DESC , 1 ASC 
                name : {
                    name: 'name',
                    desc: false,
                    alphabetically: true,
                },
                capital : {
                    name: 'capital',
                    desc: true,
                },
                portfolio : {
                    name: 'portfolio',
                    desc: false,
                    alphabetically: true,
                },
                gain : {
                    name: 'gain',
                    desc: false,
                },
                fee : {
                    name: 'fee',
                    desc: false,
                },
                real_gain : {
                    name: 'real_gain',
                    desc: false,
                },
                percentaje_gain : {
                    name: 'percentaje_gain',
                    desc: false,
                },
                new_capital : {
                    name: 'new_capital',
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
        },
        brokers : 
        {
            handler() {
                this.calculateData()
            },
            deep : true
        }
    },
    methods: {
        sortData: function (column) {
            this.brokers.sort((a,b) => {
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
        getBrokersByDate : function() {
            this.getBrokers()
            this.toggleDateEditing()
        },
        toggleDateEditing : function() {
            this.date.editing = !this.date.editing 
        },
        filterData : function() {
            this.brokers = this.brokersAux
            
            this.brokers = this.brokersAux.filter((broker)=>{
                return broker.name.toLowerCase().includes(this.query.toLowerCase())
            })
        },
        inactiveBroker : function(broker_id) {
            this.UserSupport.inactiveBroker({broker_id:broker_id},(response)=>{
                if(response.s == 1)
                {
                    this.getBrokers()
                }
            })
        },
        activeBroker : function(broker_id) {
            this.UserSupport.activeBroker({broker_id:broker_id},(response)=>{
                if(response.s == 1)
                {
                    this.getBrokers()
                }
            })
        },
        deleteBroker : function(broker_id) {
            this.UserSupport.deleteBroker({broker_id:broker_id},(response)=>{
                if(response.s == 1)
                {
                    this.getBrokers()
                }
            })
        },
        closeOperation : function(broker) {
            this.UserSupport.closeOperation({brokers:this.brokers,day:this.date.day},(response) => {
                if(response.s == 1)
                {
                    this.$refs.operation_open.innerText = 'Operación cerrada con éxito'
                    this.getBrokers()
                }
            })
        },
        addGainPerBroker : function(broker) {
            this.UserSupport.addGainPerBroker({gain:broker.gain,broker_id:broker.broker_id,day:this.date.day},(response) => {
                if(response.s == 1)
                {
                    
                }
            })
            
            this.UserSupport.addPerformance({percentaje_gain:this.totals.percentaje_gain,day:this.date.day},(response) => {
                if(response.s == 1)
                {
                    
                }
            })

            this.toggleEditing(broker);
        },
        toggleEditing : function(broker) {
            broker['editing'] = broker['editing'] != undefined ? !broker['editing'] : true
        },
        goToActivatePlan : function(company_id) {
            window.location.href = '../../apps/admin-users/activate?ulid='+company_id
        },
        resetTotals : function() {
            Object.assign(this.totals, {
                fee: 0,
                gain: 0,
                new_capital: 0,
                percentaje_gain: 0,
                portfolio: 0,
                real_gain: 0
            })
        },
        calculateData : function() {
            this.calculateVars()
            this.calculateTotals()
        },
        calculateVars : function() {
            this.brokers.map((broker) => {
                // getting gain witout fee
                broker['real_gain'] = broker['fee'] == 0 ? broker['gain'] : broker['fee'] * broker['gain']

                // getting gain percentaje
                broker['percentaje_gain'] = (broker['real_gain'] / broker['capital']) * 100

                // new capital
                broker['new_capital'] = broker['capital'] + broker['gain']
            })
        },
        addCapital : function(broker) {
            const alert = alertCtrl.create({
                title: `añade el monto para ${broker.name}`,
                subTitle: "monto a invertir",
                inputs : [
                  {
                    type: 'number',
                    placeholder: '$0',
                    name: 'capital',
                    id:'capital'
                  }  
                ],
                buttons: [
                    { 
                        text: 'Aceptar',
                        handler: data => {
                            this.UserSupport.addCapitalToBroker({capital:data.capital,broker_id:broker.broker_id,day:this.date.day},(response) => {
                                if(response.s == 1)
                                {
                                    this.getBrokers()
                                }
                            })
                        }              
                    },
                    {
                        text: 'Cancelar',
                        role: 'cancel', 
                        handler: data => {
                        }
                    },  
                ]
            });
          
            alertCtrl.present(alert.modal);
        },
        calculateTotals : function() {
            this.resetTotals()
            this.brokers.map((broker) => {
                this.totals['portfolio'] += parseFloat(broker['portfolio']);
                this.totals['gain'] += broker['gain'] ? parseFloat(broker['gain']) : 0;
                this.totals['fee'] += parseFloat(broker['fee']);
                this.totals['real_gain'] += parseFloat(broker['real_gain']);
                this.totals['new_capital'] += parseFloat(broker['gain'] + broker['capital']);
            })

            // fixing
            this.totals['percentaje_gain'] = (this.totals['real_gain'] / this.totals['capital']) * 100;
        },
        viewCapitals : function(broker_id) {
            window.location.href = '../../apps/admin-brokers/capitals?bid='+broker_id
        },
        goToEdit : function(broker_id) {
            window.location.href = '../../apps/admin-brokers/edit?bid='+broker_id
        },
        getBrokers : function() {
            this.UserSupport.getBrokers({day:this.date.day},(response)=>{
                if(response.s == 1)
                {
                    this.operation_open = response.operation_open
                    this.date.day = response.day
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
        this.getBrokers()
    },
}).mount('#app')