import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.5'

const AdminemailsendViewer = {
    name : 'adminemailsend-viewer',
    props : [],
    emits : [],
    data() {
        return {
            UserSupport: new UserSupport,
            campaign: null,
            emailsText: null,
            emailsList: [],
            emailsListSent: [],
        }
    },
    watch : {
        query : {
            handler() {
                this.filterData()
            },
            deep: true
        },
        emailsText : {
            handler() {
                if(this.emailsText)
                {
                    this.emailsList = this.emailsText.split(/\r?\n|\r|\n/g)
                }
            },
            deep: true
        }
    },
    methods: {
        filterData: function () {
            this.campaigns = this.campaignsAux

            this.campaigns = this.campaigns.filter((campaign) => {
                return campaign.title.toLowerCase().includes(this.query.toLowerCase()) 
            })
        },
        goToSendMails: function (campaign_email_id) {
            window.location.href = `../../apps/admin-email/send?cmid=${campaign_email_id}`
        },
        sendEmail: function (email) {
            return new Promise((resolve, reject) => {
                this.UserSupport.sendEmail({email:email,campaign_email_id:this.campaign.campaign_email_id}, (response) => {
                    if (response.s == 1) {
                        resolve(true)
                    } else {
                        reject()
                    }
                })
            })
        },
        sendEmails: function (campaign_email_id) {
            this.emailsList.forEach(email => {
                this.sendEmail(email).then(() => {
                    this.emailsListSent.push({
                        sent:true,
                        email:email,
                    })
                })
            });
        },
        getEmailCampaign: function (campaign_email_id) {
            return new Promise((resolve,reject) => {
                this.UserSupport.getEmailCampaign({campaign_email_id:campaign_email_id}, (response) => {
                    if (response.s == 1) {
                        resolve(response.campaign)
                    }

                    reject()
                })
            })
        },
    },
    mounted() {
        if(getParam('cmid'))
        {
            this.getEmailCampaign(getParam('cmid')).then((campaign)=>{
                this.campaign = campaign
            })
        }
    },
    template : `
        <div v-if="campaign" class="card mb-3">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        Enviar correo
                    </div>
                    <div class="col-auto">
                        <button
                            @click="sendEmails" 
                            :disabled="!emailsList.length > 0" 
                            class="btn btn-primary mb-0">Enviar</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div v-if="emailsListSent" class="row mb-3">
                    <div><span class="badge text-secondary p-0">Mensajes enviados {{emailsListSent.length}} de {{emailsList.length}} </span></div>
                
                    <div v-if="emailsListSent" class="col-12">
                        <ul class="list-group">
                            <li v-for="emailSent in emailsListSent" class="list-group-item">
                                <div class="row">
                                    <div class="col">
                                        {{emailSent.email}}
                                    </div>
                                    <div class="col-auto">
                                        <span v-if="emailSent.sent" class="badge bg-success">
                                            Enviado
                                        </span>
                                        <span v-else class="badge bg-danger">
                                            Error al enviar
                                        </span>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-xl-8">
                        <div class="row">
                            <div class="col fs-5 fw-sembold">
                                {{campaign.title}}
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-printer"></i>
                            </div>
                        </div>
                        <div class="row py-3 align-items-center">
                            <div class="col-auto">
                                <div class="avatar">
                                    <img src="../../src/img/user.webp" class="avatar rounded-circle shadow p-2">
                                </div>
                            </div>
                            <div class="col">
                                <div>Usuario < usuario@gmail.com ></div>
                                <div class="text-xs">para mí</div>
                            </div>
                            <div class="col-auto">
                                (Justo ahora)
                            </div>
                        </div>
                        <div>
                            <span v-html="campaign.html"></span>
                        </div>
                    </div>
                    <div class="col-12 col-xl-4">
                        <div class="form-floating">
                            <textarea v-model="emailsText" class="form-control" placeholder="Leave a comment here" id="floatingTextarea2" style="height: 100px"></textarea>
                            <label for="floatingTextarea2">Correos</label>
                        </div>

                        <div v-if="emailsList.length > 0" class="row py-3">
                            <div class="col-12">
                                <div><label>Lista de correos</label></div>
                                <span v-for="_email in emailsList">
                                    <span 
                                        class="badge me-2"
                                        :class="_email.isValidMail() ? 'bg-success' : 'bg-danger'">
                                        {{_email}}
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { AdminemailsendViewer } 