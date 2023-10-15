
import { LastordersViewer } from '../../src/js/lastordersViewer.vue.js?v=2.6.4'
import { WelcomeViewer } from '../../src/js/welcomeViewer.vue.js?v=2.6.4'
import { LinksViewer } from '../../src/js/linksViewer.vue.js?v=2.6.4'
import { AdvicesViewer } from '../../src/js/advicesViewer.vue.js?v=2.6.4'
import { ChatViewer } from '../../src/js/chatViewer.vue.js?v=2.6.4'

Vue.createApp({
    components : { 
        LastordersViewer, WelcomeViewer, LinksViewer, AdvicesViewer, ChatViewer
    },
}).mount('#app')