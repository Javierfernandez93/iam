import { User } from '../../src/js/user.module.js?v=2.6.6'   

const TradingViewer = {
    name : 'trading-viewer',
    data() {
        return {
            User : new User,
            token : 'eyJhbGciOiJSUzUxMiIsInR5cCI6IkpXVCJ9.eyJfaWQiOiI5NzBiYTljZDFlYzhlMjUwNmFhZTM5Y2I1ZDNjNjIyNSIsInBlcm1pc3Npb25zIjpbXSwidG9rZW5JZCI6IjIwMjEwMjEzIiwiaW1wZXJzb25hdGVkIjpmYWxzZSwicmVhbFVzZXJJZCI6Ijk3MGJhOWNkMWVjOGUyNTA2YWFlMzljYjVkM2M2MjI1IiwiaWF0IjoxNjg4NjAzNTIyfQ.QdpD19lO3DkHJWPqEdTxddovaZcyJgHtSuxECvUygT-xAQ7O76uRVrzYCfn9--VbZQeTAqPgkazP8j2Xhlk-NR5S0FQliOcWSqdw-RystKBhwRmsxb8KnKNwtie71mERE2VLV9OJWdhPwIftaMexUJWGFXO7vPU4TaNO16XLZuK8yieiEJElRbDmTe4AKHqvVF5zwbgDWc3ucJTSaoLrYiQe9fk6L81le1KvcxBSawfsh6e5bEJoPZkBWUBEJcifXUIXgYCKAle_iLmo4mXhWDyOpePIzxIOwBuWdHK8MYnut_hKe6D9Xb56yVUa22Y-z0tCoLr0L43W0M3F-AJhCyVzHmsybBbkHS4KtVrPL48FDhHArT0c7hXt5yLfyuRfFmd4QOIzaxHqp7WpwX-auIZchTtz_FqzszoRvOlJTFC4OkfrNIqobKqD0IFjMo57aA6UIkpD5fbAn2t6BJmhXRUcitTu1f1Mxerd42Yt5bQxidXHgItAr7UFrP56m5Gp_i8cz13TMlKxjJoqMphGTJvHldsy3I4RthjGs7IM6q-ETLVP-z9iR49yUdTE-kd_cO9DBbInEierTygLEnjZIIpNN9ZH8GK5HTzjZ37Twl3mXXUQ5KCFV-Yt-In2rubcLEK0vB0cfY4LgqL8nTJ7Pb009jHHi5ZncTXvwx-1NdU',
            api : null
        }
    },
    methods: {
        async connect() {
            const account = await this.api.metatraderAccountApi.getAccount('8bb2eadd-be63-4ce6-b5d6-ce9d8a9d6554')

            await account.waitConnected();
            console.log(account)

            let connection = await account.getStreamingConnection();
            await connection.connect()
            await connection.waitSynchronized()

            console.log('Testing terminal state access');
            let terminalState = connection.terminalState;
            console.log('connected:', terminalState.connected);
            console.log('connected to broker:', terminalState.connectedToBroker);
            console.log('account information:', JSON.stringify(terminalState.accountInformation));
            console.log('positions:', JSON.stringify(terminalState.positions));
            console.log('orders:', JSON.stringify(terminalState.orders));
            console.log('specifications:', JSON.stringify(terminalState.specifications));
            console.log('EURUSD specification:', JSON.stringify(terminalState.specification('EURUSD')));
            console.log('EURUSD price:', JSON.stringify(terminalState.price('EURUSD')));

            let historyStorage = connection.historyStorage;
            console.log('deals:', JSON.stringify(historyStorage.deals.slice(-5)));
            console.log('history orders:', JSON.stringify(historyStorage.historyOrders.slice(-5)));
            
            // const metaStats = new MetaStats(token);
        
            // let accountId = '49d1bb26-d3ff-4f00-94ed-687f2ad1687a'; // MetaApi account id
        
            // const s = await metaStats.getMetrics(accountId)
            // console.log(s)
        }
    },
    mounted() {
        this.api = new MetaApi(this.token);

        this.connect()
    },
    template : `
    asd
    `,
}

export { TradingViewer } 