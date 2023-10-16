import { Guest } from '../../src/js/guest.module.js?v=2.6.6'   

const VcardshareViewer = {
    name : 'vcardshare-viewer',
    props : ['vcard'],
    emits : [],
    data() {
        return {
            Guest: new Guest,
            route: null,
        }
    },
    watch : {
        query : {
            handler() {
                this.filterData()
            },
            deep: true
        }
    },
    methods: {
        getVcardRoute: function (vcard_per_user_id) {
            return new Promise((resolve,reject) => {
                this.Guest.getVcardRoute({vcard_per_user_id:vcard_per_user_id}, (response) => {
                    if (response.s == 1) {
                        resolve(response.route)
                    }

                    reject()
                })
            })
        },
        copyLink: function(link,target) {   
            navigator.clipboard.writeText(link).then(() => {
                target.innerText = 'copiado'
            });
        },
    },
    mounted() {
    },
    template : `
        <div v-if="vcard.route != null" class="card mt-3" id="card-share">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-12">
                        <div class="row fs-1">
                            <div class="col">  
                                <a :href="vcard.route.getWhatsAppFromRoute()">
                                    <i class="bi bi-whatsapp"></i>
                                </a>
                            </div>
                            <div class="col">  
                                <a :href="vcard.route.getLinkedinFromRoute()">
                                    <i class="bi bi-linkedin"></i>
                                </a>
                            </div>
                            <div class="col">  
                                <a :href="vcard.route.getTwitterFromRoute()">
                                    <i class="bi bi-twitter"></i>
                                </a>
                            </div>
                            <div class="col">  
                                <a :href="vcard.route.getRedditFromRoute()">
                                    <i class="bi bi-reddit"></i>
                                </a>
                            </div>
                            <div class="col">  
                                <a :href="vcard.route.getPinterestFromRoute()">
                                    <i class="bi bi-pinterest"></i>
                                </a>
                            </div>
                            <div class="col">  
                                <a :href="vcard.route.getEmailFromRoute()">
                                    <i class="bi bi-mailbox"></i>
                                </a>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col text-primary text-socials">
                                Métodos para compartir tu VCard
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { VcardshareViewer } 