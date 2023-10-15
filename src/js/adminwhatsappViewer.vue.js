import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.4'

const AdminwhatsappViewer = {
    name : 'adminwhatsapp-viewer',
    data() {
        return {
            UserSupport: new UserSupport,
            campaigns: null,
            campaignsAux: null,
            query: null
        }
    },
    watch : {
        query : {
            handler() {
                this.filterData()
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
        goToViewStats(campaign_whatsapp_id) {
            window.location.href = `../../apps/admin-whatsapp/stats?cmid=${campaign_whatsapp_id}`
        },
        goToAddCampaign() {
            window.location.href = '../../apps/admin-whatsapp/add'
        },
        getWhatsAppCampaigns() {
            return new Promise((resolve,reject) => {
                this.UserSupport.getWhatsAppCampaigns({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.campaigns)
                    }

                    reject()
                })
            })
        },
    },
    mounted() {
        this.getWhatsAppCampaigns().then((campaigns)=>{
            this.campaignsAux = campaigns
            this.campaigns = campaigns
        })
    },
    template : `
        <div class="card mb-3">
            <div class="input-group input-group-lg input-group-merge">
                <input
                    v-model="query"
                    :autofocus="true"
                    @keydown.enter.exact.prevent="search"
                    type="text" class="form-control border-0 shadow-lg" placeholder="Buscar campaña..."/>
                <button @click="goToAddCampaign" class="btn btn-primary m-0">Crear campaña</button>
            </div>
        </div>

        <div v-if="campaigns">
            <div class="card">
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Titulo</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Fecha de creación</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="campaign in campaigns">
                                    <td class="align-middle text-center text-xs">
                                        <span>{{campaign.campaign_whatsapp_id}}</span>
                                    </td>
                                    <td class="align-middle text-center text-sm">{{campaign.title}}</td>
                                    <td class="align-middle text-center text-sm">{{campaign.create_date.formatFullDate()}}</td>
                                    <td class="align-middle text-center text-sm">
                                        <div class="dropdown">
                                            <button class="btn btn-outline-primary px-3 btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            
                                            </button>

                                            <ul class="dropdown-menu">
                                                <li><button  
                                                    @click="goToSendMails(campaign.campaign_whatsapp_id)"
                                                    class="dropdown-item">Enviar whatsapp masivos</button>
                                                </li>
                                                <li><button  
                                                    @click="goToViewStats(campaign.campaign_whatsapp_id)"
                                                    class="dropdown-item">Ver stats</button>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div v-else-if="buys == false" class="alert alert-light fw-semibold text-center">    
            No tenemos compras aún 
        </div>
    `,
}

export { AdminwhatsappViewer } 