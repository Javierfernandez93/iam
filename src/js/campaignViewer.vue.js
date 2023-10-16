import { User } from '../../src/js/user.module.js?v=2.6.5'   

const CampaignViewer = {
    name : 'campaign-viewer',
    props : [],
    emits : [],
    data() {
        return {
            User: new User,
            query: null,
            campaignsAux: null,
            campaigns: null,
            STATUS : {
                UNPUBLISHED: 0,
                PUBLISHED: 1
            }
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
        filterData: function () {
            this.campaigns = this.campaignsAux

            this.campaigns = this.campaigns.filter((campaign) => {
                return campaign.name.toLowerCase().includes(this.query.toLowerCase()) 
            })
        },
        publishCampaign: function (campaign) {
            this.User.publishCampaign({campaign_banner_per_user_id:campaign.campaign_banner_per_user_id}, (response) => {
                if (response.s == 1) {
                    campaign.status = response.status
                }
            })
        },
        unPublishCampaign: function (campaign) {
            this.User.unPublishCampaign({campaign_banner_per_user_id:campaign.campaign_banner_per_user_id}, (response) => {
                if (response.s == 1) {
                    campaign.status = response.status
                }
            })
        },
        deleteCampaign: function (campaign_banner_per_user_id) {
            this.User.deleteCampaign({campaign_banner_per_user_id:campaign_banner_per_user_id}, (response) => {
                if (response.s == 1) {
                    this._getCampaigns()
                }
            })
        },
        getCampaigns: function () {
            return new Promise((resolve,reject) => {
                this.User.getCampaigns({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.campaigns)
                    }

                    reject()
                })
            })
        },
        goToAddCampaign: function()
        {
            window.location.href = '../../apps/banner/addCampaign'
        },
        goToEditCampaign: function(campaign_banner_per_user)
        {
            window.location.href = `../../apps/banner/editCampaign?cbpid=${campaign_banner_per_user}`
        },
        goToEditBanners: function(campaign_banner_per_user)
        {
            window.location.href = `../../apps/banner/addBanner?cbpid=${campaign_banner_per_user}`
        },
        _getCampaigns()
        {
            this.getCampaigns().then((campaigns) => {
                this.campaignsAux = campaigns
                this.campaigns = this.campaignsAux
            }).catch(() => this.campaigns = false)
        }
    },
    mounted() {
        this._getCampaigns()
    },
    template : `
        <div>
            <div class="card mb-3 bg-dark">
                <div class="card-body text-white">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="badge fs-6 bg-gradient-primary"><i class="bi bi-funnel-fill"></i></span>
                        </div>
                        <div class="col">
                            <span v-if="campaigns" class="badge p-0 text-light">Total {{campaigns.length}}</span>
                            <div class="fw-semibold">Campañas</div>
                        </div>
                        <div class="col-auto">
                            <button @click="goToAddCampaign" class="btn btn-light m-0">Añadir campaña</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-3">
                <div class="input-group input-group-lg input-group-merge">
                    <input
                        v-model="query"
                        :autofocus="true"
                        type="text" class="form-control border-0 shadow-lg" placeholder="Buscar campaña..."/>
                </div>
            </div>

            <div v-if="campaigns">
                <div v-for="campaign in campaigns" class="card mb-3">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="badge bg-gradient-primary">
                                    {{ campaign.name.getAcronime() }}
                                </span>
                            </div>
                            <div class="col">                            
                                <span class="badge"
                                    v-text="campaign.status ? 'Publicada' : 'Sin pubicar'"
                                    :class="campaign.status ? 'bg-success' : 'bg-light'"></span>
                                
                                <div class="fs-5 fw-semibold">{{campaign.name}}</div>
                                <span class="badge p-0 text-secondary opacity-50 rounded-0 border-primary border-end me-2 pe-2">Creada hace {{campaign.create_date.timeSince()}}</span>
                                <span class="badge p-0 text-secondary opacity-50">Banners {{campaign.amount_of_banner}}</span>
                            </div>
                            <div class="col-auto text-center bg-light border-radius-xl px-3 py-2 me-2">
                                <span class="badge p-0 text-secondary">Impresiones</span>
                                <div class="fs-5 fw-semibold">{{campaign.prints.numberFormat(0)}} / 300</div>
                            </div>
                            <div class="col-auto text-center bg-light border-radius-xl px-3 py-2 me-2">
                                <span class="badge p-0 text-secondary">Clicks</span>
                                <div class="fs-5 fw-semibold">{{campaign.clicks.numberFormat(0)}} / 300</div>
                            </div>
                            <div class="col-auto">
                                <div class="dropdown">
                                    <a class="btn btn-primary dropdown-toggle m-0" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false"></a>
                                
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                        <li><button v-if="campaign.status == STATUS.PUBLISHED" @click="goToEditCampaign(campaign.campaign_banner_per_user_id)" class="dropdown-item">Editar campaña</button></li>
                                        <li><button v-if="campaign.status == STATUS.PUBLISHED" @click="goToEditBanners(campaign.campaign_banner_per_user_id)" class="dropdown-item">Editar banners</button></li>
                                        <li><button v-if="campaign.status == STATUS.PUBLISHED" @click="unPublishCampaign(campaign)" class="dropdown-item">Pausar</button></li>
                                        <li><button v-if="campaign.status == STATUS.UNPUBLISHED" @click="publishCampaign(campaign)" class="dropdown-item">Activar</button></li>
                                        <li><button @click="deleteCampaign(campaign.campaign_banner_per_user_id)" class="dropdown-item">Eliminar</button></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else-if="campaigns == false" class="alert alert-light">    
                No tienes campañas
            </div>
        </div>
    `,
}

export { CampaignViewer } 