import { User } from '../../src/js/user.module.js?v=2.6.4'   

const BinanceViewer = {
    name : 'binance-viewer',
    data() {
        return {
            User : new User,
            busy : false,
            accounts : null,
            accountsAux : null,
            CATALOG_TRADING_ACCOUNTS : {
                METATRADER : 1, 
                BINANCE : 2
            },
            user_trading_account : {
                accountValid : false,
                user_trading_account_id : null,
                catalog_trading_account_id : null,
                login : '',
                password : '',
            },
            step: null,
            STEPS : {
                ADDING_KEYS : {
                    text: 'Añade tus credenciales',
                    code: 1,
                },
                FILL_LOGIN : {
                    text: 'Conectemos con tu cuenta',
                    code: 2,
                }
            },
            STATUS : {
                IN_PROGRESS : {
                    text : `<i class="bi bi-check"></i> En Dummie Trading`,
                    _class : 'text-white h4 text-center fw-semibold',
                    code : 1,
                },
                CANCELED_BY_EA : {
                    text : 'Inactivo',
                    _class : 'text-white text-center fw-semibold',
                    code : 5,
                }
            }
        }
    },
    methods: {
        goToTrades(account) {
            window.location.href = `../../apps/trades/binance?utaid=${account.user_trading_account_id}`
        },
        disableAccountsFollow() {
            this.accounts.map((account) => {
                account.follow = false
                return account
            })
        },
        followAccount(account) {
            this.User.telegramDispatcher({message:`follow=${account.login}`},(response)=>{
                if(response.s == 1)
                {
                    this.disableAccountsFollow()
                    account.follow = true
                }
            })
        },
        createBinanceAccount(account) {
            return new Promise((resolve)=>{
                this.busy = true
                this.User.createBinanceAccount(account,(response)=>{
                    this.busy = false
                    if(response.s == 1)
                    {
                        account.id = response.id

                        resolve()
                    }
                })
            })
        },
        addTradingAccount() {
            this.busy = true
            this.User.addTradingAccount(this.user_trading_account,(response)=>{
                this.busy = false
                if(response.s == 1)
                {
                    this.user_trading_account.user_trading_account_id = response.user_trading_account_id
                    
                    this.createBinanceAccount(this.user_trading_account).then(()=>{
                        this.getTradingAccountsMaster()
                    })
                } else if(response.s == 'ALREADY_HAS_ACCOUNT') {
                    alertInfo({
                        icon:'<i class="bi bi-ui-checks"></i>',
                        message: `La cuenta ${this.user_trading_account.login} que intentas añadir ya existe en Dummie Trading`,
                        _class:'bg-gradient-success text-white'
                    })
                }
            })
        },
        copy(text,target) {
            navigator.clipboard.writeText(text).then(() => {
                target.innerText = 'Copiado a portapapeles'
            })
        },
        getTradingStatus() {
            this.accounts = this.accounts.map((account) =>{
                account.statusInfo = this.getStatusInfo(account.status)

                return account
            })
        },
        changeAccountAlias(account) {
            this.busy = true
            this.User.changeAccountAlias(account,(response)=>{
                this.busy = false
                if(response.s == 1)
                {
                    account.addingAlias = false
                }
            })
        },
        connectWithBinanceAccount() {
            this.User.connectWithBinanceAccount({api_key:this.user_trading_account.login,api_secret:this.user_trading_account.password},(response)=>{
                if(response.s == 1)
                {
                    this.user_trading_account.accountValid = true
                    this.step = this.STEPS.FILL_LOGIN
                } 
            })
        },
        getStartedInfo() {
            let alert = alertCtrl.create({
                title: `<h3 class="">Conéctate a Binance</h3>`,
                bgColor: "blur shadow-blur",
                size: "modal-lg",
                html: `
                    <div class="card-body">
                        <div class="overflow-scroll">
                            <div class="card blur over-card-blur shadow-blur mb-3 animation-fall-down" style="--delay:500ms">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-12">
                                            <img src="../../src/img/video-player.png" alt="video-player" title="video-player" class="img-fluid rounded"/>
                                        </div>
                                        <div class="col-12">
                                            <div class="h4">Obtén tus llaves</div>
                                            <div class="">Debes de ir a tu cuenta de binance para comenzar</div>

                                            <div class="mt-3">
                                                <button class="btn btn-primary shadow-none btn-sm px-3">Ver video</button>
                                                <a href="../../apps/brokers/" target="_blank" class="btn btn-primary shadow-none btn-sm px-3">Crea tu cuenta en binance</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `,
            })

            alertCtrl.present(alert.modal);
        },
        getTradingAccountsMaster() {
            this.getTradingAccounts().then(accounts => {
                this.accounts = accounts
            }).catch(() => {
                this.accounts = false
    
                this.step = this.STEPS.ADDING_KEYS
                this.user_trading_account.catalog_trading_account_id = this.CATALOG_TRADING_ACCOUNTS.BINANCE
            })
        },
        getTradingAccounts() {
            return new Promise((resolve, reject) => {
                this.User.getTradingAccounts({demo:false,catalog_trading_account_id:this.CATALOG_TRADING_ACCOUNTS.BINANCE},(response)=>{
                    if(response.s == 1)
                    {
                        resolve(response.accounts)
                    } 

                    reject()
                })
            })
        },
    },
    mounted() {
        this.getTradingAccountsMaster()
        
        setTimeout(()=>{
            this.getStartedInfo()
        },500)
    },
    template : `
        <div v-if="accounts" class="row justify-content-center">
            <div v-for="account in accounts" class="col-12 col-xl-4 mb-3">
                <div class="card bg-gradient-primary overflow-hidden">
                    <div v-if="account.status == STATUS.IN_PROGRESS.code">
                        <div class="card-header bg-transparent" :class="STATUS.IN_PROGRESS._class">
                            <span v-html="STATUS.IN_PROGRESS.text"></span>
                        </div>
                    </div>
                    <div v-else-if="account.status == STATUS.CANCELED_BY_EA.code">
                        <div class="card-header bg-transparent" :class="STATUS.CANCELED_BY_EA._class">
                            <span v-html="STATUS.CANCELED_BY_EA.text"></span>
                            {{account.comment}}
                        </div>
                    </div>

                    <div class="">
                        <ul class="list-group bg-transparent list-group-flush">
                            <li class="list-group-item bg-transparent">
                                <div class="row align-items-center">
                                    <div class="col-12 col-xl text-truncate">
                                        <div class="text-light text-xs">Api key</div>
                                        <span class="text-white">{{account.login}}</span>
                                    </div>
                                    <div class="col-12 col-xl-auto">
                                        <button @click="copy(account.login,$event.target)" class="btn ms-2 mb-0 btn-sm shadow-none btn-light px-3">Copiar</button>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item bg-transparent">
                                <div class="row align-items-center">
                                <div class="col-12 col-xl text-truncate">
                                        <div class="text-light text-xs">Api Secret</div>
                                        <span class="text-white">{{account.password}}</span>
                                    </div>
                                    <div class="col-12 col-xl-auto">
                                        <button @click="copy(account.password,$event.target)" class="btn ms-2 mb-0 btn-sm shadow-none btn-light px-3">Copiar</button>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-grid">
                            <button @click="goToTrades(account)" class="btn btn-light mb-0 shadow-none btn-lg">Ver bitácora</a>
                        </div>
                        <div v-else>
                            <div>
                                <div v-if="!account.follow" class="d-grid">
                                    <button @click="followAccount(account)" class="btn mt-3 btn-outline-light shadow-none btn-lg">Seguir cuenta</a>
                                </div>
                                <div v-else class="text-center py-3 text-white fw-semibold">
                                    Estás siguiendo esta cuenta
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-white text-xs">
                                    Cuenta conectada 
                                </div>
                                <div class="text-xs text-white">
                                    {{account.id}}
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center mt-3">
                            <div class="col-auto">
                                <div v-if="account.addingAlias">
                                    <input v-model="account.alias" @keypress.enter.exact="changeAccountAlias(account)" type="text" class="form-control" placeholder="Alias"/>
                                </div>
                                <div v-else @click="account.addingAlias = true">
                                    <span v-if="account.alias" class="badge bg-light text-primary">
                                        {{account.alias}}
                                    </span>
                                    <span v-else class="text-decoration-underline text-center text-white cursor-pointer">
                                        añadir alias
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else-if="accounts == false">
            <div v-if="step" class="container">
                <div class="row align-items-center animation-fall-down" style="--delay:500ms">
                    <div class="col-12 col-xl">
                        <h2 class="mb-3">Conecta con Binance</h2>
                        <div class="mb-3">
                            <strong>Binance</strong> es una de las principales plataformas de intercambio de <strong>criptomonedas a nivel mundial</strong>. Fundada en 2017 por <strong>Changpeng Zhao</strong>, Binance se ha convertido en una de las bolsas de criptomonedas más grandes y populares del mundo en términos de volumen de <strong>operaciones</strong>.
                        </div>
                    </div>
                    <div class="col-12 col-xl-auto order-1">
                        <lottie-player src="../../src/files/json/binance.json" background="transparent"  speed="1"  mode=“normal” loop autoplay style="width: 400px; height: 400px;"></lottie-player>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-12 col-xl-4 animation-fall-right" style="--delay:800ms">
                        <div>
                            <li class="list-group bg-transparent list-group-flush">
                                <li v-for="mStep in STEPS" class="list-group-item bg-transparent cursor-pointer border-0" @click="step = mStep">
                                    <span :class="step.code == mStep.code ? 'fw-semibold text-dark' : 'text-secondary'">
                                        {{mStep.text}}
                                    </span>
                                </li>
                            </li>
                        </div>
                    </div>
                    <div class="col-12 col-xl-8">
                        <div class="card-footer">
                            <div v-if="step == STEPS.ADDING_KEYS" class="animation-fall-down" style="--delay:500ms">
                                <div class="card blur shadow-blur mb-3">
                                    <div class="card-header bg-transparent">
                                        <div class="alert alert-info text-white">
                                            <div>Si no sabes como obtenerlas da click aquí</div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="card-body">
                                            <div class="col-12 col-xl">
                                                <div class="form-floating mb-3">
                                                    <input ref="login" @keypress.enter.exact="$refs.password.focus()" :class="user_trading_account.login ? 'is-valid':'is-invalid'" type="text" v-model="user_trading_account.login" class="form-control" id="login" placeholder="Login">
                                                    <label for="login">Api Key</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input ref="password" @keypress.enter.exact="$refs.password.focus()" :class="user_trading_account.password ? 'is-valid':'is-invalid'" type="text" v-model="user_trading_account.password" class="form-control" id="password" placeholder="Contraseña">
                                                    <label for="password">Api Secret</label>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row justify-content-end">
                                    <div class="col-auto">
                                        <button class="btn btn-primary" @click="connectWithBinanceAccount" @click="step = STEPS.FILL_LOGIN">Siguiente</button>
                                    </div>
                                </div>
                            </div>
                            <div v-if="step == STEPS.FILL_LOGIN" class="animation-fall-down" style="--delay:500ms">
                                <div class="card card-body blur shadow-blur mb-3 text-center">
                                    <div class="row">
                                        <div class="col-12 col-xl">
                                            <div v-if="user_trading_account.accountValid">
                                        
                                                <div class="h3">
                                                    <i class="bi bi-check"></i>
                                                    Cuenta conectada correctamente
                                                </div>

                                                <div class="text-xs">Da click en finalizar configuracion para terminar de añadir tu cuenta a DummieTrading</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-end">
                                    <div class="col-auto">
                                        <button :disabled="!user_trading_account.accountValid" class="btn btn-primary" @click="addTradingAccount">
                                            <span v-if="busy">
                                                <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                                            </span>
                                            <span v-else>
                                                Finalizar configuración
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { BinanceViewer } 