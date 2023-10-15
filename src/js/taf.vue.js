
import { TafViewer } from '../../src/js/tafViewer.vue.js?v=2.6.4'
import { TafaddViewer } from '../../src/js/tafaddViewer.vue.js?v=2.6.4'

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