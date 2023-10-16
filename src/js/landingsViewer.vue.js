import { Guest } from '../../src/js/guest.module.js?v=2.6.6'   

const LandingsViewer = {
    name : 'landings-viewer',
    data() {
        return {
            Guest : new Guest,
            userData : null,
            landing : null,
            ACTIONS: {
                WHATSAPP : 1,
                LINK : 2,
                SIGNUP : 3
            }
        }
    },
    methods: {
        getLandingByData(path,landing) {
            this.Guest.getLandingByData({path:path,landing:landing},(response)=>{
                if(response.s == 1)
                {
                    this.landing = response.landing
                    this.userData = response.userData
                }
            })
        },
        doAction(landing)
        {
            if(landing.catalog_landing_action_id == this.ACTIONS.WHATSAPP)
            {
                window.open(this.userData.whatsApp.getWhatsAppFromText(`¡Hola *${this.userData.names.trim()}* me interesa tu video *${landing.title}* de *DummieTrading*. ¿Puedes darme más información?`))
            } else if(landing.catalog_landing_action_id == this.ACTIONS.LINK) {
                window.open(landing.action)
            } else if(landing.catalog_landing_action_id == this.ACTIONS.SIGNUP) {
                window.open(this.userData.landing.getLandingPath())
            }
        },
        isYoutubeVideo(url)
        {
            const preg = /^(?:https?:\/\/)?(?:m\.|www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
            
            return url.match(preg) ? true : false
        },
        isVimeoVideo(url)
        {
            const preg = /^.+vimeo.com\/(.*\/)?([^#\?]*)/

            return url.match(preg) ? true : false
        },
        copyToClipBoard(text) {
            navigator.clipboard.writeText(text).then(() => {
                this.$refs.landing.innerText = 'Copiada'
            });
        },
        sendByWhatsapp(landing) {
            window.open(`*¡Hola!* quiero invitarte a un *proyecto increíble* que te permite *ganar dinero* por el *entretenimiento* ¡regístrate ya! ${landing}`.getWhatsappLink())
        },
    },
    mounted() 
    {   
        if(getParam("path"))
        {
            if(getParam("landing"))
            {
                this.getLandingByData(getParam("path"),getParam("landing"))
            }
        }
    },
    template : `
        <div class="d-flex vh-100 align-items-center">
            <div class="row w-100 justify-content-center">
                <div class="col-11 col-md-7 col-xl-5">
                    <div v-if="landing" class="card card-body bg-transparent border-0">
                        <h3 class="text-center pb-3 text-white">{{landing.title}}</h3>
                        
                        <div v-if="landing.content" class="mt-3">
                            <span v-html="landing.content"></span>
                        </div>

                        <div class="my-5 rounded shadow-xl overflow-hidden">
                            <div v-if="isYoutubeVideo(landing.video)">
                                <span v-html="landing.video.getYoutubeVideoFrame()"></span>
                            </div>
                            <div v-else-if="isVimeoVideo(landing.video)">
                                <span class="rounded shadow" v-html="landing.video.getVimeoFrameOld()"></span>
                            </div>
                        </div>

                        <div class="d-grid mt-3">
                            <button v-if="landing.catalog_landing_action_id == ACTIONS.WHATSAPP" @click="doAction(landing)" class="btn btn-warning mb-0 shadow-none btn-lg">
                                <h3>
                                    {{landing.text}}
                                </h3>
                            </button>
                            <button v-else-if="landing.catalog_landing_action_id == ACTIONS.LINK" @click="doAction(landing)" class="btn btn-warning mb-0 shadow-none btn-lg">
                                <h3>
                                    {{landing.text}}
                                </h3>
                            </button>
                            <button v-else-if="landing.catalog_landing_action_id == ACTIONS.SIGNUP" @click="doAction(landing)" class="btn btn-warning mb-0 shadow-none btn-lg">
                                <h3>
                                    {{landing.text}}
                                </h3>
                            </button>
                        </div>
                    </div>
                    <div class="text-center text-white">DummieTrading ® All rights reserved 2023</div>
                </div> 
            </div>
        </div>
    `,
}

export { LandingsViewer } 