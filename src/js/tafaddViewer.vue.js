import { User } from '../../src/js/user.module.js?v=2.6.4'   

const TafaddViewer = {
    name : 'tafadd-viewer',
    emits : 'getInvitationsPerUserMaster',
    data() {
        return {
            User : new User,
            busy : false,
            invitation : {
                catalog_channel_id: 1,
                template: null,
                users: [],
            },
            MAX_USERS_PER_INVITATION: 3,
            CHANNELS : {
                WHATSAPP: 1
            },
            channels : null,
            templates : null
        }
    },
    watch: {
        'invitation.catalog_invitation_template_id' : {
            handler() {
                this.isInvitationFilled = this.template && this.users.length > 0;
            },
            deep: true
        },
        'invitation.catalog_invitation_template_id' : {
            handler() {
                
            },
            deep: true
        }
    },
    methods: {
        addUser() {
            if(this.invitation.users.length < this.MAX_USERS_PER_INVITATION)
            {
                this.invitation.users.push({
                    contact: null
                }) 
            }
        },
        getCatalogChannels() {
            return new Promise((resolve)=>{
                this.User.getCatalogChannels({},(response)=>{
                    if(response.s == 1)
                    {
                        resolve(response)
                    }
                })
            }) 
        },
        sendInvitations() {
            this.busy = true
            this.User.sendInvitations(this.invitation,(response)=>{
                this.busy = false
                if(response.s == 1)
                {
                    this.$emit('getInvitationsPerUserMaster')
                }
            })
        },
        getCatalogInvitationTemplates() {
            return new Promise((resolve)=>{
                this.User.getCatalogInvitationTemplates({},(response)=>{
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
        this.getCatalogChannels().then((response) => {
            this.channels = response.channels

            this.getCatalogInvitationTemplates().then((response) => {
                this.templates = response.templates
                this.templates.template = response.templates[response.templates.length-1]

                this.addUser()
            })
        })
    },
    template : `
        <div class="card bg-gradient-primary overflow-hidden animation-fall-down" style="--delay:600ms">
            <div v-if="busy" class="mask position-absolute z-index-1 d-flex justify-content-center align-items-center bg-dark">
                <div class="spinner-grow text-white" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            
            <div class="card-header bg-transparent text-white h3">
                Envía invitación
            </div>


            <div class="accordion" id="accordionExample">
                <div class="accordion-item">
                    <h5 class="accordion-header">
                        <button class="accordion-button fw-sembold text-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            <i class="bi text-xs me-2 bi-funnel-fill"></i> Elige el template y el canal
                        </button>
                    </h5>
                    <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <div class="form-floating mb-3">
                                <select class="form-select mb-3" v-model="invitation.catalog_channel_id" aria-label="">
                                    <option v-for="channel in channels" v-bind:value="channel.catalog_channel_id">
                                        {{ channel.channel }}
                                    </option>
                                </select>
            
                                <label for="floatingSelect">Canal</label>
                            </div>
            
                            <div class="form-floating mb-3">
                                <select class="form-select" v-model="invitation.template" aria-label="">
                                    <option v-for="template in templates" v-bind:value="template">
                                        {{ template.title }}
                                    </option>
                                </select>
                                <label for="floatingSelect">Template</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="invitation.template" class="accordion-item">
                    <h5 class="accordion-header">
                        <button class="accordion-button fw-sembold text-white collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            <i class="bi text-xs me-2 bi-eye"></i>  Previsualización
                        </button>
                    </h5>
                    <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <div v-if="invitation.template">
                                <div v-if="invitation.catalog_channel_id == CHANNELS.WHATSAPP">
                                    <div class="card shadow-none bg-whatsapp">
                                        <div class="card-header bg-dark py-2">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <i class="bi h4 text-white bi-arrow-left"></i>
                                                </div>
                                                <div class="col">
                                                    <div class="avatar mt-2">
                                                        <img class="avatar rounded-circle" src="../../src/img/user/user.png" alt="usuario"/> 
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex justify-content-end">
                                                <span class="bg-success text-white text-md p-3 rounded">
                                                    <span v-html="invitation.template.template"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center text-white fw-semibold">
                                Elige un template para poder previsualizar 
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="invitation.catalog_channel_id && invitation.template" class="accordion-item">
                    <h5 class="accordion-header">
                        <button class="accordion-button fw-sembold text-white collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            <i class="bi text-xs me-2 bi-people"></i> Añade tus contactos
                        </button>
                    </h5>
                    <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                        <div v-if="invitation.catalog_channel_id && invitation.template">
                            <div v-if="invitation.users" class="card-body">
                                <div class="d-flex justify-content-end">
                                    <button :disabled="invitation.users.length >= MAX_USERS_PER_INVITATION" @click="addUser" class="btn btn-light shadow-none">Añadir usuario</button>
                                </div>

                                <ul class="list-group list-group-flush">
                                    <li v-for="(user,index) in invitation.users" class="list-group-item p-0 border-0 bg-transparent">
                                        <div class="row align-items-center">
                                            <div class="form-floating mb-3">
                                                <input type="text" v-model="user.contact" :class="user.contact ? 'is-valid' : 'is-invalid'" class="form-control" :id="index" placeholder="">
                                                <label :for="index">
                                                    <span v-if="invitation.catalog_channel_id == CHANNELS.WHATSAPP"> 
                                                        Número de whatsapp completo
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            <div class="card-footer">
                                <div class="d-grid">
                                    <button :disabled="busy" @click="sendInvitations" class="btn btn-light shadow-none mb-0 mb-0">Enviar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { TafaddViewer } 