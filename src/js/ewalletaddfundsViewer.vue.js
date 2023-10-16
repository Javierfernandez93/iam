import { User } from '../../src/js/user.module.js?v=2.6.5'   

const EwalletaddfundsViewer = {
    name : 'ewalletaddfunds-viewer',
    emits: ['getewallet'],
    props: ['ewallet'],
    data() {
        return {
            User: new User,
            catalogPaymentMethods : null,
            fund: {
                checkout_data: null,
                amount: null,
                catalog_currency_id: null,
                catalog_payment_method_id: null
            },
            error: null,
            ERRORS: {
                NOT_PAYMENT_METHOD_SELECTED : {
                    code: 1,
                    text: 'No has seleccionado el método de fondeo'
                },
                NOT_AMMOUT : {
                    code: 2,
                    text: 'No has ingresado una cantidad a fondear'
                },
            }
        }
    },
    watch : {
        fund: {
            handler() {
                this.error = null

                if(this.fund.amount > 0)
                {
                    if(this.fund.catalog_payment_method_id)
                    {
                    
                    } else {
                        this.error = this.ERRORS.NOT_PAYMENT_METHOD_SELECTED
                    }
                } else {
                    this.error = this.ERRORS.NOT_AMMOUT
                }
            },
            deep: true
        }
    },
    methods: {
        openAddFunds: function() {     
            this.openOffCanvas()
        },
        openOffCanvas: function() {     
            $(this.$refs.offcanvasRight).offcanvas('show')
        },
        selectCatalogPaymentMethodId: function (catalog_payment_method_id) {
            this.fund.catalog_payment_method_id = catalog_payment_method_id
        },
        getPaymentMethods: function () {
            this.User.getPaymentMethods({filter_wallet:true}, (response) => {
                if (response.s == 1) {
                    this.catalogPaymentMethods = response.catalogPaymentMethods
                }
            })
        },
        addFunds: function () {
            this.User.addFunds(this.fund, (response) => {
                console.log(response)
                if (response.s == 1) {
                    this.fund.checkout_data = response.checkout_data
                }
            })
        },
    },
    updated() {
    },
    mounted() 
    {       
        this.error = this.ERRORS.NOT_AMMOUT

        this.getPaymentMethods()
    },
    template : `
        <div class="offcanvas offcanvas-end" tabindex="-1" ref="offcanvasRight" id="offcanvasRight" aria-labelledby="offcanvasWithBackdropLabel">
            <div>
                <div class="offcanvas-header">
                    <h5 id="offcanvasRightLabel">
                        <div>
                            <t>Añadir fondos USD</t>
                        </div>
                    </h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div v-if="ewallet" class="offcanvas-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="badge bg-gradient-warning fs-5 shadow-lg">
                                        <i class="bi bi-currency-exchange text-white"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div>
                                        <span class="badge text-secondary p-0">Balance</span>
                                    </div>
                                    <div class="fs-5 fw-semibold text-dark">
                                        $ {{ewallet.amount.numberFormat(2)}} USD
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-body">
                            <div v-if="!fund.checkout_data">
                                <div class="form-floating mb-3">
                                    <input 
                                        v-model="fund.amount"
                                        :class="error != ERRORS.NOT_ENOUGH_FUNDS && fund.amount > 0 ? 'is-valid' : ''"
                                        type="number" class="form-control" id="address" placeholder="Cantidad a fondear">
                                    <label for="address">Cantidad a fondear</label>
                                </div>

                                <ul v-if="catalogPaymentMethods" class="list-group mb-3">
                                    <li v-for="catalogPaymentMethod in catalogPaymentMethods" class="list-group-item border-light">
                                        <div class="row align-items-center py-3">

                                            <div class="col-4 text-truncate">
                                                <div v-if="fund.catalog_payment_method_id == catalogPaymentMethod.catalog_payment_method_id">
                                                    <i class="bi bi-check-circle fs-5 text-success"></i>
                                                </div>
                                                <div class="fw-semibold text-xs">
                                                    {{catalogPaymentMethod.payment_method}}
                                                </div>
                                            </div>
                                            <div v-if="catalogPaymentMethod.currencies" class="col-8">
                                                <div class="form-floating">
                                                    <select @change="selectCatalogCurrencyId" class="form-select" v-model="fund.catalog_currency_id" aria-label="Selecciona tu moneda">
                                                        <option v-for="currency in catalogPaymentMethod.currencies" v-bind:value="currency.catalog_currency_id">
                                                            {{ currency.currency }} - {{ currency.description }}
                                                        </option>
                                                    </select>
                                                    <label for="floatingSelect">Selecciona tu moneda</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <button 
                                                    @click="selectCatalogPaymentMethodId(catalogPaymentMethod.catalog_payment_method_id)"
                                                    :class="fund.catalog_payment_method_id == catalogPaymentMethod.catalog_payment_method_id ? 'btn-secondary' : 'btn-success'"
                                                    v-text="fund.catalog_payment_method_id == catalogPaymentMethod.catalog_payment_method_id ? 'Elegido' : 'Elegir método de pago'"
                                                    class="btn mb-0 w-100 shadow-none"></button>
                                            </div>
                                        </div>
                                    </li>
                                </ul>

                                <div v-if="error" class="alert alert-danger text-white">
                                    <strong>Aviso</strong> - {{error.text}}
                                </div>
                                <button 
                                    :disabled="error != null"
                                    @click="addFunds"
                                    class="btn btn-primary shadow-none waves-effect waves-light">
                                    Añadir fondos
                                </button>
                            </div>
                            <div v-else>
                                <div class="fw-semibold text-dark text-center mb-3">
                                    <div><i class="bi bi-shield-check fs-4 text-success"></i></div>
                                    Hemos generado tu link de pago
                                </div>

                                <div class="alert alert-success text-white">
                                    <strong>Aviso</strong> También podrás ver éste fondeo en tus ordenes de compra
                                </div>
                                <a
                                    :href="fund.checkout_data.checkout_url"
                                    target="_blank"
                                    class="btn btn-primary shadow-none waves-effect waves-light">
                                    Ir a realizar el pago
                                </a>
                            </div>
                        </div>    
                    </div>    
                </div>
            </div>
        </div>
    `,
}

export { EwalletaddfundsViewer } 