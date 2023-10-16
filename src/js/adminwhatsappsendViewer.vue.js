import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.6'

const AdminwhatsappsendViewer = {
    name : 'adminwhatsappsend-viewer',
    data() {
        return {
            UserSupport: new UserSupport,
            campaign: null,
            whatsappsText: null,
            whatsappsList: [],
            whatsappsListSent: [],
        }
    },
    watch : {
        query : {
            handler() {
                this.filterData()
            },
            deep: true
        },
        whatsappsText : {
            handler() {
                if(this.whatsappsText)
                {
                    this.whatsappsList = this.whatsappsText.split(/\r?\n|\r|\n/g)
                }
            },
            deep: true
        }
    },
    methods: {
        filterData() {
            this.campaigns = this.campaignsAux

            this.campaigns = this.campaigns.filter((campaign) => {
                return campaign.title.toLowerCase().includes(this.query.toLowerCase()) 
            })
        },
        goToSendMails(campaign_whatsapp_id) {
            window.location.href = `../../apps/admin-whatsapp/send?cmid=${campaign_whatsapp_id}`
        },
        sendWhatsapp(whatsapp) {
            return new Promise((resolve, reject) => {
                this.UserSupport.sendWhatsapp({whatsapp:whatsapp,campaign_whatsapp_id:this.campaign.campaign_whatsapp_id}, (response) => {
                    if (response.s == 1) {
                        resolve(true)
                    } else {
                        reject()
                    }
                })
            })
        },
        sendWhatsapps(campaign_whatsapp_id) 
        {
            for(let whatsapp of this.whatsappsList)
            {
                this.sendWhatsapp(whatsapp).then(() => {
                    this.whatsappsListSent.push({
                        sent:true,
                        whatsapp:whatsapp,
                    })
                })
            }
        },
        getWhatsAppCampaign(campaign_whatsapp_id) {
            return new Promise((resolve,reject) => {
                this.UserSupport.getWhatsAppCampaign({campaign_whatsapp_id:campaign_whatsapp_id}, (response) => {
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
            this.getWhatsAppCampaign(getParam('cmid')).then((campaign)=>{
                this.campaign = campaign
            })
        }
    },
    template : `
        <div v-if="campaign" class="card mb-3">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        Enviar whatsapp
                    </div>
                    <div class="col-auto">
                        <button
                            @click="sendWhatsapps" 
                            :disabled="!whatsappsList.length > 0" 
                            class="btn btn-primary mb-0">Enviar</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div v-if="whatsappsListSent" class="row mb-3">
                    <div><span class="badge text-secondary p-0">Mensajes enviados {{whatsappsListSent.length}} de {{whatsappsList.length}} </span></div>
                
                    <div v-if="whatsappsListSent" class="col-12">
                        <ul class="list-group">
                            <li v-for="whatsappSent in whatsappsListSent" class="list-group-item">
                                <div class="row">
                                    <div class="col">
                                        {{whatsappSent.whatsapp}}
                                    </div>
                                    <div class="col-auto">
                                        <span v-if="whatsappSent.sent" class="badge bg-success">
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
                        <div v-if="campaign.content">
                            <div v-for="content in campaign.content" class="mb-3">
                                {{content}}
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-4">
                        <div class="form-floating">
                            <textarea v-model="whatsappsText" class="form-control" placeholder="Leave a comment here" id="floatingTextarea2" style="height: 100px"></textarea>
                            <label for="floatingTextarea2">Whatsapps</label>
                        </div>

                        <div v-if="whatsappsList.length > 0" class="row py-3">
                            <div class="col-12">
                                <div><label>Lista de Whatsapps</label></div>
                                <span v-for="_whatsapp in whatsappsList">
                                    <span 
                                        class="badge me-2"
                                        :class="_whatsapp.isValidPhone() ? 'bg-success' : 'bg-danger'">
                                        {{_whatsapp}}
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

export { AdminwhatsappsendViewer } 