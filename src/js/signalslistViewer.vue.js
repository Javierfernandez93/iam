import { User } from '../../src/js/user.module.js?v=2.6.5'   

const SignalslistViewer = {
    name : 'signalslist-viewer',
    emit : ['openCanvas'],
    data() {
        return {
            User : new User,
            query : null,
            singalsProvidersAux : null,
            singalsProviders : null,
            CATALOG_SINGAL_PROVIDER: {
                SEMI_COPY: 1,
                PAMMY: 2,
            }
        }
    },
    watch: {
        query : {
            handler() {
                this.filterData()
            },
            deep: true
        }
    },
    methods: {
        filterData() {
            this.singalsProviders = this.singalsProvidersAux 
            this.singalsProviders = this.singalsProviders.filter((singalsProvider)=>{
                return singalsProvider.name.toLowerCase().includes(this.query.toLowerCase())
            })
        },
        getSignalsLists(catalog_signal_provider_id) {
            return new Promise((resolve, reject) => {
                this.User.getSignalsLists({catalog_signal_provider_id:catalog_signal_provider_id},(response)=>{
                    if(response.s == 1)
                    {
                        resolve(response.singalsProviders)  
                    }

                    reject()
                })
            })
        },
        enableCopyAccount(user_trading_account_provider_id,user_trading_account_id)
        {
            this.User.enableCopyAccount({user_trading_account_provider_id:user_trading_account_provider_id,user_trading_account_id:user_trading_account_id},(response)=>{
                if(response.s == 1)
                {
                    
                }
            })
        },
        getTradingAccounts(provider) {
            this.User.getTradingAccounts({catalog_trading_account_id:provider.catalog_trading_account_id},(response)=>{
                if(response.s == 1)
                {
                    provider.accounts = response.accounts
                }
            })
        },
        followSingal(singalsProvider) {
            this.User.followSingal({signal_provider_id:singalsProvider.signal_provider_id},(response)=>{
                if(response.s == 1)
                {
                    singalsProvider.followers += 1
                    singalsProvider.isFollowing = true
                }
            })
        },
        unFollowSingal(singalsProvider) {
            this.User.unFollowSingal({signal_provider_id:singalsProvider.signal_provider_id},(response)=>{
                if(response.s == 1)
                {
                    singalsProvider.followers -= 1
                    singalsProvider.isFollowing = false
                }
            })
        },
        getSignalsListsMaster(catalog_signal_provider_id) {
            this.getSignalsLists(catalog_signal_provider_id).then((singalsProviders)=>{
                this.singalsProvidersAux = singalsProviders
                this.singalsProviders = singalsProviders
            }).catch((err)=>{
                this.singalsProviders = false
                this.singalsProvidersAux = false
            })
        }
    },
    mounted() {
        this.getSignalsListsMaster(this.CATALOG_SINGAL_PROVIDER.SEMI_COPY)
    },
    template : `
        <div v-if="singalsProviders">
        <div class="row align-items-center animation-fall-down" style="--delay:500ms">
                <div class="col-12 col-xl">
                    <h2 class="mb-3">Conecta con tu proveedor de operativa</h2>
                    <div class="mb-3">
                        Ahora puedes seguir las señales <strong>de tu proveedor</strong> es una forma sencilla y rápida de copiar señales de los proovedores, sólo necesitas seguir la señal y comenzar a recibirlas en tu Telegram.
                    </div>
                </div>
                <div class="col-12 col-xl-auto order-1">
                    <lottie-player src="../../src/files/json/proveedor.json" background="transparent"  speed="1"  mode=“normal” loop autoplay style="width: 400px; height: 400px;"></lottie-player>
                </div>
            </div>
            
            <div class="d-flex justify-content-center my-3 animation-fall-down" style="--delay:700ms">
                <span class="my-5 border-top border-secondary w-50 d-flex"></span>
            </div>

            <div v-if="busy" class="justify-content-center d-flex">
                <div class="spinner-grow text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            
            
            <div v-if="singalsProviders" class="animation-fall-down" style="--delay:900ms">
                <div class="h3">Proveedores de operativa</div>
                <div class="row justify-content-center mb-5">
                    <div class="col-12 col-xl-4">
                        <input v-model="query" type="text" class="form-control form-control-lg" placeholder="Buscar proveedor"/>
                    </div>
                </div>
                <div v-for="provider in singalsProviders" class="card shadow-blur blur over-card-blur card-body mb-3 animation-fall-right" style="--delay:900ms">
                    <div class="row align-items-center">
                        <div class="col-12 col-xl-auto">
                            <div v-if="provider.image">
                                <img :src="provider.image" class="avatar"/>
                            </div>
                            <span v-else class="avatar bg-warning">
                                {{provider.name.getAcronime()}}
                            </span>
                        </div>
                        <div class="col-12 col-xl">
                            <div>
                                <span v-if="provider.copy" class="badge me-2 bg-primary">Copy</span>
                                <span v-if="provider.type" class="badge bg-primary">{{provider.type}}</span>
                                <span class="badge ms-2 bg-success" v-if="provider.isFollowing"><i class="bi bi-lightning"></i> Following</span>
                            </div>
                            <span class="h3 fw-semibold text-dark">
                                {{provider.name}}
                            </span>
                        </div>
                        <div class="col-12 text-end col-xl-auto">
                            <span class="text-xs text-secondary">followers</span>
                            <div class="h3">
                                {{provider.followers}}
                            </div>
                        </div>
                        <div class="col-12 col-xl-auto">
                            <div class="d-grid">
                                <button v-if="!provider.isFollowing" @click="followSingal(provider)" class="btn btn-success shadow-none mb-0">Seguir señales</button>
                                <button v-else @click="unFollowSingal(provider)" class="btn btn-secondary shadow-none mb-0">Dejar de seguir</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else-if="channels == false">
            sin canales
        </div>
    `,
}

export { SignalslistViewer } 