import { User } from '../../src/js/user.module.js?v=2.6.6'   

const LandingViewer = {
    name : 'landing-viewer',
    data() {
        return {
            User : new User,
            landing : null,
            hasLandingConfigurated : null,
        }
    },
    watch : {
        
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
        copyToClipBoard : function(text) {
            navigator.clipboard.writeText(text).then(() => {
                this.$refs.landing.innerText = 'Copiada'
            });
        },
        sendByWhatsapp : function(landing) {
            window.open(`*¡Hola!* quiero invitarte a un *proyecto increíble* que te permite *ganar dinero* por el *entretenimiento* ¡regístrate ya! ${landing}`.getWhatsappLink())
        },
    },
    updated() {
    },
    mounted() 
    {   
        this.getReferralLanding()
    },
    template : `
        <div class="card f-zoom-element shadow-none border bg-primary-soft mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-12 col-xl">
                        <div>
                            Link de referido
                        </div>
                        <div class="h4">
                            {{landing.getFullLanding()}}
                        </div>
                    </div>
                    <div class="col-12 col-xl-auto">
                        <button @click="copyToClipBoard(landing.getFullLanding())" ref="landing" class="btn shadow-none mb-0 me-2 btn-primary">Copiar link</button>
                        <button @click="sendByWhatsapp(landing.getFullLanding())" class="btn shadow-none mb-0 btn-success">Envíar por WhatsApp</button>
                    </div>
                </div>
            </div>
            <div v-if="!hasLandingConfigurated" class="card-footer">
                <div class="alert alert-danger text-white mb-0 text-center">
                    <div><strong>Crea tu landing personalizada</strong></div>
                    Parece que no tienes configurada tu URL personalizada, puedes configurarla dando clic <a class="text-white" href="../../apps/backoffice/profile"><u>aquí</u></a>. Ingresa un nombre para tu link de referido en el apartado de "Landing Personalizada"
                </div>
            </div>
        </div>
    `,
}

export { LandingViewer } 