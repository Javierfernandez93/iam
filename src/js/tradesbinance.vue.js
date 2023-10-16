import { TradesbinanceViewer } from '../../src/js/tradesbinanceViewer.vue.js?v=2.6.5'
import { OrdermakerViewer } from '../../src/js/ordermakerViewer.vue.js?v=2.6.5'

Vue.createApp({
    components : { 
        TradesbinanceViewer, OrdermakerViewer
    },
    data() {
        return {
            showOrderMaker: false
        }
    },
    methods : {
        toggleOrderMaker() {
            this.$refs.order.toggleModal()
        },
        sendOrder(data) {
            this.$refs.trades.getOrder(data)
        }
    }
}).mount('#app')