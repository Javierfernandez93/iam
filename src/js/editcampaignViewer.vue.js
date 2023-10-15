import { User } from '../../src/js/user.module.js?v=2.6.4'   

const EditcampaignViewer = {
    name : 'editcampaign-viewer',
    props : ['viewcountries'],
    emits : ['toggleviewcountries'],
    data() {
        return {
            User: new User,
            isCampaignFilled: false,
            campaign: {
                countries: false,
                campaign_banner_per_user_id: null,
                name: null,
                description: null,
                saved: false
            },
        }
    },
    watch : {
        campaign: {
            handler() {
                this.isCampaignFilled = this.campaign.name != null && this.campaign.description != null
            },
            deep: true
        }
    },
    methods: {
        addCountry: function (country) 
        {
            this.campaign.countries.push(country);
        },
        deleteCountry: function (country) 
        {
            if(this.campaign.countries.length > 0)
            {
                this.campaign.countries = this.campaign.countries.filter((_country) => {
                    return _country.country_id != country.country_id
                })
            }

            country.added = false
        },
        addBanner: function (campaign) {
            const { campaign_banner_per_user_id } = campaign
            window.location.href  = `../../apps/banner/addBanner?cbpid=${campaign_banner_per_user_id}`
        },
        toggleViewCountries: function () {
            this.$emit('toggleviewcountries')
        },
        updateCampaign: function () {
            this.User.updateCampaign(this.campaign, (response) => {
                if (response.s == 1) {
                    this.$refs.save.innerText = '¡Campaña actualizada!'
                    this.campaign.campaign_banner_per_user_id = response.campaign_banner_per_user_id
                    this.campaign.saved = true
                }
            })
        },
        getCampaign: function () {
            return new Promise((resolve) => {
                this.User.getCampaign({campaign_banner_per_user_id:this.campaign.campaign_banner_per_user_id}, (response) => {
                    if (response.s == 1) {
                        resolve(response.campaign)
                    }
                })
            })
        },
    },
    mounted() {
        if(getParam('cbpid'))
        {
            this.campaign.campaign_banner_per_user_id = getParam('cbpid')

            this.getCampaign().then((campaign) => {
                this.campaign = campaign
            }).catch(() => this.campaign = false)
        }
    },
    template : `
        <div class="card mb-3">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-auto">  
                        <span class="badge bg-gradient-primary"><i class="bi bi-pencil-square fs-4"></i></span>
                    </div>
                    
                    <div class="col">
                        <span class="badge text-secondary p-0">Ingresa la información </span>
                        <div class="text-primary fw-semibold text-uppercase">de tu campaña</div>
                    </div>

                    <div class="col-auto">
                        <button 
                            :disabled="!isCampaignFilled || campaign.saved"
                            ref="save"
                            @click="updateCampaign" class="btn btn-success m-0">Actualizar campaña</button>

                        <button 
                            v-if="campaign.saved"
                            @click="addBanner(campaign)" class="btn ms-1 btn-primary m-0">Añadir banner a campaña</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <span class="badge p-0 text-secondary mb-3"><i class="bi bi-info-square"></i> Información básica</span>
                <div class="row">
                    <div class="col-12 col-xl-6 form-floating mb-3">
                        <input 
                            v-model="campaign.name"
                            :class="campaign.name ? 'is-valid' : ''"
                            @keydown.enter.prevent="$refs.description.focus()"
                            ref="name"
                            type="text" class="form-control" id="floatingInput" placeholder="name@example.com">
                        <label for="floatingInput">Nombre de campaña address</label>
                    </div>
                    <div class="col-12 col-xl-6 form-floating">
                        <input 
                            v-model="campaign.description"
                            :class="campaign.description ? 'is-valid' : ''"
                            @keydown.enter.prevent=""
                            ref="description"
                            type="text" class="form-control" id="floatingPassword" placeholder="Password">
                        <label for="floatingPassword">Descripción</label>
                    </div>
                </div>

                <span class="badge p-0 text-secondary"><i class="bi bi-globe"></i> Segmentación por paises</span>
                <div class="row align-items-center">
                    <div class="col">
                        <div v-if="campaign.countries">
                            <span v-for="country in campaign.countries" class="badge border border-primary text-primary me-2">
                                <i class="bi bi-globe"></i> {{country.nicename}}
                                <i @click="deleteCountry(country)" class="bi bi-trash cursor-pointer"></i>
                            </span>
                        </div>
                        <div v-else class="fw-semibold">
                            Aún no has hecho segmentación por país
                        </div>
                    </div>
                    <div class="col-auto">
                        <button @click="toggleViewCountries" class="btn btn-primary"
                            v-text="viewcountries ? 'Cerrar lista de paises' : 'Añadir país'"></button>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { EditcampaignViewer } 