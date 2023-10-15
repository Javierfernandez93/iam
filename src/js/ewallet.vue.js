import { User } from '../../src/js/user.module.js?v=2.6.4'   

import { EwalletatmViewer } from './ewalletatmViewer.vue.js?v=2.6.4'
import { EwalletViewer } from './ewalletViewer.vue.js?v=2.6.4'
import { EwalletqrViewer } from './ewalletqrViewer.vue.js?v=2.6.4'
import { EwalletwithdrawViewer } from './ewalletwithdrawViewer.vue.js?v=2.6.4'
import { EwalletaddfundsViewer } from './ewalletaddfundsViewer.vue.js?v=2.6.4'

Vue.createApp({
    components : { 
        EwalletViewer, EwalletatmViewer, EwalletqrViewer, EwalletwithdrawViewer, EwalletaddfundsViewer
    },
    data() {
        return {
            User: new User,
            ewallet: {
                public_key: null,
                amount: 0,
                link: null,
                amountUSD: 0,
                recipientAdress: '',
                fee: 0,
                amountToSend: 0,
                addressLenght: 66,
            }
        }
    },
    watch : {
        
    },
    methods: {
        openAtm: function() {
            this.$refs.ewalletAtm.openAtm()
        },
        openWithdraw: function() {
            this.$refs.ewalletWithdraw.openWithdraw()
        },
        openAddFunds: function() {
            console.log(1)        
            this.$refs.ewalletAddFunds.openAddFunds()
        },
        getEwalletQr: function() {       
            this.$refs.ewalletqr.getEwalletQr()
        },
        getEwallet: function() {            
            this.User.getEwallet({},(response)=>{
            
                if(response.s == 1)
                {
                    this.ewallet = {...this.ewallet,...response.ewallet}

                    this.$refs.ewallet.getLastTransactionsWallet()
                }
            })
        },
    },
    mounted() 
    {   
        this.getEwallet()
    },
}).mount('#app')