import { User } from '../../src/js/user.module.js?v=2.6.4'   

const AirtmViewer = {
    name : 'airtm-viewer',
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
    },
    mounted() 
    {       
        if(getParam('txn_id'))
        {

            this.getInvoiceById(getParam('txn_id')).then((invoice)=>{
                this.invoice = invoice

                console.log(invoice)
            }).catch(() => this.invoice = false)
        }
    },
    template : `
        <div v-if="invoice" class="row justify-content-center">
            <div class="col-xl-4 mb-xl-0 mb-4">
            
                <div v-if="invoice.status == STATUS.PENDING"
                    class="card shadow-xl border-radius-2xl">
                    <div class="card-header text-center">
                        Pagar con Airtm
                    </div>
                    <div class="card-body">
                        <div class="mb-3 text-center">
                            <span class="badge text-primary">Número de factura</span>
                            <div class="fs-6">#{{invoice.invoice_id}} <button @click="copy(invoice.invoice_id,$event.target)" class="btn btn-outline-primary px-3 btn-sm mb-0 shadow-none">Copiar</button></div>
                        </div>
                        <div class="mb-3 text-center">
                            <span class="badge text-primary">Pago requerido</span>
                            <div class="fs-4 text-dark fw-semibold">$ {{invoice.amount.numberFormat(2)}} AIRUSD</div>
                        </div>
                        <div class="mb-3 text-center">
                            <span class="badge text-primary">Usuario Airtm</span>
                            <div class="fs-4 text-dark fw-semibold">{{invoice.checkout_data.email}} <button @click="copy(invoice.checkout_data.email,$event.target)" class="btn btn-outline-primary px-3 btn-sm mb-0 shadow-none">Copiar</button></div>
                        </div>

                        <div class="alert alert-light text-center fw-semibold">
                            <strong>¿Quieres saber como realizar el pago?</strong> ve el tutorial de como realizar el pago <a class="text-decoration-underline text-primary" href="https://www.iam.com.mx/apps/ticket/?fid=10" target="_blank">aquí</a>
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

export { AirtmViewer } 