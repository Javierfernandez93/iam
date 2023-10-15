import { FullViewer } from '../../src/js/fullViewer.vue.js?v=2.6.4'
// import { WidgettelegramViewer } from '../../src/js/widgettelegramViewer.vue.js?v=2.6.4'
import { TradingViewer } from '../../src/js/tradingViewer.vue.js?v=2.6.4'
import { DummyViewer } from '../../src/js/dummyViewer.vue.js?v=2.6.4'

Vue.createApp({
    components : { 
        FullViewer, TradingViewer, DummyViewer,
        // WidgettelegramViewer
    },
}).mount('#app')