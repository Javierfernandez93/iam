import { User } from '../../src/js/user.module.js?v=2.6.4'   

const TradesViewer = {
    name : 'trades-viewer',
    emits : ['toggleOrderMaker','toggleVars'],
    data() {
        return {
            User : new User,
            token : 'eyJhbGciOiJSUzUxMiIsInR5cCI6IkpXVCJ9.eyJfaWQiOiI5NzBiYTljZDFlYzhlMjUwNmFhZTM5Y2I1ZDNjNjIyNSIsInBlcm1pc3Npb25zIjpbXSwidG9rZW5JZCI6IjIwMjEwMjEzIiwiaW1wZXJzb25hdGVkIjpmYWxzZSwicmVhbFVzZXJJZCI6Ijk3MGJhOWNkMWVjOGUyNTA2YWFlMzljYjVkM2M2MjI1IiwiaWF0IjoxNjg4NjAzNTIyfQ.QdpD19lO3DkHJWPqEdTxddovaZcyJgHtSuxECvUygT-xAQ7O76uRVrzYCfn9--VbZQeTAqPgkazP8j2Xhlk-NR5S0FQliOcWSqdw-RystKBhwRmsxb8KnKNwtie71mERE2VLV9OJWdhPwIftaMexUJWGFXO7vPU4TaNO16XLZuK8yieiEJElRbDmTe4AKHqvVF5zwbgDWc3ucJTSaoLrYiQe9fk6L81le1KvcxBSawfsh6e5bEJoPZkBWUBEJcifXUIXgYCKAle_iLmo4mXhWDyOpePIzxIOwBuWdHK8MYnut_hKe6D9Xb56yVUa22Y-z0tCoLr0L43W0M3F-AJhCyVzHmsybBbkHS4KtVrPL48FDhHArT0c7hXt5yLfyuRfFmd4QOIzaxHqp7WpwX-auIZchTtz_FqzszoRvOlJTFC4OkfrNIqobKqD0IFjMo57aA6UIkpD5fbAn2t6BJmhXRUcitTu1f1Mxerd42Yt5bQxidXHgItAr7UFrP56m5Gp_i8cz13TMlKxjJoqMphGTJvHldsy3I4RthjGs7IM6q-ETLVP-z9iR49yUdTE-kd_cO9DBbInEierTygLEnjZIIpNN9ZH8GK5HTzjZ37Twl3mXXUQ5KCFV-Yt-In2rubcLEK0vB0cfY4LgqL8nTJ7Pb009jHHi5ZncTXvwx-1NdU',
            updatingTrades : false,
            loadingInfo : false,
            busy : false,
            api : null,
            connection : null,
            accountMT : null,
            terminalState : null,
            busy : false,
            query : null,
            trades : null,
            account : null,
            tradesAux : null,
            autoUpdate : false,
            interval : null,
            intervalTrades : null,
            filter : {
                start_date : null,
                end_date : null,
                user_trading_account_id : null,
                checkedAll : false,
            },
            columns: { // 0 DESC , 1 ASC 
                ticket: {
                    name: 'ticket',
                    desc: false,
                },
                create_date: {
                    name: 'create_date',
                    desc: false,
                },
                profit: {
                    name: 'profit',
                    desc: false,
                },
                buy: {
                    name: 'buy',
                    desc: false,
                },
                price: {
                    name: 'price',
                    desc: false,
                },
                symbol: {
                    name: 'symbol',
                    desc: false,
                    alphabetically: true,
                },
            },
            accounts : null
        }
    },
    watch : {
        query : {
            handler() {
                this.filterData()
            },
            deep: true
        },
        'filter.checkedAll' : {
            handler() {
                this.toggleAll()
            },
            deep: true
        }
    },
    methods: {
        closeOrder(trade) {
            trade.busy = true

            let text = `close=${trade.ticket}`

            this.User.telegramDispatcher({message:text,catalog_trading_account_id:1},(response)=>{
                trade.busy = false
                trade.close = true

                alertInfo({
                    icon:'<i class="bi bi-ui-checks"></i>',
                    message: `
                        <div class="py-3">
                            <div class="text-xs">InstrucciÃ³n enviada</div>
                            ${text}
                        </div>
                    `,
                    _class:'bg-gradient-success text-white'
                })
            })
        },
        sendOrder(text) {
            this.busy = true

            this.User.telegramDispatcher({message:text,catalog_trading_account_id:1},(response)=>{
                this.busy = false
                
                this.getTradingAccountMaster()
                // this.updateTradesFromAccount(this.terminalState.positions)

                alertInfo({
                    icon:'<i class="bi bi-ui-checks"></i>',
                    message: `
                        <div>Orden enviada</div>
                        ${text}
                    `,
                    _class:'bg-gradient-success text-white'
                })
            })
        },
        sendOrderAsSignal(signal) {
            this.busy = true

            this.User.sendOrderAsSignal({signal:signal,catalog_trading_account_id:1},(response)=>{
                this.busy = false
                
                alertInfo({
                    icon:'<i class="bi bi-ui-checks"></i>',
                    message: `
                        <div class="pb-3">SeÃ±al enviada</div>
                    `,
                    _class:'bg-gradient-success text-white'
                })
            })
        },
        toggleOrderMaker() {
            this.$emit('toggleOrderMaker')
        },
        toggleVars() {
            console.log(123)
            this.$emit('toggleVars')
        },
        sortData(column) {
            this.trades.sort((a, b) => {
                const _a = column.desc ? a : b
                const _b = column.desc ? b : a

                return column.alphabetically ? _a[column.name].localeCompare(_b[column.name]) : _a[column.name] - _b[column.name]
            })

            column.desc = !column.desc
        },
        filterData() {
            this.trades = this.tradesAux
            this.trades = this.trades.filter((trade) =>  {
                return trade.ticket.toString().includes(this.query) || trade.price.toString().includes(this.query) || trade.lotage.toString().includes(this.query) || trade.symbol.toLowerCase().includes(this.query.toLowerCase()) || trade.profit.toString().includes(this.query) 
            })
        },
        toggleAll() {
            this.trades = this.tradesAux
            this.trades = this.trades.map(trade => {
                trade.checked = this.filter.checkedAll

                return trade
            })
        },
        init() {
            this.trades = null
            this.tradesAux = null
        },
        createMarketOrder() {
            let alert = alertCtrl.create({
                title: "Importante",
                subTitle: `<div class="mb-3">Ingresa la informaciÃ³n de la orden</div>`,
                inputs: [
                    {
                        type: 'text',
                        id: 'text',
                        name: 'text',
                        placeholder: 'Orden',
                        label: 'Orden',
                    }
                ],
                buttons: [
                    {
                        text: "EnvÃ­ar orden",
                        class: 'btn-success',
                        role: "cancel",
                        handler: (data) => {
                            this.User.telegramDispatcher({message:data.text},(response)=>{
                                alertInfo({
                                    icon:'<i class="bi bi-ui-checks"></i>',
                                    message: 'Orden enviada con Ã©xito',
                                    _class:'bg-gradient-success text-white'
                                })
                            })
                        },
                    },
                    {
                        text: "Cancelar",
                        role: "cancel",
                        handler: (data) => {
                        },
                    },
                ],
            })

            alertCtrl.present(alert.modal);
        },
        getUserTradesAndDataMain() {
            this.busy = true

            this.getTradingAccount().then((response)=>{
                this.busy = false
                this.account = response.account
                
                this.getTradingAccountMaster()

                this.connect()
                this.calculateDrawDownInfo()
            }).catch((err)=>{
                this.trades = false
                this.tradesAux = false
            })
        },
        getTradingAccountMaster() {
            this.trades = null
            this.tradesAux = null
            this.getUserTrades().then((trades)=>{
                this.trades = trades
                this.tradesAux = trades
            })
        },
        getTradingAccount() {
            return new Promise((resolve, reject) => {
                this.busy = true
                this.User.getTradingAccount(this.filter,(response)=>{
                    this.busy = false
                    if(response.s == 1)
                    {
                        resolve(response)
                    }

                    reject()
                })
            })
        },
        getUserTrades() {
            return new Promise((resolve, reject) => {
                this.busy = true
                this.User.getUserTrades(this.filter,(response)=>{
                    this.busy = false
                    if(response.s == 1)
                    {
                        resolve(response.trades)
                    }

                    reject()
                })
            })
        },
        getProfit(trades) {
            this.account.profit = 0 

            trades.map((trade) => this.account.profit += trade.profit)
        },
        updateTradesFromAccount(trades) {
            console.log("updating account trades")

            this.updatingTrades = true

            this.User.updateTradesFromAccount({user_trading_account_id:this.account.user_trading_account_id,trades:trades},(response)=>{
                this.updatingTrades = false

                if(response.s == 1)
                {
                    this.updateTradesFromAccountInt(trades)
                    this.getProfit(trades)
                }
            })
        },
        async watchAccountInformation() {
            this.updateUserTradingAccount(await this.connection.getAccountInformation())
            
            this.interval = setInterval(async () => {
                this.updateUserTradingAccount(await this.connection.getAccountInformation())
            },10000)
        },
        existTrade(ticket) {
            return this.trades.find(trade => trade.ticket == ticket)
        },
        updateTradesFromAccountInt(trades) {
            trades.map((trade) => {
                let ticket_ = this.existTrade(trade.id)

                if(ticket_)
                {
                    ticket_.profit = trade.profit
                }
            })
        },
        async watchTradesInfo() {
            this.updateTradesFromAccount(await this.connection.getPositions())

            this.intervalTrades = setInterval(async () => {
                this.updateTradesFromAccount(await this.connection.getPositions())

                // this.trades.map(async (trade) => {
                //     const deal = await this.connection.getDealsByPosition(trade.ticket);
                    
                //     if(deal)
                //     {
                //         if(deal.deals[1])
                //         {
                //             trade.profit = deal.deals[1].profit
                //         }
                //     }
                // })
            },10000)
        },
        getTradingAccounts() {
            this.busy = true
            return new Promise((resolve, reject) => {
                this.busy = false
                this.User.getTradingAccounts({},(response)=>{
                    if(response.s == 1)
                    {
                        resolve(response.accounts)
                    } 
                })
            })
        },
        updateUserTradingAccount(account)
        {
            console.log(`updating account data`)

            account.user_trading_account_id = this.account.user_trading_account_id

            this.User.updateUserTradingAccount(account,(response)=>{
                if(response.s == 1)
                {
                    this.account.equity = account.equity
                    this.account.balance = account.balance
                    this.account.drawdown = response.drawdown

                    this.calculateDrawDownInfo()
                }
            })
        },
        calculateDrawDownInfo()
        {
            this.account.drawdownPercentage = Math.round((this.account.drawdown * 100) / this.account.initial_drawdown)

            this.account.drawdownPercentageClass = 'bg-gradient-success'
            
            if(this.account.drawdownPercentage >= 0 && this.account.drawdownPercentage < 33)
            {
                this.account.drawdownPercentageText = 'be careful'
                this.account.drawdownPercentageClass = 'bg-gradient-success'
            } else if(this.account.drawdownPercentage > 33 && this.account.drawdownPercentage < 66) {
                this.account.drawdownPercentageText = 'high drawdown is in risk'
                this.account.drawdownPercentageClass = 'bg-warning'
            } else if(this.account.drawdownPercentage > 66 && this.account.drawdownPercentage <= 100) {
                this.account.drawdownPercentageText = 'Â¡Danger! drawdown is almost reached'
                this.account.drawdownPercentageClass = 'bg-danger'
            } else if(this.account.drawdownPercentage >= 100) {
                this.account.drawdownPercentageText = 'Â¡Drawdown is reached!'
                this.account.drawdownPercentageClass = 'bg-danger'
            }
        },
        async connectRPC() {
            this.connectionRpc = this.accountMT.getRPCConnection();
            await connectionRpc.connect();

            // wait until terminal state synchronized to the local state
            log('Waiting for SDK to synchronize to terminal state (may take some time depending on your history size)');

            await connectionRpc.waitSynchronized();
        },
        async connect() {
            this.loadingInfo = true

            this.accountMT = await this.api.metatraderAccountApi.getAccount(this.account.id)

            await this.accountMT.waitConnected();

            this.connection = await this.accountMT.getRPCConnection();

            await this.connection.connect()
            await this.connection.waitSynchronized()

            this.terminalState = this.connection.terminalState;

            this.watchAccountInformation()

            this.watchTradesInfo()

            this.loadingInfo = false
        }
    },
    mounted() {
        this.api = new MetaApi(this.token);

        this.getTradingAccounts().then((accounts)=>{
            this.accounts = accounts
            this.filter.user_trading_account_id = getParam("utaid") ? getParam("utaid") : this.accounts[0].user_trading_account_id

            this.getUserTradesAndDataMain()
        })
    },
    template : `
        <div v-if="busy" class="justify-content-center d-flex">
            <div class="spinner-grow text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <div v-if="accounts">
            <div class="card mb-3 animation-fall-down" style="--delay:750ms">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-12 col-xl">
                            <div class="form-floating mb-3 mb-xl-0">
                                <input type="date" v-model="filter.start_date" class="form-control" id="start_date" placeholder="name@example.com">
                                <label for="start_date">Desde</label>
                            </div>
                        </div>
                        <div class="col-12 col-xl">
                            <div class="form-floating mb-3 mb-xl-0">
                                <input type="date" v-model="filter.end_date" class="form-control" id="end_date" placeholder="name@example.com">
                                <label for="end_date">Hasta</label>
                            </div>
                        </div>
                        <div v-if="accounts" class="col-12 col-xl">
                            <div class="form-floating mb-3 mb-xl-0">

                                <select class="form-select" id="user_trading_account_id" v-model="filter.user_trading_account_id" aria-label="Selecciona tu cuenta">
                                    <option v-for="account in accounts" v-bind:value="account.user_trading_account_id">
                                        {{ account.server }} - {{ account.login }}
                                    </option>
                                </select>

                                <label for="user_trading_account_id">Selecciona tu cuenta</label>
                            </div>
                        </div>
                        <div class="col-12 col-xl-auto">
                            <button :disabled="busy" @click="getUserTradesAndDataMain" class="btn btn-primary mb-0 shadow-none">
                                <div v-if="!busy">
                                    Aplicar
                                </div>
                                <div v-else>
                                    ...
                                </div>
                            </button>
                        </div>
                        <div class="col-12 col-xl-auto">
                            <button :disabled="busy" @click="toggleOrderMaker" class="btn btn-success me-1 mb-0 shadow-none">
                                <span v-if="busy">
                                    ...
                                </span>
                                <span v-else>
                                    Nueva Orden
                                </span>
                            </button>
                            <button :disabled="busy" @click="toggleVars" class="btn btn-success me-1 mb-0 shadow-none">
                                <span v-if="busy">
                                    ...
                                </span>
                                <span v-else>
                                    Variables
                                </span>
                            </button>
                            <input type="checkbox" class="btn-check" v-model="autoUpdate" id="btn-check" autocomplete="off">
                            <label class="btn btn-warning mb-0 shadow-none" for="btn-check"><i class="bi bi-bootstrap-reboot"></i></label>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="account" class="row position-relative align-items-center mb-3 animation-fall-down" style="--delay:800ms">  
                <div v-if="loadingInfo" class="position-absolute top-50 z-index-1">
                    <div class="justify-content-center d-flex">
                        <div class="spinner-grow text-white" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl">  
                    <div class="card">  
                        <span class="mask opacity-9 bg-gradient-success border-radius-xl"></span>
                        <div class="card-body p-3 position-relative">   
                            <div class="icon icon-shape bg-white shadow text-center border-radius-md"><i class="bi bi-currency-dollar text-dark text-gradient text-lg opacity-10" aria-hidden="true"></i></div>
                            <h5 class="text-white font-weight-bolder mb-0 mt-3">
                                $ {{account.balance.numberFormat(2)}}
                            </h5>
                            <span class="text-white text-sm">
                                Current balance
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl">  
                    <div class="card">  
                        <span class="mask opacity-9 border-radius-xl" :class="account.drawdownPercentageClass"></span>
                        <div class="card-body p-3 position-relative">   
                            <div class="icon icon-shape bg-white shadow text-center border-radius-md"><i class="bi bi-bar-chart-fill text-dark text-gradient text-lg opacity-10" aria-hidden="true"></i></div>
                            <h5 v-if="account.drawdown !== false" class="text-white font-weight-bolder mb-0 mt-3">
                                {{account.drawdown.numberFormat(2)}} %
                            </h5>
                            <span class="text-white text-xs">
                                DD - {{account.drawdownPercentageText}}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl">  
                    <div class="card">  
                        <span class="mask bg-gradient-primary opacity-9 border-radius-xl"></span>
                        <div class="card-body p-3 position-relative">   
                            <div class="icon icon-shape bg-white shadow text-center border-radius-md"><i class="bi bi-currency-dollar text-dark text-gradient text-lg opacity-10" aria-hidden="true"></i></div>
                            <h5 class="text-white font-weight-bolder mb-0 mt-3">
                                $ {{account.equity.numberFormat(2)}} 
                            </h5>
                            <span class="text-white text-sm">
                                Equity 
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl">  
                    <div class="card">  
                        <span class="mask bg-gradient-dark opacity-9 border-radius-xl"></span>
                        <div class="card-body p-3 position-relative">  
                            <div class="icon icon-shape bg-white shadow text-center border-radius-md"><i class="bi bi-currency-dollar text-dark text-gradient text-lg opacity-10" aria-hidden="true"></i></div>
                            
                            <h5 class="text-white font-weight-bolder mb-0 mt-3">
                                $ {{account.initial_balance.numberFormat(2)}}
                            </h5>
                            <span class="text-white text-sm">Initial balance</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl">  
                    <div class="card">  
                        <span class="mask bg-gradient-dark opacity-9 border-radius-xl"></span>
                        <div class="card-body p-3 position-relative">  
                            <div class="icon icon-shape bg-white shadow text-center border-radius-md"><i class="bi bi-bar-chart-fill text-dark text-gradient text-lg opacity-10" aria-hidden="true"></i></div>

                            <h5 class="text-white font-weight-bolder mb-0 mt-3">
                                {{account.initial_drawdown.numberFormat(2)}} %
                            </h5>
                            <span class="text-white text-sm">Max Drawdown</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl">  
                    <div class="card">  
                        <span class="mask bg-gradient-primary opacity-9 border-radius-xl"></span>
                        <div class="card-body p-3 position-relative">  
                            <div class="icon icon-shape bg-white shadow text-center border-radius-md"><i class="bi bi-currency-dollar text-dark text-gradient text-lg opacity-10" aria-hidden="true"></i></div>

                            <h5 class="text-white font-weight-bolder mb-0 mt-3">
                                <span v-if="account.profit">
                                    $ {{account.profit.numberFormat(2)}} 
                                </span>
                                <span v-else>
                                    ...
                                </span>
                            </h5>
                            <span class="text-white text-sm">Profit</span>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="trades" class="card animation-fall-down" style="--delay:500ms">
                <div class="table align-items-center mb-0">
                    <div class="card-header">
                        <div class="row">
                            <div v-if="updatingTrades" class="col col-xl-auto">
                                <div class="spinner-grow text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="text-primary fs-4 fw-semibold">Trades</div>
                            </div>
                            <div class="col-6">
                                <input :autofocus="true" v-model="query" class="form-control" type="search" placeholder="buscar por ticket, activo, precio entrada o beneficio neto...">
                            </div>
                            <div class="col">
                                <input :autofocus="true" v-model="query" class="form-control" type="search" placeholder="buscar por ticket, activo, precio entrada o beneficio neto...">
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-2">
                        <table class="table align-items-center mb-0">
                            <thead class="">
                                <tr class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    <td class="">
                                        <div class="form-check">
                                            <input v-model="filter.checkedAll" class="form-check-input" type="checkbox" id="checkedAll">
                                        </div>
                                    </td>
                                    <th @click="sortData(columns.ticket)" class="text-uppercase text-secondary cursor-pointer text-xxs font-weight-bolder opacity-7">
                                        <span v-if="columns.ticket.desc">
                                            <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                        </span>    
                                        <span v-else>    
                                            <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                        </span>    
                                        <u class="text-sm ms-2">Ticket</u>
                                    </th>
                                    <th @click="sortData(columns.symbol)" class="text-uppercase text-secondary cursor-pointer text-xxs font-weight-bolder opacity-7">
                                        <span v-if="columns.symbol.desc">
                                            <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                        </span>    
                                        <span v-else>    
                                            <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                        </span>    
                                        <u class="text-sm ms-2">Activo</u>
                                    </th>
                                    <th @click="sortData(columns.buy)" class="text-uppercase text-secondary cursor-pointer text-xxs font-weight-bolder opacity-7">
                                        <span v-if="columns.buy.desc">
                                            <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                        </span>    
                                        <span v-else>    
                                            <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                        </span>    
                                        <u class="text-sm ms-2">Lado</u>
                                    </th>
                                    <th @click="sortData(columns.create_date)" class="text-uppercase text-secondary cursor-pointer text-xxs font-weight-bolder opacity-7">
                                        <span v-if="columns.create_date.desc">
                                            <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                        </span>    
                                        <span v-else>    
                                            <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                        </span>    
                                        <u class="text-sm ms-2">Apertura</u>
                                    </th>
                                    <th @click="sortData(columns.price)" class="text-center c-pointer text-uppercase text-secondary font-weight-bolder opacity-7">
                                        <span v-if="columns.price.desc">
                                            <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                        </span>    
                                        <span v-else>    
                                            <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                        </span>    
                                        <u class="text-sm ms-2">Precio entrada</u>
                                    </th>
                                    <th @click="sortData(columns.price)" class="text-center c-pointer text-uppercase text-secondary font-weight-bolder opacity-7">
                                        <span v-if="columns.price.desc">
                                            <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                        </span>    
                                        <span v-else>    
                                            <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                        </span>    
                                        <u class="text-sm ms-2">Lotaje</u>
                                    </th>
                                    <th @click="sortData(columns.profit)" class="text-center c-pointer text-uppercase text-secondary font-weight-bolder opacity-7">
                                        <span v-if="columns.profit.desc">
                                            <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                        </span>    
                                        <span v-else>    
                                            <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                        </span>    
                                        <u class="text-sm ms-2">Beneficio neto</u>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(trade,index) in trades" class="text-xs text-center align-middle">
                                    <td>
                                        <div v-if="!trade.close" class="form-check">
                                            <input v-model="trade.checked" class="form-check-input" type="checkbox" value="" id="index">
                                        </div>
                                    </td>
                                    <td>
                                        {{trade.ticket}}
                                    </td>
                                    <td>
                                        {{trade.symbol}}
                                    </td>
                                    <td>
                                        <span v-if="trade.buy" class="text-success">
                                            Buy
                                        </span>
                                        <span v-else class="text-sell">
                                            Sell
                                        </span>
                                    </td>
                                    <td>
                                        {{trade.create_date.formatFullDate()}}
                                    </td>
                                    <td>
                                        {{trade.price}}
                                    </td>
                                    <td>
                                        {{trade.lotage}}
                                    </td>
                                    <td>
                                        <span v-if="trade.profit > 0" class="text-success fw-semibold">
                                            + {{trade.profit}}
                                        </span>
                                        <span v-else-if="trade.profit < 0" class="text-danger fw-semibold">
                                            {{trade.profit}}
                                        </span>
                                        <span v-else class="text-secondary fw-semibold">
                                            {{trade.profit}}
                                        </span>
                                    </td>
                                    <td>
                                        <button :disabled="trade.busy" v-if="!trade.close" @click="closeOrder(trade)" class="btn btn-danger mb-0 shadow-none btn-sm px-3">Close</button>
                                        
                                        <div v-if="trade.busy"class="spinner-grow text-primary  spinner-grow-sm" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </table>
                </div>
            </div>
        </div>
        <div v-else-if="trades == false" class="alert alert-info text-center text-white alert-dismissible fade show" role="alert">
            <div class="fs-5 fw-semibold">Comienza a hacer trades en tu cuenta</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i class="bi bi-x"></i></button>
        </div>            
    `,
}

export { TradesViewer } 