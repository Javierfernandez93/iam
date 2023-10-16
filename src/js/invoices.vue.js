import { WelcomeViewer } from '../../src/js/welcomeViewer.vue.js?v=2.6.5'
import { InvoicesViewer } from '../../src/js/invoicesViewer.vue.js?v=2.6.5'

Vue.createApp({
    components: {
        WelcomeViewer, InvoicesViewer
    },
}).mount('#app')