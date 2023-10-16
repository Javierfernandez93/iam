import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.5'

const AdmintelegramsendViewer = {
    name : 'admintelegramsend-viewer',
    data() {
        return {
            UserSupport: new UserSupport,
            campaign: null,
            telegramsText: null,
            telegramsList: [],
            telegramsListSent: [],
        }
    },
    watch : {
        query : {
            handler() {
                this.filterData()
            },
            deep: true
        },
        telegramsText : {
            handler() {
                if(this.telegramsText)
                {
                    this.telegramsList = this.telegramsText.split(/\r?\n|\r|\n/g)
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
        goToSendMails(campaign_telegram_id) {
            window.location.href = `../../apps/admin-telegram/send?cmid=${campaign_telegram_id}`
        },
        sendTelegram(telegram) {
            return new Promise((resolve, reject) => {
                this.UserSupport.sendTelegramToUser({telegram:telegram,campaign_telegram_id:this.campaign.campaign_telegram_id}, (response) => {
                    if (response.s == 1) {
                        resolve(true)
                    } else {
                        reject()
                    }
                })
            })
        },
        sendTelegrams(campaign_telegram_id) 
        {
            for(let telegram of this.telegramsList)
            {
                this.sendTelegram(telegram).then(() => {
                    this.telegramsListSent.push({
                        sent:true,
                        telegram:telegram,
                    })
                })
            }
        },
        getTelegramCampaign(campaign_telegram_id) {
            return new Promise((resolve,reject) => {
                this.UserSupport.getTelegramCampaign({campaign_telegram_id:campaign_telegram_id}, (response) => {
                    if (response.s == 1) {
                        resolve(response.campaign)
                    }

                    reject()
                })
            })
        },
        getTelegramUsersByCampaigns(catalog_campaign_ids_in) {
            return new Promise((resolve,reject) => {
                this.UserSupport.getTelegramUsersByCampaigns({catalog_campaign_ids_in:catalog_campaign_ids_in}, (response) => {
                    if (response.s == 1) {
                        resolve(response.users)
                    }

                    reject()
                })
            })
        },
    },
    mounted() {
        if(getParam('cmid'))
        {
            this.getTelegramCampaign(getParam('cmid')).then((campaign)=>{
                this.campaign = campaign

                this.getTelegramUsersByCampaigns(campaign.catalog_campaign_ids_in).then((users)=>{
                    this.telegramsText = users
                })
            })
        }
    },
    template : `
        <div v-if="campaign" class="card mb-3">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        Enviar telegram
                    </div>
                    <div class="col-auto">
                        <button
                            @click="sendTelegrams" 
                            :disabled="!telegramsList.length > 0" 
                            class="btn btn-primary mb-0">Enviar</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div v-if="telegramsListSent" class="row mb-3">
                    <div><span class="badge text-secondary p-0">Mensajes enviados {{telegramsListSent.length}} de {{telegramsList.length}} </span></div>
                
                    <div v-if="telegramsListSent" class="col-12">
                        <ul class="list-group">
                            <li v-for="telegramSent in telegramsListSent" class="list-group-item">
                                <div class="row">
                                    <div class="col">
                                        {{telegramSent.telegram}}
                                    </div>
                                    <div class="col-auto">
                                        <span v-if="telegramSent.sent" class="badge bg-success">
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
                            <textarea v-model="telegramsText" class="form-control" placeholder="Leave a comment here" id="floatingTextarea2" style="height: 100px"></textarea>
                            <label for="floatingTextarea2">Telegrams</label>
                        </div>

                        <div v-if="telegramsList.length > 0" class="row py-3">
                            <div class="col-12">
                                <div><label>Lista de Telegrams</label></div>
                                <span v-for="_telegram in telegramsList">
                                    <span class="badge me-2 bg-success">
                                        {{_telegram}}
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

export { AdmintelegramsendViewer } 