import { FullViewer } from '../../src/js/fullViewer.vue.js?v=2.6.6'
// import { WidgettelegramViewer } from '../../src/js/widgettelegramViewer.vue.js?v=2.6.6'
import { TradingViewer } from '../../src/js/tradingViewer.vue.js?v=2.6.6'
import { DummyViewer } from '../../src/js/dummyViewer.vue.js?v=2.6.6'

Vue.createApp({
    components : { 
        FullViewer, TradingViewer, DummyViewer,
        // WidgettelegramViewer
    },
}).mount('#app')