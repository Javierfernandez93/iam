import { User } from '../../src/js/user.module.js?v=2.6.5'   

const TradingviewViewer = {
    name : 'tradingview-viewer',
    data() {
        return {
            User : new User
        }
    },
    methods: {
        initTradingView() {     
            new TradingView.widget({
                "autosize": true,
                "symbol": "NASDAQ:AAPL",
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
        },
    },
    mounted() {
        setTimeout(()=>{ this.initTradingView()},100)
    },
    template : `
        <div class="tradingview-widget-container animation-fall-down" style="--delay:500ms;">
            <div class="trading-view" id="tradingview_3884c"></div>
            <div class="tradingview-widget-copyright">
                <a href="https://www.tradingview.com/" rel="noopener nofollow" target="_blank"><span class="blue-text">Track all markets on TradingView</span></a>
            </div>
        </div>
    `,
}

export { TradingviewViewer } 