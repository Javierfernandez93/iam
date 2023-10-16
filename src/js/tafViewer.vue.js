import { User } from '../../src/js/user.module.js?v=2.6.5'   

const TafViewer = {
    name : 'taf-viewer',
    data() {
        return {
            User : new User,
            busy : false,
            invitations : null,
            invitationsAux : null,
            STATUS : {
                PENDING: 0,
                SENT: 1,
            }
        }
    },
    methods: {
        getInvitationsPerUser() {
            return new Promise((resolve)=>{
                this.busy = true
                this.User.getInvitationsPerUser({},(response)=>{
                    this.busy = false
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
        getInvitationsPerUserMaster() {
            this.getInvitationsPerUser().then((response) => {
                this.invitations = response.invitations
                this.invitationsAUX = response.invitations
            })
        },
    },
    mounted() 
    {   
        this.getInvitationsPerUserMaster()
    },
    template : `
        <div class="card shadow-blur blur animation-fall-down" style="--delay:200ms">
            <div class="card-header bg-transparent">
                <div class="row align-items-center">
                    <div class="col-12 col-xl">
                        <h3>Tell a friend</h3>
                    </div>
                    <div v-if="invitations" class="col-12 col-xl-auto">
                        <span class="badge bg-primary">
                            {{invitations.length}} enviadas
                        </span>
                    </div>
                    <div v-if="busy" class="col-12 col-xl-auto">
                        <div class="spinner-grow text-white" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="invitations" class="card-body overflow-scroll" style="max-height:30rem">
                <ul class="list-group list-group-flush bg-transparent">
                    <li v-for="invitation in invitations" class="list-group-item py-3 bg-transparent">
                        <div class="row align-items-center">
                            <div class="col-12 col-xl">
                                <div>
                                    <span class="badge bg-secondary">
                                        {{invitation.channel}}
                                    </span>
                                </div>
                                <div class="h5 mt-3">
                                    Campaña <b>{{invitation.title}}</b>
                                </div>
                                <div>
                                    Contacto <b class="text-primary">{{invitation.contact}}</b>
                                </div>
                            </div>
                            <div class="col-12 col-xl-auto text-center">
                                <div v-if="invitation.send_date">
                                    <div class=""><span class="badge bg-primary">Enviado {{invitation.send_date.timeSince()}}</span></div>
                                </div>
                            </div>
                            <div class="col-12 col-xl-auto">
                                <div v-if="invitation.status == STATUS.SENT" class="badge bg-success">
                                    Enviado
                                </div>
                                <div v-else-if="invitation.status == STATUS.PENDING" class="badge bg-warning">
                                    Pendiente
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div v-else class="card-body text-center">
                <div class="h4">
                    Crea tu primer invitación
                </div>
               <div class="d-flex justify-content-center">
                    <lottie-player src="../../src/files/json/tell-a-friend.json" background="transparent"  speed="1"  mode=“normal” loop autoplay style="width: 250px; height: 250px;"></lottie-player>
                </div>
            </div>
        </div>
    `,
}

export { TafViewer } 