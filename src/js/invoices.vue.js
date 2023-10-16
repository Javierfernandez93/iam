import { WelcomeViewer } from '../../src/js/welcomeViewer.vue.js?v=2.6.6'
import { InvoicesViewer } from '../../src/js/invoicesViewer.vue.js?v=2.6.6'

Vue.createApp({
    components: {
        WelcomeViewer, InvoicesViewer
    },
}).mount('#app')