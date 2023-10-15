import { Guest } from '../../src/js/guest.module.js?v=2.6.4'   

/* vue */
import { VcardqrViewer } from './vcardqrViewer.vue.js?v=2.6.4'
import { VcardshareViewer } from './vcardshareViewer.vue.js?v=2.6.4'
import { VcardsocialViewer } from './vcardsocialiewer.vue.js?v=2.6.4'

Vue.createApp({
    components: {
        VcardqrViewer, VcardshareViewer, VcardsocialViewer
    },
    data() {
        return {
            Guest : new Guest,
            vcard : {
                vcard_per_user_id: null,
                route: null,
            }
        }
    },
    watch: {
    },
    methods: {
        runMethods: function() {   
            this.$refs.social.getSocialsFromVcard(this.vcard.vcard_per_user_id)
            this.$refs.qr.getVcardQR(this.vcard.vcard_per_user_id)
            this.$refs.share.getVcardRoute(this.vcard.vcard_per_user_id)
        },
        getVcard: function() {   
            return new Promise((resolve,reject) => {
                if(!getParam('vpulid'))
                {
                    this.Guest.getVCardByRoute({route:getLastUrlPart()}, (response) => {
                        if (response.s == 1) {
                            resolve(response.vcard)
                        }

                        reject()
                    })
                } else {
                    this.Guest.getVCardById({vcard_per_user_id:getParam('vpulid')}, (response) => {
                        if (response.s == 1) {
                            resolve(response.vcard)
                        }

                        reject()
                    })
                }
            })
        },
    },
    mounted() {
        this.getVcard().then((vcard)=>{
            this.vcard = vcard

            this.runMethods()
        }).catch(() => this.vcard.vcard_per_user_id = null)
    },
}).mount('#app')