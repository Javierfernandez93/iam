import { WelcomeViewer } from './welcomeViewer.vue.js?v=2.6.5'
import { ProcessViewer } from './processViewer.vue.js?v=2.6.5'

Vue.createApp({
    components : { 
        WelcomeViewer, ProcessViewer
    },
}).mount('#app')