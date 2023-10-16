import { WelcomeViewer } from '../../src/js/welcomeViewer.vue.js?v=2.6.6'
import { ProfileViewer } from '../../src/js/profileViewer.vue.js?v=2.6.6'

Vue.createApp({
    components : { 
        WelcomeViewer, ProfileViewer
    },
}).mount('#app')