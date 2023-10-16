import { SignalsViewer } from '../../src/js/signalsViewer.vue.js?v=2.6.5'
import { SignalsaddViewer } from '../../src/js/signalsaddViewer.vue.js?v=2.6.5'
import { TradingviewwidgetViewer } from '../../src/js/tradingviewwidgetViewer.vue.js?v=2.6.5'

Vue.createApp({
    components : { 
        SignalsViewer, SignalsaddViewer, TradingviewwidgetViewer
    },
    methods: {
        openCanvas(channel)
        {
            this.$refs.off.setChannel(channel)
            this.$refs.off.openOffCanvas()
        },
        openCanvasTop(symbol)
        {
            this.$refs.tradingview.initTradingView(symbol)
        },
    }
}).mount('#app')