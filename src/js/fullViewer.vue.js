import { User } from '../../src/js/user.module.js?v=2.6.4'   

const FullViewer = {
    name : 'full-viewer',
    data() {
        return {
            User : new User,
            accounts : null,
            accountsAux : null,
            STATUS : {
                IN_PROGRESS : {
                    text : 'En progresso realizando trades',
                    _class : 'bg-gradient-success text-white text-center fw-semibold',
                    code : 1,
                },
                CANCELED_BY_EA : {
                    text : 'Cancelada por EA',
                    _class : 'bg-gradient-danger text-white text-center fw-semibold',
                    code : 5,
                }
            }
        }
    },
    methods: {
        goToTrades(account) {
            window.location.href = `../../apps/trades/?utaid=${account.user_trading_account_id}`
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
        createAccount(account) {
            this.User.createAccount(account,(response)=>{
                if(response.s == 1)
                {
                    account.id = response.id
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
        getTradingAccounts() {
            return new Promise((resolve, reject) => {
                this.User.getTradingAccounts({demo:false},(response)=>{
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
        this.getTradingAccounts().then(accounts => {
            this.accounts = accounts
        }).catch(() => {
            this.accounts = false
        })
    },
    template : `
        <div v-if="accounts" class="row">
            <div v-for="account in accounts" class="col-12 col-xl-4 mb-3">
                <div class="card overflow-hidden">
                    <div v-if="account.status == STATUS.IN_PROGRESS.code">
                        <div class="card-header" :class="STATUS.IN_PROGRESS._class">
                            <span v-html="STATUS.IN_PROGRESS.text"></span>
                        </div>
                    </div>
                    <div v-else-if="account.status == STATUS.CANCELED_BY_EA.code">
                        <div class="card-header" :class="STATUS.CANCELED_BY_EA._class">
                            <span v-html="STATUS.CANCELED_BY_EA.text"></span>
                            {{account.comment}}
                        </div>
                    </div>

                    <div class="">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="text-secondary text-xs">Login</div>
                                        <span class="text-whitdark">{{account.login}}</span>
                                    </div>
                                    <div class="col-auto">
                                        <button @click="copy(account.login,$event.target)" class="btn ms-2 mb-0 btn-sm shadow-none btn-light px-3">Copiar</button>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="text-secondary text-xs">Password</div>
                                        <span class="text-whitdark">{{account.password}}</span>
                                    </div>
                                    <div class="col-auto">
                                        <button @click="copy(account.password,$event.target)" class="btn ms-2 mb-0 btn-sm shadow-none btn-light px-3">Copiar</button>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="text-secondary text-xs">Server</div>
                                        <span class="text-whitdark">{{account.server}}</span>
                                    </div>
                                    <div class="col-auto">
                                        <button @click="copy(account.server,$event.target)" class="btn ms-2 mb-0 btn-sm shadow-none btn-light px-3">Copiar</button>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="text-secondary text-xs">Trader</div>
                                        <span class="text-whitdark">{{account.trader}}</span>
                                    </div>
                                    <div class="col-auto">
                                        <button @click="copy(account.trader,$event.target)" class="btn ms-2 mb-0 btn-sm shadow-none btn-light px-3">Copiar</button>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="mb-3">
                                            <div class="text-secondary text-xs">Plataforma</div>
                                            <span class="text-whitdark">Meta Trader 4</span>
                                        </div>
                                    
                                        <div>
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
                        
                        <ul class="list-group list-group-flush">
                            <li v-for="configuration in account.configuration" class="list-group-item">
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
                    <div class="card-footer bg-primary">
                        <div class="d-grid">
                            <button @click="goToTrades(account)" class="btn btn-light mb-0 shadow-none btn-lg">Ver bitácora</a>
                        </div>
                        <div v-if="!account.id" class="d-grid mt-2">
                            <button @click="createAccount(account)" class="btn btn-light mb-0 shadow-none btn-lg">Crear cuenta</a>
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
                    </div>
                </div>
            </div>
        </div>
        <div v-else-if="accounts == false" class="card">
            <div class="card-body text-center">
                <div>
                    <strong>Aviso</strong>
                </div>
                <div class="mb-3">Aún no tienes ningún desafió, para comenzar </div>
                <a class="btn btn-primary" href="../../apps/store/package">Compra tu producto aquí </a>
            </div>
        </div>
    `,
}

export { FullViewer } 