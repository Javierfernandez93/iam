import { User } from '../../src/js/user.module.js?v=2.6.6'   

const PammyViewer = {
    name : 'pammy-viewer',
    data() {
        return {
            User : new User,
            wallet : {
                amount: 0
            },
            busy : null,
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
            var today = new Date();
            
            var lastDayOfMonth = new Date(today.getFullYear(), today.getMonth()+1, 0).toLocaleTimeString("es-ES",{ hour: 'numeric', minute: 'numeric', hour12: true, day: 'numeric', month: 'long'});

            let alert = alertCtrl.create({
                size: "modal-md",
                bgColor: "",
                title: `<div class="">Importante antes de continuar</div>`,
                subTitle: `
                    <div class="alert alert-danger text-white text-center">
                        <strong>Importante</strong>
                        Por favor lee las condiciones de la conexión antes de continuar:
                    </div>
                    <div class="text-center text-dark lead fw-semibold">
                        <div class="mb-3">
                            Al habilitar <b>PAMMyTrading</b> para este proveedor tu cuenta tendrá que estár ligada a hasta <b>${lastDayOfMonth}</b>
                        </div>
                        <div class="mb-3">
                            Después de haber finalizado la fecha de bloqueo podrás cambiar de proovedor o elegir el mismo si así lo requieres.
                        </div>
                        <div class="mb-3">
                            Ten en cuenta que PAMMy trading sólo está disponible para habilitarse 1 vez al mes para el proveedor que elijas.
                        </div>
                    </div>
                `,
                buttons: [
                    {
                        text: "Sí habilitar",
                        class: 'btn-primary btn-lg rounded-1 w-100',
                        role: "cancel",
                        handler: (data) => {
                            this.busy = true

                            alert.modal.dismiss();

                            this.User.enableCopyAccount({user_trading_account_provider_id:user_trading_account_provider_id,user_trading_account_id:user_trading_account_id},(response)=>{
                                this.busy = false
                                if(response.s == 1)
                                {
                                    alertInfo({
                                        icon: '<i class="bi bi-ui-checks"></i>',
                                        message: `Se ha habilitado PAMMyTrading para este proveedor`,
                                        _class:'bg-gradient-success text-white'
                                    })
                                } else if(response.r == 'MAX_CONNECTIONS_AUTOCOPY_REACHED') {
                                    alertInfo({
                                        icon: '<i class="bi bi-x"></i>',
                                        message: `Alcanzaste el número máximo de cuentas conectas a PAMMyTrading`,
                                        _class:'bg-gradient-danger text-white'
                                    })
                                }
                            })
                        },
                    },
                ],
            })

            alertCtrl.present(alert.modal);
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
                    singalsProvider.isFollowing = true
                }
            })
        },
        unFollowSingal(singalsProvider) {
            this.User.unFollowSingal({signal_provider_id:singalsProvider.signal_provider_id},(response)=>{
                if(response.s == 1)
                {
                    singalsProvider.isFollowing = false
                }
            })
        },
        getEwalletBalance() {
            this.User.getEwalletBalance({},(response)=>{
                if(response.s == 1)
                {
                    this.wallet.amount = response.amount
                }
            })
        },
        getSignalsListsMaster(catalog_signal_provider_id) {
            this.getSignalsLists(catalog_signal_provider_id).then((singalsProviders)=>{
                this.singalsProvidersAux = singalsProviders
                this.singalsProviders = singalsProviders
            }).catch((err)=>{
                this.singalsProviders = false
            })
        }
    },
    mounted() {
        this.getEwalletBalance()
        this.getSignalsListsMaster(this.CATALOG_SINGAL_PROVIDER.PAMMY)
    },
    template : `
        <div v-if="singalsProviders">
            <div class="row align-items-center animation-fall-down" style="--delay:500ms">
                <div class="col-12 col-xl">
                    <h2 class="mb-3">Conecta con PAMMyTrading</h2>
                    <div class="mb-3">
                        El <strong>copytrading</strong> es una forma de inversión en los mercados financieros que permite a los inversores copiar o <strong>replicar automáticamente las operaciones de otros inversores</strong> más experimentados y exitosos. También se conoce como "trading social" o "trading espejo".
                    </div>
                </div>
                <div class="col-12 col-xl-auto order-1">
                    <lottie-player src="../../src/files/json/pammy.json" background="transparent"  speed="1"  mode=“normal” loop autoplay style="width: 400px; height: 400px;"></lottie-player>
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
                <div class="h3">Proveedores PAMMyTrading</div>
                <div class="row justify-content-center mb-5">
                    <div class="col-12 col-xl-4">
                        <input v-model="query" type="text" class="form-control form-control-lg" placeholder="Buscar proveedor pammytrading"/>
                    </div>
                </div>
                <div v-for="provider in singalsProviders" class="card over-card-blur shadow-blur blur card-body mb-3">
                    <div class="row align-items-center">
                        <div class="col-12 col-xl-auto">
                            <span class="avatar bg-warning">
                                {{provider.name.getAcronime()}}
                            </span>
                        </div>
                        <div class="col-12 col-xl">
                            <div>
                                <span v-if="provider.copy" class="badge text-xs me-2 bg-primary">Copy</span>
                                <span v-if="provider.type" class="badge text-xs me-2 bg-primary">{{provider.type}}</span>
                                <span v-if="provider.isSuscribed" class="badge text-xs me-2 bg-success">PAMMyTrading habilitado</span>
                            </div>
                            <span class="h3 fw-semibold text-dark">
                                {{provider.name}}
                            </span>
                        </div>
                        <div class="col-12 col-xl-auto">
                            <div class="d-grid mt-1" v-if="provider.copy">
                                <button @click="provider.viewDescription = !provider.viewDescription" v-text="provider.viewDescription ? 'Ocultar info':'Ver más info'" class="btn btn-info mb-1 shadow-none"></button>
                                <button :disabled="provider.isSuscribed" @click="getTradingAccounts(provider)" class="btn btn-primary shadow-none mb-0">Habilitar PAMMyTrading</button>
                            </div>
                        </div>
                    </div>
                    <div v-if="provider.viewDescription" class="card-body">
                        <div v-if="provider.viewDescription" class="row align-items-center mt-3">
                            <div class="card card-body shadow shadow-blur">
                                <span v-html="provider.description"></span>
                            </div>
                        </div>
                    </div>
                    <div v-if="provider.accounts" class="row align-items-center mt-3">
                        <div class="col-12">
                            <ul class="list-group list-group-flush">
                                <li v-for="account in provider.accounts" class="list-group-item bg-transparent">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-xl">
                                            <div class="card card-body">
                                                <div class="row">
                                                    <div class="col-8">
                                                        <div class="numbers">
                                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Account</p>
                                                            <h5 class="font-weight-bolder mb-0">
                                                                {{account.login}}
                                                            </h5>
                                                        </div>
                                                    </div>
                                                    <div class="col-4 text-end">
                                                        <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                                            <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-xl">
                                            <div class="card card-body">
                                                <div class="row">
                                                    <div class="col-8">
                                                        <div class="numbers">
                                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Balance</p>
                                                            <h5 class="font-weight-bolder mb-0">
                                                                $ {{account.balance.numberFormat(2)}}
                                                            </h5>
                                                        </div>
                                                    </div>
                                                    <div class="col-4 text-end">
                                                        <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                                            <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-xl">
                                            <div class="card card-body">
                                                <div class="row">
                                                    <div class="col-8">
                                                        <div class="numbers">
                                                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Balance in wallet</p>
                                                            <h5 class="font-weight-bolder mb-0">
                                                                $ {{wallet.amount.numberFormat(2)}} USD 
                                                            </h5>
                                                        </div>
                                                    </div>
                                                    <div class="col-4 text-end">
                                                        <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                                            <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-xl-auto">
                                            <div class="d-grid">
                                                <button @click="enableCopyAccount(provider.user_trading_account_id,account.user_trading_account_id)" class="btn btn-primary shadow-none mb-0">Enable Copy for this account</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="alert alert-secondary text-white">
                                                <div class="h4 text-white">
                                                    <strong>Importante</strong>
                                                </div>
                                                <div class="mb-3 lead">
                                                    Para habilitar <b>PAMMyTrading</b> para este proveedor necesitas al menos el 10% del balance de tu cuenta <b>{{account.login}}</b> lo que representa <b>$ {{account.balance.getPercentajeToEnablePammy().numberFormat(2)}} USD</b> en tu cartera electrónica. 
                                                </div>
                                                <div v-if="account.balance.getPercentajeToEnablePammy()-wallet.amount > 0" class="mb-3 lead">
                                                    Actualmente tienes <b>$ {{wallet.amount.numberFormat(2)}} USD</b> en tu cartera electronica por favor depósita <b>$ {{(account.balance.getPercentajeToEnablePammy()-wallet.amount).numberFormat(2)}} USD</b> <a href="">aquí</a>
                                                </div>
                                                <div v-else>
                                                    Ya puedes habilitar PAMMyTrading para este proveedor
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
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

export { PammyViewer } 