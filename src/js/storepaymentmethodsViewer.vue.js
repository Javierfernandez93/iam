import { User } from '../../src/js/user.module.js?v=2.6.6'   

const StorepaymentmethodsViewer = {
    name : 'storepaymentmethods-viewer',
    props : ['cart'],
    emits : ['nextstep'],
    data() {
        return {
            User: new User,
            catalogPaymentMethods: null
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
        selectCatalogCurrencyId() {
            this.User.selectCatalogCurrencyId({catalog_currency_id:this.cart.catalog_currency_id}, (response) => {
                if (response.s == 1) {
                }
            })
        },
        getPaymentMethods() {
            return new Promise((resolve) => {
                this.User.getPaymentMethods({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.catalogPaymentMethods)
                    }
                })
            })
        },
        unselectCatalogPayments() {
            this.catalogPaymentMethods.map((catalogPaymentMethod) => catalogPaymentMethod.selected = false)
        },
        selectCatalogPaymentMethodId(catalogPaymentMethod) {
            this.unselectCatalogPayments()
            this.User.selectCatalogPaymentMethodId({catalog_payment_method_id:catalogPaymentMethod.catalog_payment_method_id}, (response) => {
                if (response.s == 1) {
                    catalogPaymentMethod.selected = true
                    this.cart.catalog_payment_method_id = response.catalog_payment_method_id

                    if(!catalogPaymentMethod.currencies)
                    {
                        setTimeout(()=>{
                            this.$emit('nextstep')
                        },500)
                    } else {
                        this.cart.catalog_currency_id = catalogPaymentMethod.currencies[1].catalog_currency_id
                        this.selectCatalogCurrencyId()
                    }
                }
            })
        },
    },
    mounted() {
        this.getPaymentMethods().then(catalogPaymentMethods => this.catalogPaymentMethods = catalogPaymentMethods)
    },
    template : `
        <div v-if="catalogPaymentMethods" class="row justify-content-center">
            <div class="col-12 col-xl-8">
                <ul class="list-group">
                    <li v-for="catalogPaymentMethod in catalogPaymentMethods" class="list-group-item list-group-item-action f-zoom-element-sm">
                        <div class="row align-items-center cursor-pointer py-3">
                            <div class="col-auto">
                                <div class="avatar avatar">
                                    <span class="avatar avatar bg-dark position-relative bg-dark">
                                        {{catalogPaymentMethod.payment_method.getFirstLetter()}}

                                        <span v-if="cart.catalog_payment_method_id == catalogPaymentMethod.catalog_payment_method_id" class="position-absolute top-0 start-100 translate-middle p-2 bg-success border border-light rounded-circle">
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <div class="col">
                                <div>
                                    <span v-if="catalogPaymentMethod.fee" class="badge bg-gradient-warning text-xxs">fee del {{catalogPaymentMethod.fee.numberFormat(0)}}% </span>
                                    <span v-else class="badge text-success border border-success text-xxs">0% Comisión</span>

                                    <div class="fs-5 fw-semibold text-dark"
                                        :class="cart.catalog_payment_method_id == catalogPaymentMethod.catalog_payment_method_id ? 'fw-sembold text-dark' : ''">{{catalogPaymentMethod.payment_method}}</div>
                                </div>
                            </div>
                            <div v-if="catalogPaymentMethod.currencies && catalogPaymentMethod.selected" class="col">
                                <div class="form-floating">
                                    <select @change="selectCatalogCurrencyId" class="form-select" v-model="cart.catalog_currency_id" aria-label="Selecciona tu moneda">
                                        <option v-for="currency in catalogPaymentMethod.currencies" v-bind:value="currency.catalog_currency_id">
                                            {{ currency.currency }} - {{ currency.description }}
                                        </option>
                                    </select>
                                    <label for="floatingSelect">Selecciona tu moneda</label>
                                </div>
                            </div>
                            <div class="col-auto">
                                <button 
                                    @click="selectCatalogPaymentMethodId(catalogPaymentMethod)"
                                    :class="cart.catalog_payment_method_id == catalogPaymentMethod.catalog_payment_method_id ? 'btn-secondary' : 'btn-warning'"
                                    v-text="cart.catalog_payment_method_id == catalogPaymentMethod.catalog_payment_method_id ? 'Elegido' : 'Elegir método'"
                                    class="btn shadow-none mb-0"></button>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    `,
}

export { StorepaymentmethodsViewer } 