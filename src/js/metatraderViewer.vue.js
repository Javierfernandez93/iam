import { User } from '../../src/js/user.module.js?v=2.6.4'   

const MetatraderViewer = {
    name : 'metatrader-viewer',
    emits: ['nextStep'],
    props: ['feedback'],
    data() {
        return {
            User : new User,
            token : 'eyJhbGciOiJSUzUxMiIsInR5cCI6IkpXVCJ9.eyJfaWQiOiI5NzBiYTljZDFlYzhlMjUwNmFhZTM5Y2I1ZDNjNjIyNSIsInBlcm1pc3Npb25zIjpbXSwidG9rZW5JZCI6IjIwMjEwMjEzIiwiaW1wZXJzb25hdGVkIjpmYWxzZSwicmVhbFVzZXJJZCI6Ijk3MGJhOWNkMWVjOGUyNTA2YWFlMzljYjVkM2M2MjI1IiwiaWF0IjoxNjg4NjAzNTIyfQ.QdpD19lO3DkHJWPqEdTxddovaZcyJgHtSuxECvUygT-xAQ7O76uRVrzYCfn9--VbZQeTAqPgkazP8j2Xhlk-NR5S0FQliOcWSqdw-RystKBhwRmsxb8KnKNwtie71mERE2VLV9OJWdhPwIftaMexUJWGFXO7vPU4TaNO16XLZuK8yieiEJElRbDmTe4AKHqvVF5zwbgDWc3ucJTSaoLrYiQe9fk6L81le1KvcxBSawfsh6e5bEJoPZkBWUBEJcifXUIXgYCKAle_iLmo4mXhWDyOpePIzxIOwBuWdHK8MYnut_hKe6D9Xb56yVUa22Y-z0tCoLr0L43W0M3F-AJhCyVzHmsybBbkHS4KtVrPL48FDhHArT0c7hXt5yLfyuRfFmd4QOIzaxHqp7WpwX-auIZchTtz_FqzszoRvOlJTFC4OkfrNIqobKqD0IFjMo57aA6UIkpD5fbAn2t6BJmhXRUcitTu1f1Mxerd42Yt5bQxidXHgItAr7UFrP56m5Gp_i8cz13TMlKxjJoqMphGTJvHldsy3I4RthjGs7IM6q-ETLVP-z9iR49yUdTE-kd_cO9DBbInEierTygLEnjZIIpNN9ZH8GK5HTzjZ37Twl3mXXUQ5KCFV-Yt-In2rubcLEK0vB0cfY4LgqL8nTJ7Pb009jHHi5ZncTXvwx-1NdU',
            busy : false,
            connectionPassed : false,
            showButtonToDeleteAccount : false,
            max_trading_accounts_per_user : 1,
            busyFollowingAccount : false,
            brokersVisible : false,
            brokers : null,
            brokersAux : null,
            metaTraderResponse : null,
            accounts : null,
            accountsAux : null,
            user_trading_account : {
                user_trading_account_id : null,
                catalog_trading_account_id : null,
                catalog_platform_id : 1,
                server : '',
                login : '',
                password : '',
            },
            CATALOG_TRADING_ACCOUNTS : {
                METATRADER : 1, 
                BINANCE : 2
            },
            step: null,
            STEPS : {
                SELECT_ACCOUNT : {
                    text: 'Selecciona tu cuenta',
                    code: 1,
                },
                SELECT_BROKER : {
                    text: 'Selecciona tu broker',
                    code: 2,
                },
                FILL_LOGIN : {
                    text: 'Añade tu cuenta',
                    code: 3,
                }
            },
            STATUS : {
                IN_PROGRESS : {
                    text : `<i class="bi bi-check"></i> Dummie Trading`,
                    _class : 'text-white h4 text-center fw-semibold',
                    code : 1,
                },
                CANCELED_BY_EA : {
                    text : 'Cancelada por EA',
                    _class : 'bg-gradient-danger text-white text-center fw-semibold',
                    code : 5,
                }
            },
            api : null,
        }
    },
    watch: {
        'user_trading_account.catalog_platform_id' : {
            handler() {
                this.getAllBrokers()
            },
            deep: true
        }
    },
    methods: {
        getAllBrokers() {
            this.User.getAllBrokers({catalog_platform_id:this.user_trading_account.catalog_platform_id},(response)=>{
                if(response.s == 1)
                {
                    this.brokers = response.brokers
                    this.brokersAux = response.brokers
                }
            })
        },
        selectBroker(broker) {
            this.user_trading_account.server = broker
            this.brokersVisible = false
        },
        searchBroker: _debounce((self) => {
            if(self.user_trading_account.server.length >= 3)
            {
                self.brokersVisible = true
                self.brokers = self.brokersAux
                self.brokers = self.brokers.filter((broker)=>{
                    return broker.toLowerCase().includes(self.user_trading_account.server.toLowerCase())
                })
            }
        },500),
        goToTrades(account) {
            window.location.href = `../../apps/trades/?utaid=${account.user_trading_account_id}`
        },
        validateBroker() {
            this.User.validateBroker({catalog_platform_id:this.user_trading_account.catalog_platform_id,broker:this.user_trading_account.server},(response)=>{
                console.log(response);
                if(response.s == 1)
                {
                    this.step = this.STEPS.FILL_LOGIN
                } else {
                    alertInfo({
                        icon:'<i class="bi bi-x"></i>',
                        message: `No encontramos a el broker <b>${this.user_trading_account.server}</b>. Verifica que está correctamente escrito`,
                        _class:'bg-gradient-danger text-white'
                    })
                }
            })
        },
        createRiskFactor(account) {
            this.User.createRiskFactor({id:account.id},(response)=>{
                if(response.s == 1)
                {

                }
            })
        },
        createEquityListener(account) {
            this.User.createEquityListener({id:account.id,user_trading_account_id:account.user_trading_account_id},(response)=>{
                if(response.s == 1)
                {

                }
            })
        },
        getTrackingData(account) {
            this.User.getTrackingData({id:account.id},(response)=>{
                if(response.s == 1)
                {

                }
            })
        },
        watchDrawdown(account) {
            this.User.watchDrawdown({user_trading_account_id:account.user_trading_account_id},(response)=>{
                if(response.s == 1)
                {
                    account.watch_drawdown = true
                }
            })
        },
        disableAccountsFollow() {
            this.accounts.map((account) => {
                account.follow = false
                return account
            })
        },
        validateIfAccountIsConnected(account) {
            if(!this.busy)
            {
                this.busy = true
                this.User.validateIfAccountIsConnected({id:account.id},(response)=>{
                    this.busy = false

                    if(response.s == 1)
                    {
                        alertInfo({
                            icon:'<i class="bi bi-ui-checks"></i>',
                            message: `<div>Tu cuenta</div> <div class="text-xs">${account.id}</div> <div>está conectada a Dummie Trading</div>`,
                            _class:'bg-gradient-success text-white'
                        })
                    } else if(response.r == 'NOT_CONNECTED'){
                        let text = ''

                        if(response.account_disconnected)
                        {
                            account.id = null
                            text = `.Prueba volviendo conectar a MetaTrader en el botón "conectar a metatrader"`
                        }
                        
                        alertInfo({
                            icon:'<i class="bi bi-x"></i>',
                            message: `Cuenta ${account.id} no conectada${text}`,
                            _class:'bg-gradient-danger text-white'
                        })
                    }
                })
            }
        },
        followAccount(account) {
            this.busyFollowingAccount = true
            this.User.telegramDispatcher({message:`follow=${account.login}`},(response)=>{
                this.busyFollowingAccount = false
                if(response.s == 1)
                {
                    this.disableAccountsFollow()
                    account.follow = true
                }
            })
        },
        deleteAccount(account) {
            account.busy = true

            this.User.deleteAccount({user_trading_account_id:account.user_trading_account_id},(response)=>{
                account.busy = false
                
                if(response.s == 1)
                {
                    this.getTradingAccountsMaster()
                } 
            })
        },
        createAccount(account) {
            return new Promise((resolve)=>{
                account.busy = true
                this.metaTraderResponse = null
                this.busy = true
                this.User.createAccount(account,(response)=>{
                    account.busy = false
                    this.busy = false

                    if(response.s == 1)
                    {
                        account.id = response.id

                        resolve()
                    } else {
                        this.metaTraderResponse = response.metaTraderResponse
                        this.showButtonToDeleteAccount = true
                    }
                })
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
        addTradingAccount() {
            this.busy = true
            this.metaTraderResponse = null
            this.User.addTradingAccount(this.user_trading_account,(response)=>{
                this.busy = false

                if(response.s == 1)
                {
                    this.user_trading_account.user_trading_account_id = response.user_trading_account_id
                    
                    this.createAccount(this.user_trading_account).then(()=>{
                        this.getTradingAccountsMaster()

                        if(this.feedback)
                        {
                            // this.$emit('nextStep')
                        }
                    })
                } else if(response.r == 'INVALID_BROKER') {
                    alertInfo({
                        icon:'<i class="bi bi-x"></i>',
                        message: `No encontramos a el broker <b>${this.user_trading_account.server}</b>. Verifica que está correctamente escrito`,
                        _class:'bg-gradient-danger text-white'
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
        getTradingAccountsMaster() {
            this.getTradingAccounts().then(accounts => {
                this.accounts = accounts

                if(this.feedback)
                {
                    // this.$emit('nextStep')
                }
            }).catch(() => {
                this.addAccount()
            })
        },
        addAccount() {

            this.accounts = false
    
            this.step = this.STEPS.SELECT_ACCOUNT
            this.user_trading_account.catalog_trading_account_id = this.CATALOG_TRADING_ACCOUNTS.METATRADER

            this.getAllBrokers()
        },
        getTradingAccounts() {
            return new Promise((resolve, reject) => {
                this.User.getTradingAccounts({demo:false,catalog_trading_account_id:this.CATALOG_TRADING_ACCOUNTS.METATRADER},(response)=>{
                    if(response.s == 1)
                    {
                        resolve(response.accounts)
                    } 

                    reject()
                })
            })
        },
        getMaxTradingAccountsPerUser() {
            this.User.getMaxTradingAccountsPerUser({},(response)=>{
                this.max_trading_accounts_per_user = response.max_trading_accounts_per_user
            })
        },
        updateUserTradingAccount(account,user_trading_account_id)
        {
            console.log(`updating account data`)

            account.user_trading_account_id = user_trading_account_id

            this.User.updateUserTradingAccount(account,(response)=>{
                if(response.s == 1)
                {
                    this.$emit('nextStep')
                }
            })
        },
        updateTradesFromAccount(trades,user_trading_account_id) {
            console.log("updating account trades")

            this.updatingTrades = true

            this.User.updateTradesFromAccount({user_trading_account_id:user_trading_account_id,trades:trades},(response)=>{
                this.updatingTrades = false
            })
        },
        async connect(account) {
            this.busy = true
            this.api = new MetaApi(this.token);

            console.log(account)

            this.loadingInfo = true

            this.accountMT = await this.api.metatraderAccountApi.getAccount(account.id)

            await this.accountMT.waitConnected();

            this.connection = await this.accountMT.getRPCConnection();

            await this.connection.connect()
            await this.connection.waitSynchronized()

            // console.log('Testing terminal state access');

            this.terminalState = this.connection.terminalState;
            
            // log('history deals by ticket:', JSON.stringify(await connection.getDealsByTicket('1234567')));
            // log('history deals by position:', JSON.stringify(await connection.getDealsByPosition('1234567')));

            this.updateUserTradingAccount(await this.connection.getAccountInformation(),account.user_trading_account_id)
            
            this.updateTradesFromAccount(await this.connection.getPositions(),account.user_trading_account_id)

            this.busy = false
        }
    },
    mounted() {
        this.user_trading_account.catalog_trading_account_id = this.CATALOG_TRADING_ACCOUNTS.METATRADER
        
        this.getMaxTradingAccountsPerUser()
        this.getTradingAccountsMaster()
    },
    template : `
        <div v-if="accounts">
            <div v-if="accounts.length < max_trading_accounts_per_user" class="row justify-content-center mb-3">
                <div class="col-12 col-xl-4">
                    <div class="card card-body text-center">
                        <div class="h4 text-primary">¿Quieres añadir otra cuenta?</div>
                        <button v-if="accounts" class="btn btn-primary" @click="addAccount">Añadir otra cuenta</button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="accounts" class="row justify-content-center">
            <div v-for="account in accounts" class="col-12 col-xl-4 mb-3 animation-fall-right" style="--delay:500ms">
                <div class="card over-card-blur bg-gradient-primary overflow-hidden">
                    <div v-if="account.busy" class="mask position-absolute z-index-1 d-flex justify-content-center align-items-center bg-dark">
                        <div class="spinner-grow text-white" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>

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
                        <ul class="list-group list-group-flush bg-transparent">
                            <li class="list-group-item bg-transparent">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="text-light text-xs">Login</div>
                                        <span class="text-white">{{account.login}}</span>
                                    </div>
                                    <div class="col-auto">
                                        <button @click="copy(account.login,$event.target)" class="btn ms-2 mb-0 btn-sm shadow-none btn-light px-3">Copiar</button>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item bg-transparent">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="text-light text-xs">Password</div>

                                        <div class="text-white">
                                            <span v-if="account.show">
                                                {{account.password}} 
                                            </span>
                                            <span v-else>
                                                {{account.password.hideText(5)}}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button @click="copy(account.password,$event.target)" class="btn ms-2 mb-0 btn-sm shadow-none btn-light px-3">Copiar</button>
                                        <button @click="account.show = !account.show" class="btn ms-2 mb-0 btn-sm shadow-none btn-light px-3" v-text="account-show ? 'hide':'show'"></button>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item bg-transparent">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="text-light text-xs">Server</div>
                                        <span class="text-white">{{account.server}}</span>
                                    </div>
                                    <div class="col-auto">
                                        <button @click="copy(account.server,$event.target)" class="btn ms-2 mb-0 btn-sm shadow-none btn-light px-3">Copiar</button>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item bg-transparent">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="text-light text-xs">Trader</div>
                                        <span class="text-white">{{account.trader}}</span>
                                    </div>
                                    <div class="col-auto">
                                        <button @click="copy(account.trader,$event.target)" class="btn ms-2 mb-0 btn-sm shadow-none btn-light px-3">Copiar</button>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item bg-transparent">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="mb-3">
                                            <div class="text-light text-xs">Plataforma</div>
                                            <span class="text-white">{{account.type}}</span>
                                        </div>
                                    
                                        <div class="d-none">
                                            <a href="https://download.mql5.com/cdn/web/metaquotes.software.corp/mt5/mt5setup.exe?utm_source=www.metatrader5.com&utm_campaign=download" class="btn btn-sm shadow-none btn-light px-3"><i class="bi fs-6 bi-windows"></i></a>
                                            <a href="https://download.mql5.com/cdn/web/metaquotes.software.corp/mt5/MetaTrader5.dmg?utm_source=www.metatrader5.com&utm_campaign=download.mt5.macos" class="btn ms-2 btn-sm shadow-none btn-light px-3"><i class="bi fs-6 bi-apple"></i></a>
                                            <a href="https://www.mql5.com/es/articles/625?utm_source=www.metatrader5.com&utm_campaign=download.mt5.linux" class="btn ms-2 btn-sm shadow-none btn-light px-3">Linux</a>
                                            <a href="https://www.metatrader5.com/i/main/metatrader-5-windows.jpg" class="btn ms-2 btn-sm shadow-none btn-light px-3">web</a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div v-if="account.configuration" class="">
                        <div class="mb-3"><span class="badge ms-2 text-secondary p-0"> Configuración elegida para tu cuenta</span></div>
                        
                        <ul class="list-group list-group-flush bg-transparent">
                            <li v-for="configuration in account.configuration" class="list-group-item bg-transparent">
                                <div class="row">
                                    <div class="col-12 col-xl">
                                        <div class=" text-xs">{{configuration.description}}</div>
                                    </div>
                                    <div class="col-12 col-xl-auto">
                                        <div class="">{{configuration.value}}</div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div v-if="account.id" class="d-grid">
                            <div v-if="feedback" class="d-grid">
                                <button :disabled="busy" @click="connect(account)" class="btn btn-light mb-0 shadow-none btn-lg">
                                    <span v-if="busy">
                                        ...
                                    </span>
                                    <span v-else>
                                        Probar conexión
                                    </span>
                                </button>
                            </div>
                            <div v-else>
                                <div class="d-grid">
                                    <button @click="goToTrades(account)" class="btn btn-light mb-0 shadow-none btn-lg">Ver bitácora</button>
                                </div>

                                <button @click="createEquityListener(account)" class="btn d-none btn-light mt-3 shadow-none btn-lg">Create listener</button>
                                <button @click="createRiskFactor(account)" class="btn d-none btn-light mt-3 shadow-none btn-lg">Create risk</button>
                                <button @click="getTrackingData(account)" class="btn d-none btn-light mt-3 shadow-none btn-lg">getData risk</button>

                                <div v-if="account.watch_drawdown" class="text-white text-center p-3 fw-sembold text-md">
                                    <i class="bi bi-eye"></i>
                                    Visualizando DrawDown
                                </div>
                                <div v-else class="d-grid">
                                    <button @click="watchDrawdown(account)" class="btn btn-light mt-3 shadow-none btn-lg">WatchDrawdown</button>
                                </div>
                            </div>
                        </div>

                        <div v-if="metaTraderResponse" class="alert text-white alert-danger">
                            <div v-if="metaTraderResponse.r =='ERROR_CREATING_ACCOUNT'">
                                <div v-if="metaTraderResponse.e">
                                    <span v-if="metaTraderResponse.e.message.lenght > 0">
                                        <span v-for="message in metaTraderResponse.e.message">
                                            <strong>{{message.parameter}}:</strong>
                                            {{message.message}}
                                        </span>
                                    </span>
                                    <span v-else>
                                        <strong>{{metaTraderResponse.e.message}}</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div v-if="showButtonToDeleteAccount" class="d-grid mt-2">
                            <button @click="deleteAccount(account)" class="btn btn-light mb-0 shadow-none btn-lg">Eliminar cuenta</a>
                        </div>
                        <div v-if="!account.id" class="d-grid mt-2">
                            <button @click="createAccount(account)" class="btn btn-light mb-0 shadow-none btn-lg">Conectar a MetaTrader</a>
                        </div>
                        <div v-else>
                            <div>
                                <div v-if="!account.follow" class="d-grid">
                                    <button :disabled="busyFollowingAccount" @click="followAccount(account)" class="btn mt-3 btn-outline-light shadow-none btn-lg">
                                        <span v-if="!busyFollowingAccount">
                                            Seguir cuenta
                                        </span>
                                        <span v-else>
                                            ...
                                        </span>
                                    </a>
                                </div>
                                <div v-else class="text-center py-3 text-white fw-semibold">
                                    Estás siguiendo esta cuenta
                                </div>
                            </div>
                            <div class="text-center fw-semibold ">
                                <div v-if="!feedback" class="mb-3 text-white">
                                    Cuenta conectada 
                                    
                                    <button :disabled="busy" @click="validateIfAccountIsConnected(account)" class="btn btn-light btn-sm px-3 mb-0 shadow-none">
                                        <span v-if="!busy">
                                            Probar conexión
                                        </span>
                                        <span v-else>
                                            ...
                                        </span>
                                    </button>
                                </div>
                                <div class="text-white">
                                    <div class="text-xs">Id de cuenta</div>
                                    <div class="fw-semibold">
                                        {{account.id}}
                                    </div>
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
            <div v-if="feedback" class="d-flex justify-content-center mt-3">
                <button @click="$emit('nextStep')" class="btn btn-primary mb-0 shadow-none btn-lg">
                    Siguiente paso
                </button>
            </div>
        </div>
        <div v-else-if="accounts == false">
            <div v-if="step" class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-xl-4">
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
                            <div v-if="step == STEPS.SELECT_ACCOUNT" class="animation-fall-down" style="--delay:500ms">
                                <div class="card card-body blur shadow-blur mb-3">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12 col-xl">
                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" v-model="user_trading_account.catalog_platform_id" name="type" type="radio" value="1" name="MT4" id="MT4">
                                                        <label class="form-check-label" for="MT4">
                                                            Meta trader 4
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" v-model="user_trading_account.catalog_platform_id" name="type" type="radio" value="2" name="MT5" id="MT5">
                                                        <label class="form-check-label" for="MT5">
                                                            Meta trader 5
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row justify-content-end">
                                    <div class="col-auto">
                                        <button class="btn btn-primary" @click="step = STEPS.SELECT_BROKER">Siguiente</button>
                                    </div>
                                </div>
                            </div>
                            <div v-if="step == STEPS.SELECT_BROKER" class="animation-fall-down" style="--delay:500ms">
                                <div class="card blur shadow-blur mb-3">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12 col-xl">
                                                <div class="position-relative">
                                                    <div class="form-floating mb-3">
                                                        <input ref="server" @keypress="searchBroker(this)" autocomplete="one-time-code" v-model="user_trading_account.server" type="text" class="form-control" id="server" placeholder="Par">
                                                        <label for="server">Broker</label>
                                                    </div>
                                                    <div v-if="brokers && brokersVisible" class="card z-index-1 position-absolute top-100 w-100 start-0 overflow-scroll" style="max-height:12rem">
                                                        <ul v-for="broker in brokers" class="list-group list-group-flush">
                                                            <li @click="selectBroker(broker)" class="cursor-pointer list-group-item rounded-0">{{broker}}</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row justify-content-end">
                                    <div class="col-auto">
                                        <button :disabled="!user_trading_account.server" class="btn btn-primary" @click="validateBroker">Siguiente</button>
                                    </div>
                                </div>
                            </div>
                            <div v-if="step == STEPS.FILL_LOGIN" class="animation-fall-down" style="--delay:500ms">
                                <div class="card card-body blur shadow-blur mb-3">    
                                    <div class="row">
                                        <div class="col-12 col-xl">
                                            <div class="form-floating mb-3">
                                                <input ref="login" @keypress.enter.exact="$refs.password.focus()" :class="user_trading_account.login ? 'is-valid':'is-invalid'" type="text" v-model="user_trading_account.login" class="form-control" id="login" placeholder="Login">
                                                <label for="login">Login</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input ref="password" @keypress.enter.exact="$refs.password.focus()" :class="user_trading_account.password ? 'is-valid':'is-invalid'" type="text" v-model="user_trading_account.password" class="form-control" id="password" placeholder="Contraseña">
                                                <label for="password">Contraseña</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="metaTraderResponse" class="alert text-white alert-danger">
                                    <div v-if="metaTraderResponse.r =='ERROR_CREATING_ACCOUNT'">
                                        <div v-if="metaTraderResponse.e">
                                            <span v-if="metaTraderResponse.e.message.lenght > 0">
                                                <span v-for="message in metaTraderResponse.e.message">
                                                    <strong>{{message.parameter}}:</strong>
                                                    {{message.message}}
                                                </span>
                                            </span>
                                            <span v-else>
                                                <strong>{{metaTraderResponse.e.message}}</strong>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-end">
                                    <div class="col-auto">
                                        <button :disabled="busy" class="btn btn-primary" @click="addTradingAccount">
                                            <span v-if="busy">
                                                <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                                            </span>
                                            <span v-else>
                                                Conectar
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

export { MetatraderViewer } 