import { StartViewer } from '../../src/js/startViewer.vue.js?v=2.6.6'
import { MetatraderViewer } from '../../src/js/metatraderViewer.vue.js?v=2.6.6'
import { ConnectViewer } from '../../src/js/connectViewer.vue.js?v=2.6.6'
import { SignalViewer } from '../../src/js/signalViewer.vue.js?v=2.6.6'

Vue.createApp({
    components : { 
        StartViewer, MetatraderViewer, ConnectViewer, SignalViewer
    },
    data() {
        return {
            step: 1,
            steps: {
                SELECT_BROKER: 1,        
                CONNECT_TELEGRAM: 2,        
                TEST_SIGNALS: 3,        
            },
        }
    },
    methods:{
        nextStep() {
            this.step += 1
        },
        selectStep(step) {
            this.step = step
        }
    }
}).mount('#app')