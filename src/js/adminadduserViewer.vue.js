import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.6'   

const AdminadduserViewer = {
    name : 'adminadduser-viewer',
    data() {
        return {
            UserSupport: new UserSupport,
            filled: false,
            feedback: null,
            catalogCampaigns: null,
            countries: {},
            user: {
                names: null,
                password: null,
                email: null,
                phone: null,
                catalog_campaign_id: 2,
                country_id: 159,
                user_account: {
                    landing: null,
                },
                referral: {
                    user_login_id: null
                }
            },
        }
    },
    watch: {
        user: {
            handler() {
                this.filled = this.user.names != null && this.user.email != null && this.user.phone != null && this.user.password != null
            },
            deep: true
        }
    },
    methods: {
        saveUser() {
            this.feedback = null

            this.UserSupport.saveUser({user:this.user}, (response) => {
                if (response.s == 1) {
                    this.$refs.button.innerText = "Actualizado"
                } else if (response.r == 'MAIL_ALREADY_EXISTS') {
                    this.feedback = 'El correo proporcionado ya está registrado'
                } else if (response.r == 'USER_NAME_EXIST') {
                    this.feedback = 'El nombre de usuario ya está registrado'
                }
            })
        },
        getAdminReferralProfile: _debounce((self) => {
            self.UserSupport.getAdminReferralProfile({referral_id:self.user.referral.user_login_id}, (response) => {
                if (response.s == 1) {
                    self.user.referral = {...self.user.referral,...response.profile} 
                }
            })
        },500),
        getCountries() {
            this.UserSupport.getCountries({  }, (response) => {
                if (response.s == 1) {
                    Object.assign(this.countries, response.countries)
                }
            })
        },
        getCatalogCampaigns() {
            this.UserSupport.getCatalogCampaigns({  }, (response) => {
                if (response.s == 1) {
                    this.catalogCampaigns = response.catalogCampaigns
                }
            })
        },
    },
    mounted() {
        $(this.$refs.phone).mask('(00) 0000-0000');

        this.getCountries()
        this.getCatalogCampaigns()
    },
    template : `
        <div class="card">
            <div class="card-header"> 
                <div class="row justify-content-center"> 
                    <div class="col-12 col-xl"> 
                        <div class="fs-4 fw-sembold text-primary">Añadir usuario</div>
                    </div>
                    <div class="col-12 col-xl-auto"> 
                        <button 
                            :disabled="!filled"
                            ref="button"
                            type="submit" class="btn shadow-none mb-0 btn-primary" @click="saveUser">Guardar usuario</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-12 col-xl-6">
                        <label>Usuario</label>
                        <input 
                            :autofocus="true"
                            :class="user.names ? 'is-valid' : ''"
                            @keydown.enter.exact.prevent="$refs.email.focus()"
                            v-model="user.names"
                            ref="names"
                            type="text" class="form-control" placeholder="Nombre(s)">
                    </div>
                    <div class="col-12 col-xl-6">
                        <label>Correo</label>
                        <input 
                            v-model="user.email"
                            :class="user.email ? 'is-valid' : ''"
                            @keydown.enter.exact.prevent="$refs.password.focus()"
                            ref="email"
                            type="text" class="form-control" placeholder="Email">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12 col-xl">
                        <label>Nombre de usuario</label>
                        <input 
                            v-model="user.user_account.landing"
                            :class="user.user_account.landing ? 'is-valid' : ''"
                            @keydown.space.prevent
                            ref="landing"
                            type="text" class="form-control" placeholder="Nombre de usuario">
                    </div>
                    <div class="col-12 col-xl">
                        <label>Contraseña</label>
                        <input 
                            v-model="user.password"
                            :class="user.password ? 'is-valid' : ''"
                            @keydown.enter.exact.prevent="$refs.referral.focus()"
                            ref="password"
                            type="text" class="form-control" placeholder="Password">
                    </div>
                    
                    <div class="col-12 col-xl">
                        <label>Campaña</label>
                        <select class="form-select" v-model="user.catalog_campaign_id" aria-label="Campaña">
                            <option v-for="campaign in catalogCampaigns" v-bind:value="campaign.catalog_campaign_id">
                                {{ campaign.campaign }} 
                            </option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12 col-xl-6">
                        <label>Referido por</label>
                        <input 
                            v-model="user.referral.user_login_id"
                            :class="user.referral.user_login_id ? 'is-valid' : ''"
                            @keypress="getAdminReferralProfile(this)"
                            ref="number"
                            type="text" class="form-control" placeholder="Enter para buscar">

                        <div v-if="user.referral.names" class="alert mb-0 mt-2 alert-info text-center text-white">
                            {{user.referral.names}}
                        </div>
                    </div>

                    <div class="col-12 col-xl-6">
                        <label>Teléfono</label>
                        <div class="row">
                            <div class="col-auto">
                                <select class="form-select" v-model="user.country_id" aria-label="Selecciona tu país">
                                    <option>Selecciona tu país</option>
                                    <option v-for="country in countries" v-bind:value="country.country_id">
                                        {{ country.nicename }} <span v-if="country.phone_code > 0">+ {{ country.phone_code }}</span>
                                    </option>
                                </select>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <input 
                                        :class="user.phone ? 'is-valid' : ''"
                                        ref="phone"
                                        type="text" ref="phone" v-model="user.phone" class="form-control" @keydown.enter.exact.prevent="saveUser" placeholder="Teléfono" aria-label="Teléfono" aria-describedby="basic-addon1">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    v-if="feedback" 
                    class="alert alert-secondary text-white text-center">
                    {{feedback}}
                </div>
            </div>
        </div>
    `,
}

export { AdminadduserViewer } 