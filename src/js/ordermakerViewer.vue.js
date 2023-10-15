import { User } from '../../src/js/user.module.js?v=2.5.1'   

const OrdermakerViewer = {
    name : 'ordermaker-viewer',
    props: ['showOrderMaker'],
    emits: ['sendOrder','sendOrderAsSignal','openCanvasTop'],
    data() {
        return {
            User : new User,
            filled : true,
            symbols : null,
            symbolsAux : null,
            symbolsVisible : false,
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
                if(signal.takeProfit && signal.stopPrice)
                {
                    return `${signal.type}=${signal.side},${signal.quantity},${signal.takeProfit},${signal.stopPrice},${signal.symbol}`
                } else {
                    return `${signal.type}=${signal.side},${signal.quantity},${signal.symbol}`
                }
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
        sendOrderAsSignal() {
            $(this.$refs.offcanvasRight2).offcanvas('hide')

            this.$emit('sendOrderAsSignal', this.signal)
        }
    },
    mounted() {
        this.signal = this.signalAux

        this.getAllCriptoSymbolsMaster()
    },
    template : `
    <div class="offcanvas offcanvas-end blur shadow-blur overflow-scroll" tabindex="-1" ref="offcanvasRight2" id="offcanvasRight2" aria-labelledby="offcanvasRightLabel">
        <div>
            <div class="offcanvas-header">
                <div id="offcanvasRightLabel">
                    <div class="h4">
                        Nueva orden
                    </div>
                </div>
                <button type="button" class="btn btn-sm px-3 shadow-none btn-danger" data-bs-dismiss="offcanvas" aria-label="Close"><i class="bi fs-5 bi-x"></i> </button>
            </div>
            <div class="offcanvas-body">
                <div class="card shadow-none">
                    <div class="card-body">
                        <div class="d-flex justify-content-center">
                            <button @click="openTradingView" type="button" class="btn btn-sm btn-success">Abrir trading view</button>
                        </div>

                        <div class="row align-items-center p-3">
                            <div class="col-12">
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
                                        Take Profit
                                    </label>

                                    <button @click="getPriceFromSymbol('takeProfit',signal.symbol)" style="margin-top:0.6rem" class="btn btn-sm btn-primary mb-0 position-absolute end-0 top-0 px-3 btn-lg me-2">$</button>
                                </div>
                                <div class="form-floating mb-3">
                                    <input ref="stopPrice" :autofocus="true" v-model="signal.stopPrice" @keypress.enter.exact="$refs.stopPrice.focus()" type="number" class="form-control" id="stopPrice" placeholder="Stop loss">
                                    <label for="stopPrice">
                                        Stop loss
                                    </label>

                                    <button @click="getPriceFromSymbol('stopPrice',signal.symbol)" style="margin-top:0.6rem" class="btn btn-sm btn-primary mb-0 position-absolute end-0 top-0 px-3 btn-lg me-2">$</button>
                                </div>

                                <div class="position-relative">

                                    <div class="form-floating mb-3">
                                        <input ref="quantity" v-model="signal.quantity" type="text" class="form-control" id="quantity" placeholder="Lotage">
                                        <label for="quantity">Lotaje</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input ref="symbol" @keypress="searchSymbol(this)" v-model="signal.symbol" type="text" class="form-control" id="symbol" placeholder="Par">
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
                            </div>

                            <button @click="sendOrder" class="btn btn-primary mb-1">
                                Envíar orden
                            </button>
                            <button @click="sendOrderAsSignal" class="btn btn-primary mb-0">
                                Envíar como señal (sólo recibirás tu)
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { OrdermakerViewer } 