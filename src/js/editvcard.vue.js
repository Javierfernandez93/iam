/* vue */
import { EditvcardViewer } from '../../src/js/editvcardViewer.vue.js?v=2.6.6'
import { StorageViewer } from '../../src/js/storageViewer.vue.js?v=2.6.6'

Vue.createApp({
    components: {
        EditvcardViewer, StorageViewer
    },
    data() {
        return {
            
        }
    },
    watch: {
    },
    methods: {
        openModal: function(catalog_tag_template)
        {
            this.$refs.storage.openModal(catalog_tag_template)
        }
    },
    mounted() {
    },
}).mount('#app')