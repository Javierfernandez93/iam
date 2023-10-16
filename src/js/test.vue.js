import { TestViewer } from '../../src/js/testViewer.vue.js?v=2.6.5'
import { TestprogressViewer } from '../../src/js/testprogressViewer.vue.js?v=2.6.5'

Vue.createApp({
    components : { 
        TestViewer, TestprogressViewer
    },
}).mount('#app')