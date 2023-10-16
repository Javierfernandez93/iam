import { WelcomeViewer } from './welcomeViewer.vue.js?v=2.6.6'
import { ProcessViewer } from './processViewer.vue.js?v=2.6.6'

Vue.createApp({
    components : { 
        WelcomeViewer, ProcessViewer
    },
}).mount('#app')