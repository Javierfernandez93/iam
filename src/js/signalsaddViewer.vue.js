import { User } from '../../src/js/user.module.js?v=2.6.5'   

const SignalsaddViewer = {
    name : 'signalsadd-viewer',
    emit : ['openCanvasTop'],
    data() {
        return {
            User : new User,
            filled : false,
            sending : false,
            followers : null,
            followersSent : 0,
            symbolsCategories : null,
            symbols : null,
            symbolsAux : null,
            symbolsVisible : false,
            channel : null,
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
                quantity: 1,
                comment: '',
                side: 'buy',
                type: 'oco',
                price: 0,
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
        setChannel(channel) {
            this.sending = false
            this.channel = channel
            this.signal = this.signalAux
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
        sendMessageToChannel() {
            this.User.sendMessageToChannel({
                telegram_channel_id : this.channel.telegram_channel_id,
                telegram_api_id : this.channel.telegram_channel.telegram_api_id,
                message : formatSignal(this.signal),
                signal : this.signal
            },(response)=>{
                if(response.s == 1)
                {
                    $(this.$refs.offcanvasRight).offcanvas('hide')
                }
            })
        },
        sendMessageToUser(data) {
            return new Promise((resolve,reject)=>{
                this.User.sendMessageToUser(data,(response)=>{
                    resolve(response)
                })
            })
        },
        closeOffset() {
            $(this.$refs.offcanvasRight).offcanvas('hide')
        },
        sendMessageToUsers() {
            this.User.getFollowers({telegram_api_id:this.channel.telegram_channel.telegram_api_id},(response)=>{
                if(response.s == 1)
                {
                    this.followers = response.followers

                    this.sending = true

                    for(let follower of this.followers)
                    {
                        this.sendMessageToUser({
                            chat_id : follower.chat_id,
                            telegram_api_id : this.channel.telegram_channel.telegram_api_id,
                            signal_provider_id : response.signalProvider.signal_provider_id,
                            signal: this.signal
                        }).then((response)=>{
                            this.followersSent += 1
                        })
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
        openOffCanvas() {   
            $(this.$refs.offcanvasRight).offcanvas('show')
        },
    },
    mounted() {
        this.signal = this.signalAux

        this.getAllCriptoSymbolsMaster()
    },
    template : `
        <div class="offcanvas offcanvas-end blur shadow-blur overflow-scroll" tabindex="-1" ref="offcanvasRight" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
            <div v-if="channel">
                <div class="offcanvas-header">
                    <div id="offcanvasRightLabel">
                        <div class="text">
                            Canal 
                        </div>
                        <div class="h5">
                            {{ channel.name }}
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm px-3 shadow-none btn-danger" data-bs-dismiss="offcanvas" aria-label="Close"><i class="bi fs-5 bi-x"></i> </button>
                </div>

                <div class="justify-content-center text-center">
                    <button @click="openTradingView" type="button" class="btn p-0 btn-link">Abrir trading view</button>
                </div>

                <div class="offcanvas-body">
                    <div class="card bg-white lead">
                        <div v-if="!sending">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-12">
                                        <div class="mb-1">
                                            <div class="text-xs mb-1">Mercado</div>
                                            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                                <input v-model="signal.market_type" type="radio" class="btn-check" name="market_type" value="1" id="forex" autocomplete="off" checked>
                                                <label class="btn btn-outline-primary" for="forex">Forex</label>
                                            
                                                <input v-model="signal.market_type" type="radio" class="btn-check" name="market_type" value="2" id="crypto" autocomplete="off">
                                                <label class="btn btn-outline-primary" for="crypto">Crypto</label>
                                            </div>
                                        </div>
                                        <div class="mb-1">
                                            <div class="text-xs mb-1">Tipo de operación</div>
                                            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                                <input v-model="signal.type" type="radio" class="btn-check" name="type" value="market" id="market" autocomplete="off" checked>
                                                <label class="btn btn-outline-primary" for="market">
                                                    Mercado
                                                </label>
                                            
                                                <input v-model="signal.type" type="radio" class="btn-check" name="type" value="oco" id="oco" autocomplete="off">
                                                <label class="btn btn-outline-primary" for="oco">
                                                    <span v-if="signal.market_type == CATALOG_TRADING_ACCOUNTS.CRYPTO">
                                                        OCO 
                                                    </span>
                                                    <span v-else-if="signal.market_type == CATALOG_TRADING_ACCOUNTS.FOREX">
                                                        LIMIT (TP y SL)
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-1">
                                            <div class="text-xs mb-1">Lado</div>
                                            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                                <input v-model="signal.side" type="radio" class="btn-check" name="side" value="buy" id="buy" autocomplete="off" checked>
                                                <label class="btn btn-outline-primary" for="buy">Compra</label>

                                                <input v-model="signal.side" type="radio" class="btn-check" name="side" value="sell" id="sell" autocomplete="off">
                                                <label class="btn btn-outline-primary" for="sell">Venta</label>
                                            </div>
                                        </div>
                                        <div class="mb-1">
                                            <div class="form-floating d-none mb-3">
                                                <input ref="quantity" :autofocus="true" v-model="signal.quantity" @keypress.enter.exact="$refs.symbol.focus()" type="number" class="form-control" id="quantity" placeholder="quantity">
                                                <label for="quantity">Cantidad</label>
                                            </div>
                                        </div>

                                        <div v-if="signal.type == ORDERS_TYPES.OCO">
                                            <div v-if="signal.market_type == CATALOG_TRADING_ACCOUNTS.FOREX">
                                                <div class="form-floating mb-1">
                                                    <input ref="priceEntrace" :autofocus="true" v-model="signal.priceEntrace" @keypress.enter.exact="$refs.takeProfit.focus()" type="number" class="form-control" id="priceEntrace" placeholder="Precio de entrada">
                                                    <label for="priceEntrace">
                                                        Precio de entrada
                                                    </label>
                                                </div>
                                                <div class="form-floating mb-1">
                                                    <input ref="takeProfit" :autofocus="true" v-model="signal.takeProfit" @keypress.enter.exact="$refs.stopPrice.focus()" type="number" class="form-control" id="takeProfit" placeholder="Take profit">
                                                    <label for="takeProfit">
                                                        Take Profit
                                                    </label>
                                                </div>
                                                <div class="form-floating mb-1">
                                                    <input ref="stopPrice" :autofocus="true" v-model="signal.stopPrice" @keypress.enter.exact="$refs.stopPrice.focus()" type="number" class="form-control" id="stopPrice" placeholder="Stop loss">
                                                    <label for="stopPrice">
                                                        Stop loss
                                                    </label>
                                                </div>
                                            </div>
                                            <div v-if="signal.market_type == CATALOG_TRADING_ACCOUNTS.CRYPTO">
                                                <div class="form-floating mb-1">
                                                    <input ref="price" :autofocus="true" v-model="signal.price" @keypress.enter.exact="$refs.stopPrice.focus()" type="number" class="form-control" id="price" placeholder="Lotaje">
                                                    <label for="price">
                                                        Precio (take profit)
                                                    </label>
                                                </div>
                                                
                                                <div class="form-floating mb-3">
                                                    <input ref="stopPrice" :autofocus="true" v-model="signal.stopPrice" @keypress.enter.exact="$refs.stopLimitPrice.focus()" type="number" class="form-control" id="stopPrice" placeholder="stopPrice">
                                                    <label for="stopPrice">Stop</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input ref="stopLimitPrice" :autofocus="true" v-model="signal.stopLimitPrice" @keypress.enter.exact="$refs.symbol.focus()" type="number" class="form-control" id="stopLimitPrice" placeholder="stopLimitPrice">
                                                    <label for="stopLimitPrice">Limit</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="position-relative">
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

                                        <div class="form-floating mb-3">
                                            <input ref="comment" v-model="signal.comment" type="text" class="form-control" id="comment" placeholder="comment">
                                            <label for="comment">Comentario</label>
                                        </div>
                                    </div>
                                </div>
                            
                                <div class="d-grid">
                                    <button @click="sendMessageToUsers" class="btn btn-primary mb-0">
                                        Envíar señal
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div v-else class="card-body">
                            <div v-if="followers">
                                <div class="text-center text-xs mb-3">
                                    Enviando <b>{{followersSent}}</b> de <b>{{followers.length}}</b>
                                </div>

                                <div class="progress" role="progressbar" aria-label="Basic example" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar" :style="{'width': followersSent.getPercentajeReverse(followers.length) + '%'}"></div>
                                </div>

                                <div class="mt-3 justify-content-center text-center">
                                    <button type="button" @click="closeOffset" class="btn btn-danger">cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { SignalsaddViewer } 