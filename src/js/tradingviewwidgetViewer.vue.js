import { User } from '../../src/js/user.module.js?v=2.6.6'   

const TradingviewwidgetViewer = {
    name : 'tradingviewwidget-viewer',
    data() {
        return {
            User : new User,
            loaded : false,
            symbol : null,
            view : false
        }
    },
    methods: {
        initTradingView(symbol) {   
            this.view = true

            $(this.$refs.offcanvasTop).offcanvas('show')

            if(!this.loaded || this.symbol != symbol)
            {
                this.loaded = true
                this.symbol = symbol
    
                new TradingView.widget({
                    "autosize": true,
                    "symbol": symbol ?? "NASDAQ:AAPL",
                    "interval": "D",
                    "timezone": "Etc/UTC",
                    "theme": "dark",
                    "style": "1",
                    "locale": "en",
                    "toolbar_bg": "#f1f3f6",
                    "enable_publishing": false,
                    "withdateranges": true,
                    "hide_side_toolbar": false,
                    "allow_symbol_change": true,
                    "show_popup_button": true,
                    "popup_width": "1000",
                    "popup_height": "650",
                    "container_id": "tradingview_3884c"
                });
            }
        },
    },
    mounted() {
        
    },
    template : `
        <div class="offcanvas offcanvas-bottom offcanvas-trading-view blur shadow-blur" tabindex="-1" ref="offcanvasTop" id="offcanvasTop" aria-labelledby="offcanvasTopLabel">
            <div>
                <div class="offcanvas-header">
                    <h5 id="offcanvasTopLabel">
                        TradingView
                    </h5>
                    <button type="button" class="btn btn-sm px-3 shadow-none btn-danger" data-bs-dismiss="offcanvas" aria-label="Close"><i class="bi fs-5 bi-x"></i> </button>
                </div>
                
                <div class="offcanvas-body">
                    <div class="card bg-white lead">
                        <div class="tradingview-widget-container animation-fall-down" style="--delay:500ms;" :class="view ? '': 'd-none'">
                            <div class="trading-view" id="tradingview_3884c"></div>
                            <div class="tradingview-widget-copyright">
                                <a href="https://www.tradingview.com/" rel="noopener nofollow" target="_blank"><span class="blue-text">Track all markets on TradingView</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { TradingviewwidgetViewer } 