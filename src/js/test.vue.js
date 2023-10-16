import { TestViewer } from '../../src/js/testViewer.vue.js?v=2.6.6'
import { TestprogressViewer } from '../../src/js/testprogressViewer.vue.js?v=2.6.6'

Vue.createApp({
    components : { 
        TestViewer, TestprogressViewer
    },
}).mount('#app')