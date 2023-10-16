import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.5'

/* vue */

Vue.createApp({
    components: {
    },
    data() {
        return {
            UserSupport: null,
            broker: {
                name: null,
                fee: null,
            },
            query: null,
            capitals: {},
            capitalsAux: {}
        }
    },
    watch: {
        query:
        {
            handler() {
                this.filterData()
            },
            deep: true
        }
    },
    methods: {
        deleteCapital: function (capital_per_broker_id) {
            this.UserSupport.deleteCapital({ capital_per_broker_id: capital_per_broker_id }, (response) => {
                if (response.s == 1) {
                    this.getBrokerCapitals(getParam('bid'))
                }
            })
        },
        filterData: function () {
            this.capitals = this.capitalsAux

            this.capitals = this.capitals.filter(capital => {
                return capital.capital.toString().includes(this.query.toLowerCase()) || capital.create_date.formatDate().toString().includes(this.query.toLowerCase())
            })
        },
        getBrokerCapitals: function (broker_id) {
            this.UserSupport.getBrokerCapitals({ broker_id: broker_id }, (response) => {
                if (response.s == 1) {
                    this.broker = response.broker
                    this.capitalsAux = response.capitals
                    this.capitals = this.capitalsAux

                    console.log(this.capitals)
                }
            })
        },
    },
    mounted() {
        this.UserSupport = new UserSupport

        if (getParam('bid')) {
            this.getBrokerCapitals(getParam('bid'))
        }
    },
}).mount('#app')