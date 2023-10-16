import { User } from '../../src/js/user.module.js?v=2.6.5'   

const SignalViewer = {
    name : 'signal-viewer',
    props: ['feedback'],
    data() {
        return {
            User : new User,
            filled : true,
            symbols : null,
            isFirstTime : true,
            busy : false,
            symbolsAux : null,
            followed : null,
            symbolsVisible : false,
            singalsProvidersAux : null,
            singalsProviders : null,
            CATALOG_SINGAL_PROVIDER: {
                SEMI_COPY: 1,
                PAMMY: 2,
            },
            CATALOG_TRADING_ACCOUNTS: {
                FOREX: 1,
                CRYPTO: 2,
            },
            ORDERS_TYPES: {
                MARKET: 'market',
                OCO: 'oco'
            },
            signal : null,
            signalAux : {
                market_type: 1,
                symbol: 'EURUSD',
                quantity: 0.1,
                side: 'buy',
                type: 'market',
                price: 0,
                text: 0,
                priceEntrace: 0,
                takeProfit: 0,
                stopPrice: 0, // stopLoss
                stopLimitPrice: 0,
            }
        }
    },
    watch: {
        'signal.market_type': {
            handler() {
                this.getAllCriptoSymbolsMaster()
            },
            deep: true
        },
        signal: {
            handler() {
                // todo custom filled by market type
                this.filled = ['buy','sell'].includes(this.signal.side) && this.signal.symbol 
            },
            deep: true
        }
    },
    methods: {
        copyClipboard(text,target) {
            navigator.clipboard.writeText(text).then(() => {
                let text = target.innerText
                target.innerText = 'Copiado'

                setTimeout(()=>{
                    target.innerText = text
                },3000)
            })
        },
        constructOrder(signal) {
            let text = ''

            if(this.signal.type == this.ORDERS_TYPES.MARKET)
            {
                return `${signal.type}=${signal.side},${signal.quantity},${signal.symbol}`
            } else if(this.signal.type == this.ORDERS_TYPES.OCO) {
                return `${signal.type}=${signal.side},${signal.quantity},${signal.priceEntrace},${signal.takeProfit},${signal.stopPrice},${signal.symbol}`
            }
        },
        toggleModal() {
            setTimeout(() => {
                $(this.$refs.offcanvasRight2).offcanvas('show')
                
                setTimeout(() => {
                    this.$refs.lotage.focus()
                    this.signal.text = this.constructOrder(this.signal)
                }, 500);
            }, 100);
        },
        selectSymbol(symbol) {
            this.signal.symbol = symbol
            this.symbolsVisible = false
        },
        searchSymbol: _debounce((self) => {
            self.symbolsVisible = true
            self.symbols = self.symbolsAux
            self.symbols = self.symbols.filter((symbol)=>{
                return symbol.toLowerCase().includes(self.signal.symbol.toLowerCase())
            })
        },500),
        getAllCriptoSymbols() {
            return new Promise((resolve, reject) =>{
                this.User.getAllCriptoSymbols({market_type:this.signal.market_type},(response)=>{
                    if(response.s == 1)
                    {
                        resolve(response.symbols)
                    }
                })
            })
        },
        getPriceFromSymbol(target,symbol) {
            this.User.getPriceFromSymbol({symbol:symbol},(response)=>{
                if(response.s == 1)
                {
                    if(target == 'takeProfit')
                    {
                        this.signal.takeProfit = response.ask
                    } else if(target == 'stopPrice') {
                        this.signal.stopPrice = response.ask
                    } else if(target == 'priceEntrace') {
                        this.signal.priceEntrace = response.ask
                    }
                }
            })
        },
        getAllCriptoSymbolsMaster() {     
            this.getAllCriptoSymbols().then((symbols)=>{
                this.symbols = symbols
                this.symbolsAux = symbols
            })
        },
        openTradingView() {     
            this.$emit('openCanvasTop',this.signal.symbol)
        },
        sendOrder() {
            $(this.$refs.offcanvasRight2).offcanvas('hide')

            this.$emit('sendOrder', this.constructOrder(this.signal))
        },
        sendVideoTest() {
        },
        sendOrderAsSignal() {
            this.busy = true

            if(this.isFirstTime)
            {
                this.isFirstTime = false;
                this.User.telegramDispatcher({message:'Video de como copiar una señal'},(response)=>{
                    
                })
            }

            setTimeout(()=>{
                this.User.sendOrderAsSignal({signal:this.signal,catalog_trading_account_id:1},(response)=>{
                    this.busy = false
                    
                    alertInfo({
                        icon:'<i class="bi bi-ui-checks"></i>',
                        size:'modal-md',
                        message: `
                            <div class="pb-3">
                                <div class="h3 text-white">¡Genial!</div>
                                <div class="lead text-white">Hemos enviado la señal, puedes aprobarla respondiendo "Sí con 0.01". También hemos enviado un video para que puedas identificar como responder a las señales</div>
                            </div>
                        `,
                        _class:'bg-gradient-success text-white'
                    })
                })
            },500)
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
        this.signal = this.signalAux

        this.getSignalsListsMaster(this.CATALOG_SINGAL_PROVIDER.SEMI_COPY)
        this.getAllCriptoSymbolsMaster()
    },
    template : `
        <div class="row align-items-center justify-content-center p-3">
            <div class="col-12 col-xl-6">
                <div v-if="!followed">
                    <div v-if="singalsProviders">
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
                    <div class="text-center">
                        <div class="h3">¿Terminaste?</div>
                        <button @click="followed = true" class="btn btn-primary">Haz una prueba de señal</button>
                        <a href="../../apps/backoffice" class="btn btn-primary">Ve a tu backoffice</a>
                    </div>
                </div>
                <div v-else class="card card-body shadow-blur blur">
                    <div class="mb-1">
                        <div class="text-xs mb-1">Tipo de operación</div>
                        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                            <input v-model="signal.type" type="radio" class="btn-check" name="type" value="market" id="market" autocomplete="off" checked>
                            <label class="btn btn-outline-primary" for="market">Mercado</label>
                        
                            <input v-model="signal.type" type="radio" class="btn-check" name="type" value="oco" id="oco" autocomplete="off">
                            <label class="btn btn-outline-primary" for="oco">Oco</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="text-xs mb-1">Lado</div>
                        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                            <input v-model="signal.side" type="radio" class="btn-check" name="side" value="buy" id="buy" autocomplete="off" checked>
                            <label class="btn btn-outline-primary" for="buy">Compra</label>

                            <input v-model="signal.side" type="radio" class="btn-check" name="side" value="sell" id="sell" autocomplete="off">
                            <label class="btn btn-outline-primary" for="sell">Venta</label>
                        </div>
                    </div>

                    <div v-if="signal.type == ORDERS_TYPES.OCO">
                        <div v-if="signal.market_type == CATALOG_TRADING_ACCOUNTS.FOREX">
                            <div class="form-floating mb-3">
                                <input ref="priceEntrace" :autofocus="true" v-model="signal.priceEntrace" @keypress.enter.exact="$refs.takeProfit.focus()" type="number" class="form-control" id="priceEntrace" placeholder="Precio de entrada">
                                <label for="priceEntrace">
                                    Precio de entrada
                                </label>

                                <button @click="getPriceFromSymbol('priceEntrace',signal.symbol)" style="margin-top:0.6rem" class="btn btn-sm btn-primary mb-0 position-absolute end-0 top-0 px-3 btn-lg me-2">$</button>
                            </div>
                        </div>
                    </div>
                    <div class="form-floating mb-3">
                        <input ref="takeProfit" :autofocus="true" v-model="signal.takeProfit" @keypress.enter.exact="$refs.stopPrice.focus()" type="number" class="form-control" id="takeProfit" placeholder="Take profit">
                        <label for="takeProfit">
                            Take Profit (Ganancia)
                        </label>

                        <button @click="getPriceFromSymbol('takeProfit',signal.symbol)" style="margin-top:0.6rem" class="btn btn-sm btn-primary mb-0 position-absolute end-0 top-0 px-3 btn-lg me-2">$</button>
                    </div>
                    <div class="form-floating mb-3">
                        <input ref="stopPrice" :autofocus="true" v-model="signal.stopPrice" @keypress.enter.exact="$refs.stopPrice.focus()" type="number" class="form-control" id="stopPrice" placeholder="Stop loss">
                        <label for="stopPrice">
                            Stop loss (Perdida)
                        </label>

                        <button @click="getPriceFromSymbol('stopPrice',signal.symbol)" style="margin-top:0.6rem" class="btn btn-sm btn-primary mb-0 position-absolute end-0 top-0 px-3 btn-lg me-2">$</button>
                    </div>

                    <div class="position-relative">

                        <div class="form-floating mb-3">
                            <input ref="quantity" v-model="signal.quantity" type="text" class="form-control" id="quantity" placeholder="Lotage">
                            <label for="quantity">Lotaje (Pips)</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input ref="symbol" @keypress="searchSymbol(this)" autocomplete="one-time-code" v-model="signal.symbol" type="text" class="form-control" id="symbol" placeholder="Par">
                            <label for="symbol">Par</label>
                        </div>
                        <div v-if="symbols && symbolsVisible" class="card z-index-1 position-absolute top-100 w-100 start-0 overflow-scroll" style="max-height:12rem">
                            <ul v-for="symbol in symbols" class="list-group list-group-flush">
                                <li @click="selectSymbol(symbol)" class="cursor-pointer list-group-item rounded-0">{{symbol}}</li>
                            </ul>
                        </div>    
                    </div>

                    <div class="my-3">
                        <div class="text-xs">salida de orden</div>
                        <div class="text-dark fw-semibold">
                            {{constructOrder(signal)}}

                            <button @click="copyClipboard(constructOrder(signal),$event.target)" class="btn btn-primary mb-0 btn-sm px-3">Copy</button>
                        </div>
                    </div>

                    <button :disabled="busy" v-text="busy ? '...' : 'Envíar señal de prueba'" @click="sendOrderAsSignal" class="btn btn-lg btn-primary mb-3">
                    </button>

                    <a href="../../apps/backoffice" v-if="feedback" class="btn btn-lg btn-secondary mb-0">
                        Ir a mi backoffice
                    </a>
                </div>
            </div>
        </div>
    `,
}

export { SignalViewer } 