import { User } from '../../src/js/user.module.js?v=2.6.5'   

/* vue */
import { StorecreditViewer } from '../../src/js/storecreditViewer.vue.js?v=2.6.5'
import { StorepaymentmethodsViewer } from '../../src/js/storepaymentmethodsViewer.vue.js?v=2.6.5'
import { StorecheckoutViewer } from '../../src/js/storecheckoutViewer.vue.js?v=2.6.5'
import { StoreinvoiceViewer } from '../../src/js/storeinvoiceViewer.vue.js?v=2.6.5'

Vue.createApp({
    components: {
        StorecreditViewer, StorepaymentmethodsViewer, StorecheckoutViewer, StoreinvoiceViewer
    },
    data() {
        return {
            User : new User,  
            STATES: {
                CHOICE_ITEMS: {
                    code: 1,
                    text: "Agrega tus créditos",
                },
                CHOICE_PAYMENT_METHOD: {
                    code: 2,
                    text: "Elegir método de pago",
                },
                CHECKOUT: {
                    code: 3,
                    text: "Resumen de compra",
                },
                INVOICE: {
                    code: 4,
                    text: "Ticket de pago",
                }
            },      
            cart: {
                hasItems : false,
                id: null,
                catalog_currency_id: null,
                catalog_payment_method_id: null,
                package_id: null,
                amount: null,
                state: null,
                package_type: null,
                buy_per_user : {
                    buy_per_user_id: null
                }
            }
        }
    },
    methods: {
        nextStep() {
            if(this.cart.state.code < Object.keys(this.STATES).length)
            {
                this.proccessBeforeContinue(this.cart.state).then(()=>{
                    this.cart.state = this.getStateByCode(this.cart.state.code+1)
                })
            }
        },
        proccessBeforeContinue(state) {
            return new Promise((resolve) => {
                if(state == this.STATES.CHECKOUT)
                {
                    this.saveBuy().then(()=>{
                        resolve()
                    })
                } else {
                    resolve()
                }
            })
        },
        getStateByCode(code) {
            const states = Object.keys(this.STATES);
            let state = null
            
            for(let i=0; i< states.length; i++)
            {
                if(this.STATES[states[i]].code == code)
                {
                    state = this.STATES[states[i]]
                }
            }

            return state
        },
        saveBuy() {
            return new Promise((resolve) => {
                this.User.saveBuy(this.cart, (response) => {
                    if (response.s == 1) {
                        this.cart.buy_per_user.buy_per_user_id = response.buy_per_user_id
                    }

                    resolve()
                })
            })
        },
        initCart() {
            return new Promise((resolve,reject) => {
                this.User.initCart({}, (response) => {
                    if (response.s == 1) {
                        this.cart.id = response.instance_id

                        resolve()
                    }

                    reject()
                })
            })
        },
    },
    mounted() {
        const package_type = getLastUrlPart()
        
        if(['package','credit'].includes(package_type))
        {
            this.cart.package_type = package_type
            this.cart.state = this.STATES.CHOICE_ITEMS
            // this.cart.state = this.STATES.INVOICE

            this.initCart().then(() =>{
                
            }).catch(() => {
                alert("Hubo un error")
            })
        }
    },
}).mount('#app')