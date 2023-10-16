import { Guest } from '../../src/js/guest.module.js?v=2.6.5'   

const VcardsocialViewer = {
    name : 'vcardsocial-viewer',
    props : ['vcard'],
    emits : [],
    data() {
        return {
            Guest: new Guest,
            socials: null,
        }
    },
    watch : {
        vcard : {
            handler() {
                if(this.vcard)
                {
                    console.log('ease')
                    console.log(this.vcard)
                }
            },
            deep: true
        }
    },
    methods: {
        getSocialsFromVcard: function (vcard_per_user_id) {
            this.Guest.getSocialsFromVcard({vcard_per_user_id:vcard_per_user_id}, (response) => {
                if (response.s == 1) {
                    this.socials = response.socials
                }
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
        <div v-if="socials" class="card mt-3" id="card-social">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-12">
                        <div class="row fs-1">
                            <div v-if="socials.facebook" class="col">  
                                <a :href="socials.facebook.getFacebookFromText()" class="btn btn-social btn-outline-primary">
                                    <i class="bi bi-facebook"></i>
                                </a>
                            </div>
                            <div v-if="socials.instagram" class="col">  
                                <a :href="socials.instagram.getInstagramFromText()" class="btn btn-social btn-outline-secondary">
                                    <i class="bi bi-instagram"></i>
                                </a>
                            </div>
                            <div v-if="socials.youtube" class="col">  
                                <a :href="socials.youtube.getYoutubeFromText()" class="btn btn-social btn-outline-danger">
                                    <i class="bi bi-youtube"></i>
                                </a>
                            </div>
                            <div v-if="socials.whatsapp" class="col">  
                                <a :href="socials.whatsapp.getWhatsAppFromText('¡Hola!')" class="btn btn-social btn-outline-success">
                                    <i class="bi bi-whatsapp"></i>
                                </a>
                            </div>
                            <div v-if="socials.linkedin" class="col">  
                                <a :href="socials.linkedin.getLinkedinFromText()" class="btn btn-social btn-outline-primary">
                                    <i class="bi bi-linkedin"></i>
                                </a>
                            </div>
                            <div v-if="socials.twitter" class="col">  
                                <a :href="socials.twitter.getTwitterFromText()" class="btn btn-social btn-outline-info">
                                    <i class="bi bi-twitter"></i>
                                </a>
                            </div>
                            <div v-if="socials.reddit" class="col">  
                                <a :href="socials.reddit.getRedditFromText()" class="btn btn-social btn-outline-warning">
                                    <i class="bi bi-reddit"></i>
                                </a>
                            </div>
                            <div v-if="socials.pinterest" class="col">  
                                <a :href="socials.pinterest.getPinterestFromText()" class="btn btn-social btn-outline-danger">
                                    <i class="bi bi-pinterest"></i>
                                </a>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col text-primary text-socials">
                                Mis redes sociales
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { VcardsocialViewer } 