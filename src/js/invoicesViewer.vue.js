import { User } from '../../src/js/user.module.js?v=2.6.6'   

const InvoicesViewer = {
    name : 'invoices-viewer',
    data() {
        return {
            User: new User,
            buy_per_user_id: null,
            query: null,
            invoices: null,
            invoicesAux: null,
            STATUS : {
                DELETED: -1,
                CANCELED: 0,
                PENDING: 1,
                PAYED: 2,
                REFUND: 3,
            }
        }
    },
    watch : {
        query : {
            handler() {
                this.filterData()
            },
            deep: true
        }
    },
    methods: {
        filterData() {
            this.invoices = this.invoicesAux

            this.invoices = this.invoices.filter((invoice) => {
                return invoice.amount.toString().includes(this.query) || invoice.invoice_id.toLowerCase().includes(this.query.toLowerCase())
            })
        },
        getInvoices() {
            return new Promise((resolve, reject) => {
                this.User.getInvoices({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.invoices)
                    }

                    reject()
                })
            })
        },
    },
    mounted() {
        if(getParam("bpid"))
        {
            this.buy_per_user_id = getParam("bpid")
        }

        this.getInvoices().then((invoices) => {
            this.invoices = invoices
            this.invoicesAux = invoices
        }).catch((err) => { this.invoices = false })
    },
    template : `
        <div v-if="invoices">
            <div class="card mb-3">
                <div class="card-header bg-transparent">
                    <div class="row align-items-center">
                        <div class="col-12 col-xl mb-3 mb-xl-0">
                            <div class="col-auto"><span class="badge text-dark">Total {{invoices.length}}</span></div>
                            <div class="col fw-semibold text-dark h3">Mis compras</div>
                        </div>
                        <div class="col-12 col-xl-6">
                            <input type="search" class="form-control" v-model="query" placeholder="buscar por monto o items"/>
                        </div>
                    </div>
                </div>
            </div>
            <div v-for="invoice in invoices" class="card mb-3" :class="invoice.buy_per_user_id == buy_per_user_id ? 'border-primary border-5' : ''">
                <div class="card-header">
                    <div class="row">
                        <div class="col-12 col-xl">
                            <div class="text-xs text-secondary">
                                Orden
                            </div>
                            <span class="lead fw-semibold text-dark">#{{invoice.invoice_id}}</span>
                        </div>
                        <div class="col-12 col-xl-auto text-end">
                            <div class="text-xs text-secondary">
                                Monto
                            </div>
                            <span class="lead fw-semibold text-dark">$ {{invoice.amount.numberFormat(2)}} MXN</span>
                        </div>
                        <div class="col-12 col-xl-auto text-end">
                            <div class="text-xs text-secondary">
                                Método pago
                            </div>
                            <span class="lead fw-semibold text-dark">{{invoice.catalog_payment_method.payment_method}}</span>
                        </div>
                        <div class="col-12 col-xl-auto text-end">
                            <div class="text-xs text-secondary">
                                Pedido realizado
                            </div>
                            <span class="lead fw-semibold text-dark">{{invoice.create_date.formatFullDate()}}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-xl">
                            <span v-for="(item,index) in invoice.items" class="lead fw-sembold text-primary">
                                {{item.title}} {{index < invoice.items.length -1 ? ',' : ''}}
                            </span>
                        </div>
                        <div class="col-12 col-xl-auto">
                            <span v-if="invoice.status == STATUS.DELETED"
                                class="badge border border-danger text-danger">
                                <i class="bi bi-dash-circle-fill"></i>
                                Eliminada
                            </span>
                            <span v-else-if="invoice.status == STATUS.CANCELED"
                                class="badge border border-warning text-warning">
                                <i class="bi bi-dash-circle"></i>
                                Cancelada
                            </span>
                            <span v-else-if="invoice.status == STATUS.PENDING"
                                class="badge border border-secondary text-secondary">
                                <i class="bi bi-clock"></i>
                                Pendiente por verificar pago
                            </span>
                            <span v-else-if="invoice.status == STATUS.PAYED"
                                class="badge border border-success text-success">
                                <i class="bi bi-check-circle"></i> 
                                Pagada
                            </span>
                            <span v-else-if="invoice.status == STATUS.REFUND"
                                class="badge border border-primary text-primary">
                                <i class="bi bi-arrow-clockwise"></i>
                                Reembolsada
                            </span>
                        </div>
                        <div v-if="invoice.status == STATUS.PAYED" class="col-12 col-xl-auto">
                            <span v-if="invoice.send" class="badge border border-success text-success">
                                <i class="bi bi-truck"></i>
                                Paquete Enviado
                            </span>
                            <span v-else class="badge border border-warning text-warning">
                                <i class="bi bi-arrow-clockwise"></i>
                                Preparando paquete
                            </span>
                        </div>
                        <div class="col-12 col-xl-auto">
                            <div v-if="invoice.checkout_data.checkout_url">
                                <a v-if="invoice.status == STATUS.PENDING" :href="invoice.checkout_data.checkout_url" :disabled="invoice.status != STATUS.PENDING" target="_blank" class="btn btn-sm shadow-none m-0 btn-success">
                                    Generar ficha de pago
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else-if="invoices == false" class="alert alert-light text-center">
            <div><strong>Importante</strong></div>
            Aquí te mostraremos las compras que realices de tu paquete.

            <div class="d-flex justify-content-center py-3">
                <a href="../../apps/store/package" class="btn btn-primary me-2 mb-0 shadow-none">Configurar tu paquete</a>
            </div>
        </div>
    `,
}

export { InvoicesViewer } 