import { HomeViewer } from '../../src/js/homeViewer.vue.js?v=2.6.5'
import { NewsletterViewer } from '../../src/js/newsletterViewer.vue.js?v=2.6.5'
import { LoginwidgetViewer } from '../../src/js/loginwidgetViewer.vue.js?v=2.6.5'

Vue.createApp({
    components : { 
        HomeViewer, NewsletterViewer, LoginwidgetViewer
    },
}).mount('#app')