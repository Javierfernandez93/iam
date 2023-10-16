
import { TafViewer } from '../../src/js/tafViewer.vue.js?v=2.6.6'
import { TafaddViewer } from '../../src/js/tafaddViewer.vue.js?v=2.6.6'

Vue.createApp({
    components : { 
        TafViewer, TafaddViewer
    },
    methods: {
        getInvitationsPerUserMaster()
        {
            this.$refs.list.getInvitationsPerUserMaster()
        }
    }
}).mount('#app')