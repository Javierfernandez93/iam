import { User } from '../../src/js/user.module.js?v=2.6.6'   

const BackofficelandingsViewer = {
    name : 'backofficelandings-viewer',
    data() {
        return {
            User : new User,
            hasLandingConfigurated : null,
            userLanding : null,
            landings : null,
        }
    },
    methods: {
        getAllMyLandings() {
            return new Promise((resolve)=>{
                this.User.getAllMyLandings({},(response)=>{
                    if(response.s == 1)
                    {
                        resolve(response)
                    }
                })
            }) 
        },
        copyToClipBoard(text,target) {
            navigator.clipboard.writeText(text).then(() => {
                target.innerText = 'Copiado'
            });
        },
        openLink(landing) {
            window.open(landing)
        },
        sendWhatsApp(landing) {
            const landingUrl = landing.path.getLandingPathFormatted(this.userLanding)

            window.open(`${landing.share_text} ${landingUrl}`.getWhatsappLink())
        },
    },
    mounted() 
    {   
        this.getAllMyLandings().then((response) => {
            this.hasLandingConfigurated = response.hasLandingConfigurated

            if(this.hasLandingConfigurated)
            {
                this.landings = response.landings
                this.userLanding = response.userLanding
            }
        })
    },
    template : `
        <div v-if="landings">
            <div v-for="landing in landings">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="fw-semibold text-dark">
                            <div class="fw-sembold">{{landing.title}}</div>

                            <div class="text-secondary">{{landing.description}}</div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-xl">
                                <a target="_blank" :href="landing.path.getLandingPathFormatted(userLanding)">{{landing.path.getLandingPathFormatted(userLanding)}}</a>
                            </div>
                            <div class="col-12 col-xl-auto">
                                <button @click="sendWhatsApp(landing)" class="btn shadow-none mb-0 px-3 btn-sm me-2 btn-success">Enviar WhatsApp</button>
                                <button @click="openLink(landing.path.getLandingPathFormatted(userLanding))" class="btn shadow-none mb-0 px-3 btn-sm me-2 btn-primary">Abrir</button>
                                <button @click="copyToClipBoard(landing.path.getLandingPathFormatted(userLanding),$event.target)" class="btn shadow-none mb-0 px-3 btn-sm btn-primary">Copiar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="hasLandingConfigurated == false" class="alert alert-info text-center text-white">
            No tienes configurada tu landing, para comenzar ve a tu perfil y crea tu identificador único
            <div>
                <a href="../../apps/backoffice/profile" class="btn mb-0 mt-3 shadow-none btn-lg btn-light">Ir a configurar mi landing</a>
            </div>
        </div>
    `,
}

export { BackofficelandingsViewer } 