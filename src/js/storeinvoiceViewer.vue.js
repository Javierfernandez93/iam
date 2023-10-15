import { User } from '../../src/js/user.module.js?v=2.6.4'   

const StoreinvoiceViewer = {
    name : 'storeinvoice-viewer',
    props : ['cart'],
    data() {
        return {
            User: new User,
            CATALOG_PAYMENT_METHOD: {
                COINPAYMENTS: 1,
                STRIPE: 2,
                EWALLET: 3,
                PAYPAL: 4,
                PAYMENT_GATEWAY: 7
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
        getInvoice() {
            this.User.getInvoice({buy_per_user_id:this.cart.buy_per_user.buy_per_user_id}, (response) => {
                if (response.s == 1) {
                    this.cart.buy_per_user = response.buy_per_user

                    if(this.cart.buy_per_user.catalog_payment_method_id == this.CATALOG_PAYMENT_METHOD.PAYMENT_GATEWAY)
                    {
                        window.location.href = `../../apps/paymentGateway/process?invoiceId=${this.cart.buy_per_user.checkout_data.invoice_id}`
                    }
                }
            })
        },
    },
    mounted() {
        this.getInvoice()
    },
    template : `
        <div v-if="cart.buy_per_user" class="row justify-content-center">
            <div class="col-12 col-xl-5">
                <div class="card overflow-hidden">
                    <div v-if="cart.buy_per_user.catalog_payment_method">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    <div>
                                        <span class="badge text-secondary p-0">Método de pago </span>
                                    </div>
                                    <div class="fs-5 text-primary text-gradient fw-sembold">{{cart.buy_per_user.catalog_payment_method.payment_method}}</div>
                                </div>
                                <div class="col-auto">
                                    <div><span class="badge text-secondary p-0">Número de pago</span></div>
                                    <div class="fs-5">{{cart.buy_per_user.invoice_id}}</div>
                                </div>
                            </div>
                        </div>
                        <div v-if="cart.buy_per_user.catalog_payment_method_id == CATALOG_PAYMENT_METHOD.COINPAYMENTS">
                            <div v-if="cart.buy_per_user.checkout_data" class="card-body text-center">
                                <div><span class="badge text-secondary p-0">Total a Pagar</span></div>
                                <div class="fs-4 text-sembold text-dark">{{cart.buy_per_user.checkout_data.amount}}</div>
                                
                                <div><span class="badge text-secondary p-0">Dirección de pago</span></div>
                                <div class="fs-7">{{cart.buy_per_user.checkout_data.address}}</div>

                                <div><span class="badge text-secondary p-0">Confirmaciones</span></div>
                                <div class="fs-7">{{cart.buy_per_user.checkout_data.confirms_needed}}</div>

                                <div><span class="badge text-secondary p-0">Tiempo restante </span></div>
                                <div class="fs-7">{{cart.buy_per_user.checkout_data.timeout.getSeconds()}}</div>
                            </div>
                            <div v-if="cart.buy_per_user.checkout_data" class="row bg-dark justify-content-center py-3">
                                <div class="col-12 col-xl-6">
                                    <div v-if="cart.buy_per_user.checkout_data">
                                        <img :src="cart.buy_per_user.checkout_data.qrcode_url" class="w-100">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body mb-0 pb-0">
                                <div class="alert alert-light">
                                    <strong>Aviso</strong>
                                    Al dar clic en <strong>"IR A PAGAR"</strong> serás redirijido a otra páginas según el medio de pago. Si es la <strong>primera vez que realizas un pago como éste</strong>, es importante que <strong>leas las intrucciones</strong> antes de realizar el pago.
                                </div>
                            </div>
                        </div>
                        <div v-else-if="cart.buy_per_user.catalog_payment_method_id == CATALOG_PAYMENT_METHOD.EWALLET">
                            <div v-if="cart.buy_per_user.checkout_data" class="card-body text-center">
                                <div><span class="badge text-secondary p-0">Total a Pagar</span></div>
                                <div class="fs-4">$ {{cart.buy_per_user.checkout_data.amount.numberFormat(2)}}</div>
                                
                            </div>
                            <div class="card-body mb-0 pb-0">
                                <div class="alert alert-light">
                                    <strong>Aviso</strong>
                                    Al dar clic en <strong>"IR A PAGAR"</strong> serás redirijido a otra páginas según el medio de pago. Si es la <strong>primera vez que realizas un pago como éste</strong>, es importante que <strong>leas las intrucciones</strong> antes de realizar el pago.
                                </div>
                            </div>
                        </div>
                        <div v-else-if="cart.buy_per_user.catalog_payment_method_id == CATALOG_PAYMENT_METHOD.PAYPAL">
                            <div v-if="cart.buy_per_user.checkout_data" class="card-body text-center">
                                <div><span class="badge text-secondary p-0">Total a Pagar</span></div>
                                <div class="fs-4 fw-sembold text-dark">$ {{(cart.buy_per_user.checkout_data.amount+cart.buy_per_user.checkout_data.fee).numberFormat(2)}} USD </div>
                            </div>
                        </div>
                        <div v-else-if="cart.buy_per_user.catalog_payment_method_id == CATALOG_PAYMENT_METHOD.PAYMENT_GATEWAY">
                            <div v-if="cart.buy_per_user.checkout_data" class="card-body text-center">
                                <div><span class="badge text-secondary p-0">Total a Pagar</span></div>
                                <div class="fs-4 fw-sembold text-dark">$ {{(cart.buy_per_user.checkout_data.amount+cart.buy_per_user.checkout_data.fee).numberFormat(2)}} USD </div>

                                <div>Espera mientras redirigimos a la pantalla de pago</div>
                                <div class="spinner-grow" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <div v-if="cart.buy_per_user.checkout_data" class="card-footer">
                            <a :href="cart.buy_per_user.checkout_data.checkout_url" target="_blank" class="btn btn-success bg-gradient-success btn-lg fs-4 shadow-none w-100 mb-0">Ir a pagar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { StoreinvoiceViewer } 