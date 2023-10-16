import { User } from '../../src/js/user.module.js?v=2.6.6'   

const PaymentgatewayViewer = {
    name : 'paymentgateway-viewer',
    data() {
        return {
            User: new User,
            ewallet: null,
            loading: false,
            invoice: null,
            STATUS: {
                DELETED: -1,
                EXPIRED: 0,
                PENDING: 1,
                VALIDATED: 2,
            }
        }
    },
    methods: {
        copy(text,target) {
            navigator.clipboard.writeText(text).then(() => {
               target.innerText = 'Copiado'
            })
        },
        runInterval() {
            this.interval = setInterval(()=>{
                this.invoice.timeLeft = this.invoice.checkout_data.expiration_date.getTimeLeft()
            },1000)
        },
        getInvoiceById(invoice_id) {
            return new Promise((resolve,reject)=> {
                this.User.getInvoiceById({invoice_id:invoice_id}, (response) => {
                    if (response.s == 1) {
                        resolve(response.invoice)
                    }

                    reject()
                })

            })
        },
        getPaymentGateWay(buy_per_user_id) {
            return new Promise((resolve,reject)=> {
                this.User.getPaymentGateWay({buy_per_user_id:buy_per_user_id}, (response) => {
                    if (response.s == 1) {
                        resolve(response.wallet)
                    }

                    reject()
                })

            })
        },
    },
    mounted() 
    {       
        if(getParam('invoiceId'))
        {
            this.getInvoiceById(getParam('invoiceId')).then((invoice)=>{
                this.invoice = invoice
                this.runInterval()
            }).catch(() => this.invoice = false)
        }
    },
    template : `
        <div v-if="invoice" class="row justify-content-center">
            <div class="col-xl-5 mb-xl-0 mb-4">
            
                <div v-if="invoice.status == STATUS.PENDING"
                    class="card shadow-xl blur shadow-blur border-radius-2xl">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-12 col-xl">
                                Pagar con 
                                <div class="h3 fw-semibold">USDT.TRC20</div>
                            </div>
                            <div class="col-12 col-xl-auto text-end">
                                <span class="badge px-0 text-primary">Número de factura</span>
                                <div class="fs-6">#{{invoice.invoice_id}}</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 mx-3">
                            <span class="badge text-primary px-0">Monto a pagar</span>
                            <div class="row border border-primary rounded p-3 align-items-center">
                                <div class="col-12 col-xl">
                                    <div class="h4 text-dark fw-semibold">$ {{invoice.amount.numberFormat(2)}}</div>
                                </div>
                                <div class="col-12 col-xl-auto">
                                    USDT (TRC20)
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 mx-3">
                            <div class="row align-items-center">
                                <div class="col-12 col-xl">
                                    <span class="badge text-start text-primary px-0">Dirección USDT.TRC20</span>
                                    <div class="text-xs text-dark fw-semibold">
                                        {{invoice.checkout_data.address}} 
                                    </div>
                                </div>
                                <div class="col-12 col-xl-auto">
                                    <button @click="copy(invoice.checkout_data.address,$event.target)" class="btn btn-primary px-2 btn-sm mb-0 shadow-none">Copiar</button>
                                </div>
                            </div>
                        </div>
                        
                        <div v-if="invoice.timeLeft" class="text-center">
                            <div><span class="badge text-secondary p-0">Tiempo restante </span></div>
                            <div class="fs-7">{{invoice.timeLeft}}</div>
                        </div>
                    </div>

                    <div class="p-5">
                        <img :src="invoice.checkout_data.checkout_url.getQrCode()" class="w-100" alt="address" title="address"/>
                    </div>
                      
                    <div class="card-body">
                        <div class="alert alert-light fw-semibold">
                            <strong>Asegúrate</strong> de enviar solo USDT (TRC20) a esta dirección de deposito.</a>
                        </div>
                        
                        
                        <div class="d-grid">
                            <a class="text-decoration-underline text-primary" class="btn btn-primary text-white btn-lg mb-0 shadow-none" :href="invoice.checkout_data.checkout_url" target="_blank">Realiza tu pago aquí</a>
                        </div>
                    </div>
                </div>
                <div v-if="invoice.status == STATUS.VALIDATED"
                    class="card bg-gradient-success shadow-xl border-radius-2xl">
                    <div class="card-body text-center text-white">
                        <div class="mb-3">
                            <span class="badge text-white">Número de factura</span>
                            <div class="fs-6 fw-semibold">#{{invoice.invoice_id}}</div>
                        </div>
                        <div class="fs-1 text-white"><i class="bi bi-ui-checks"></i></div>
                        <div class="fs-4 fw-semibold">Factura pagada</div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { PaymentgatewayViewer } 