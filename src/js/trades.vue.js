import { TradesViewer } from '../../src/js/tradesViewer.vue.js?v=2.6.6'
import { OrdermakerViewer } from '../../src/js/ordermakerViewer.vue.js?v=2.6.6'
import { VarsViewer } from '../../src/js/varsViewer.vue.js?v=2.6.6'
import { TradingviewwidgetViewer } from '../../src/js/tradingviewwidgetViewer.vue.js?v=2.6.6'

Vue.createApp({
    components : { 
        TradesViewer, OrdermakerViewer, VarsViewer, TradingviewwidgetViewer 
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
        toggleVars() {
            console.log(1)
            this.$refs.vars.toggleModal()
        },
        sendOrder(data) {
            this.$refs.trades.sendOrder(data)
        },
        sendOrderAsSignal(data) {
            this.$refs.trades.sendOrderAsSignal(data)
        },
        openCanvasTop(symbol)
        {
            this.$refs.tradingview.initTradingView(symbol)
        },
    }
}).mount('#app')