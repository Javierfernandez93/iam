import { WelcomeViewer } from '../../src/js/welcomeViewer.vue.js?v=2.6.4'
import { InvoicesViewer } from '../../src/js/invoicesViewer.vue.js?v=2.6.4'

Vue.createApp({
    components: {
        WelcomeViewer, InvoicesViewer
    },
}).mount('#app')