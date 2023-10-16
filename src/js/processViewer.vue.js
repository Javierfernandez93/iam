import { User } from '../../src/js/user.module.js?v=2.6.5'   

const ProcessViewer = {
    name : 'process-viewer',
    data() {
        return {
            User: new User,
            ewallet: null,
            loading: false,
            invoice: {
                invoice_id: null,
                amount: null,
                status: false
            },
            STATUS: {
                DELETED: -1,
                EXPIRED: 0,
                PENDING: 1,
                VALIDATED: 2,
            }
        }
    },
    methods: {
        getEwallet() {            
            return new Promise((resolve)=>{
                this.User.getEwallet({},(response)=>{
                
                    if(response.s == 1)
                    {
                        this.ewallet = response.ewallet
                    }
                    resolve()
                })
            })
        },
        payInvoiceFromWallet() {  
            this.loading = true     

            this.User.payInvoiceFromWallet({invoice_id:this.invoice.invoice_id},(response)=>{

                this.loading = false          

                if(response.s == 1)
                {
                    this.invoice.status = response.response.status
                }
            })
        },
        getInvoiceById() {
            this.User.getInvoiceById({invoice_id:this.invoice.invoice_id}, (response) => {
                if (response.s == 1) {
                    this.invoice = response.invoice
                }
            })
        },
    },
    mounted() 
    {       
        if(getParam('txn_id'))
        {
            this.invoice.invoice_id = getParam('txn_id')

            this.getEwallet().then(()=>{
                this.getInvoiceById()
            })
        }
    },
    template : `
        <div v-if="ewallet" class="row justify-content-center">
            <div class="col-xl-4 mb-xl-0 mb-4">
            
                <div v-if="invoice.status == STATUS.PENDING"
                    class="card shadow-xl border-radius-2xl">
                    <div class="card-header text-center">
                        Pagar con Ewallet
                    </div>
                    <div class="card-body">
                        <div class="mb-3 text-center">
                            <span class="badge text-primary">Número de factura</span>
                            <div class="fs-6">#{{invoice.invoice_id}}</div>
                        </div>
                        <div class="mb-3 text-center">
                            <span class="badge text-primary">Pago requerido</span>
                            <div class="fs-4 text-dark fw-semibold">$ {{invoice.amount.numberFormat(2)}}</div>
                        </div>
                        <div class="mb-3 text-center">
                            <span class="badge text-primary">Balance disponible</span>
                            <div class="fs-4 text-dark fw-semibold">$ {{ewallet.amount.numberFormat(2)}}</div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div v-if="ewallet.amount >= invoice.amount">
                            <button 
                                :disabled="loading"
                                @click="payInvoiceFromWallet"
                                class="btn btn-primary w-100 btn-lg">
                                    <span v-if="loading" class="spinner-border spinner-border-sm shadow-none" role="status" aria-hidden="true"></span>
                                    <span v-else>
                                        Pagar factura
                                    </span>
                                </button>
                        </div>
                        <div v-else class="alert alert-danger fw-semibold text-white">
                            <strong>Aviso</strong>
                            No tienes fondos suficientes para pagar ésta factura
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

export { ProcessViewer } 