import { User } from '../../src/js/user.module.js?v=1.0.3'   

const WidgetlandingViewer = {
    name : 'widgetlanding-viewer',
    data() {
        return {
            User: new User,
            landing : null,
            hasLandingConfigurated : null,
        }
    },
    methods: {
        getReferralLanding() {
            this.User.getReferralLanding({},(response)=>{
                if(response.s == 1)
                {
                    this.landing = response.landing
                    this.hasLandingConfigurated = response.hasLandingConfigurated
                }
            })
        },
        copyToClipBoard(text,target) {
            let currentText = target.innerText

            navigator.clipboard.writeText(text).then(() => {
                target.innerText = 'Copiada...'
                
                setTimeout(() => {
                    target.innerText = currentText
                },2000)
            });
        },
        sendByWhatsapp : function(landing) {
            window.open(`*¡Hola!* quiero invitarte DummieTrading *proyecto increíble* que te permite *ganar dinero* por el *trading* ¡regístrate ya! ${landing}`.getWhatsappLink())
        },
    },
    mounted() 
    {   
        this.getReferralLanding()
    },
    template : `
        <div v-if="landing" class="mt-3 overflow-hidden position-relative border-radius-lg bg-cover animation-fall-down" style="--delay:500ms" style="background-image: url('../../assets/img/ivancik.jpg');">
            <span class="mask bg-gradient-primary"></span>
            <div class="card-body position-relative z-index-1 h-100 p-3">
                <h6 class="text-white font-weight-bolder mb-3">¿Te gusta Dummie Trading?</h6>
                <p class="text-white mb-3">Comparte tu enlace personal</p>
                
                <span @click="copyToClipBoard(landing.getFullLanding(),$event.target)" class="badge cursor-pointer text-xxs mb-3 border border-light">{{landing.getFullLanding()}}</span>
                
                <div>
                    <button @click="copyToClipBoard(landing.getFullLanding(),$event.target)" class="btn btn-round btn-outline-white">
                        Copiar Landing
                        <i class="fas fa-arrow-right text-sm ms-1" aria-hidden="true"></i>
                    </button>
                    <button @click="sendByWhatsapp(landing.getFullLanding())" class="btn mb-3 btn-round btn-outline-white mb-0">
                        Envíar a WhatsApp
                        <i class="fas fa-arrow-right text-sm ms-1" aria-hidden="true"></i>
                    </button>
                </div>
                <a v-if="!hasLandingConfigurated" href="../../apps/backoffice/profile" class="btn btn-round btn-outline-white mb-0">
                    Personalizar Landing
                    <i class="fas fa-arrow-right text-sm ms-1" aria-hidden="true"></i>
                </a>
                <a v-if="hasLandingConfigurated" href="../../apps/backoffice/landings" class="btn btn-round btn-outline-white mb-0">
                    Ver landings disponibles
                    <i class="fas fa-arrow-right text-sm ms-1" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    `,
}

export { WidgetlandingViewer } 