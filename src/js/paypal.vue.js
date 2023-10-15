import { User } from '../../src/js/user.module.js?t=5'

Vue.createApp({
    data() {
        return {
            loading : null,
            User: new User,
            paymentStatus: null,
            STATUS: {
                PAYMENT_DONE : 1,
                PAYMENT_EXPIRED : 2
            },
        }
    },
    methods: {
        getPayPalPaymentStatus(paymentId,PayerID) {
            this.loading = true
            this.User.getPayPalPaymentStatus({paymentId:paymentId,PayerID:PayerID}, (response) => {
                this.loading = false
                if (response.s == 1) {
                    this.paymentStatus = this.STATUS.PAYMENT_DONE
                } else {
                    this.paymentStatus = this.STATUS.PAYMENT_EXPIRED
                }
            })
        },
    },
    mounted() {
        if(getParam('paymentId') && getParam('PayerID'))
        {
            this.getPayPalPaymentStatus(getParam('paymentId'),getParam('PayerID'))
        }
    },
}).mount('#app')