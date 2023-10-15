import { User } from '../../src/js/user.module.js?v=2.6.4'   

const TradesbinanceViewer = {
    name : 'tradesbinance-viewer',
    emits : ['toggleOrderMaker'],
    data() {
        return {
            User : new User,
            query : null,
            account : null,
            accountInfo : null
        }
    },
    watch: {
        query: {
            handler() {
                this.filterBalances()
            },
            deep : true
        }
    },
    methods: {
        filterBalances() {
            this.accountInfo.balances = this.accountInfo.balancesAux
            this.accountInfo.balances = this.accountInfo.balances.filter((asset)=>{
                return asset.asset.toLowerCase().includes(this.query.toLowerCase())
            })
        },
        formatBalances() {
            this.query = null
            this.accountInfo.balances = this.accountInfo.balancesAux.filter((asset)=>{
                return parseFloat(asset.free) > 0
            })
        },
        getBinanceBalance() {
            this.User.getBinanceBalance({user_trading_account_id:this.account.user_trading_account_id},(response)=>{
                if(response.s == 1)
                {
                    this.formatBalances()
                }
            })
        },
        getBinanceTradeFee() {
            this.User.getBinanceTradeFee({user_trading_account_id:this.account.user_trading_account_id},(response)=>{
                if(response.s == 1)
                {
                    this.formatBalances()
                }
            })
        },
        getBinanceTrades() {
            this.User.getBinanceTrades({user_trading_account_id:this.account.user_trading_account_id},(response)=>{
                if(response.s == 1)
                {
                    
                }
            })
        },
        getBinanceAccount() {
            this.User.getBinanceAccount({user_trading_account_id:this.account.user_trading_account_id},(response)=>{
                if(response.s == 1)
                {
                    this.accountInfo = response.accountInfo
                    this.accountInfo.balancesAux = response.accountInfo.balances

                    this.formatBalances()
                }
            })
        },
        getTradingAccount(user_trading_account_id) {
            return new Promise((resolve, reject) => {
                this.busy = true
                this.User.getTradingAccount({user_trading_account_id:user_trading_account_id},(response)=>{
                    this.busy = false

                    if(response.s == 1)
                    {
                        resolve(response.account)
                    }

                    reject()
                })
            })
        },
        orderTest() {
            this.User.orderTest({},(response)=>{
                this.busy = false

                if(response.s == 1)
                {
                }
            })
        },
    },
    mounted() {
        if(getParam("utaid"))
        {
            this.getTradingAccount(getParam("utaid")).then((account)=>{
                this.account = account

                this.getBinanceAccount()
                this.getBinanceTrades()
            })
        }
    },
    template : `
        <button @click="orderTest" class="btn btn-primary">Order TEst</button>
        <div class="card mb-3">
            <div class="card-header pb-0">
                <div class="fs-4 text-primary fw-semibold">Binance account</div>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-12 col-xl-6">
                        <div @click="account.show = !account.show" class="text-xs text-secondary">
                            <button class="btn btn-primary mb-0 btn-sm px-3">Copy</button>
                            apiKey
                        </div>
                        <div class="row d-flex">
                            <div class="col-12 col-xl-10">
                                <div class="fw-sembold">
                                    <div>
                                        <span v-if="account.show">
                                            {{account.login}} 
                                        </span>
                                        <span v-else>
                                            {{account.login.hideText(20)}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-xl-auto">
                                <div class="d-grid">
                                    <button class="btn btn-primary">Copy</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-6">
                        <div @click="account.showPassword = !account.showPassword" class="text-xs text-secondary">
                            <button class="btn btn-primary mb-0 btn-sm px-3">Copy</button>
                            apiSecret
                        </div>
                        <div class="row d-flex">
                            <div class="col-12 col-xl-10">
                                <div class="fw-sembold">
                                    <div>
                                        <span v-if="account.showPassword">
                                            {{account.password}} 
                                        </span>
                                        <span v-else>
                                            {{account.password.hideText(20)}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-xl-auto">
                                <div class="d-grid">
                                    <button class="btn btn-primary">Copy</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="accountInfo" class="row">
            <div class="col-12 col-xl-6">
                <div v-if="accountInfo.balances" class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-12 col-xl fs-5 fw-semibold text-primary">
                                Assets in your account
                            </div>
                            <div class="col-12 col-xl-auto">
                                <input v-model="query" type="text" class="form-control" placeholder="Search..."/>
                            </div>
                            <div class="col-12 col-xl-auto">
                                <button @click="formatBalances" class="btn mb-0 shadow-none btn-primary">Compact</button>
                            </div>
                        </div>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li v-for="balance in accountInfo.balances" class="list-group-item">
                            <div class="row justify-content-center align-items-center">
                                <div class="col-12 col-xl text-dark">
                                    {{balance.asset}}
                                </div>
                                <div class="col-12 col-xl text-end fw-semibold text-primary">
                                    {{balance.free.numberFormat(6)}}
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-12 col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-12 col-xl fs-5 fw-semibold text-primary">
                                Account info
                            </div>
                        </div>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="row justify-content-center align-items-center">
                                <div class="col-12 col-xl text-dark">
                                    Can trade
                                </div>
                                <div class="col-12 col-xl text-end fw-semibold text-primary">
                                    {{accountInfo.canTrade}}
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row justify-content-center align-items-center">
                                <div class="col-12 col-xl text-dark">
                                    Account type
                                </div>
                                <div class="col-12 col-xl text-end fw-semibold text-primary">
                                    {{accountInfo.accountType}}
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row justify-content-center align-items-center">
                                <div class="col-12 col-xl text-dark">
                                    Update date
                                </div>
                                <div class="col-12 col-xl text-end fw-semibold text-primary">
                                    {{accountInfo.updateTime}}
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    `,
}

export { TradesbinanceViewer } 