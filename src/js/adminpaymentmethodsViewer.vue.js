import { UserSupport } from '../../src/js/userSupport.module.js?t=5.1.4'

const AdminpaymentmethodsViewer = {
    name: 'adminpaymentmethods-viewer',
    data() {
        return {
            UserSupport: new UserSupport,
            catalogPaymentMethods: {},
            catalogPaymentMethodsAux: {},
            query: null,
            percentaje: 0,
            total: 0,
            total_profit_today: 0,
            total_profit_sponsor_today: 0,
            columns: { // 0 DESC , 1 ASC 
                catalog_payment_method_id: {
                    name: 'catalog_payment_method_id',
                    desc: false,
                },
                currency: {
                    name: 'currency',
                    desc: false,
                    alphabetically: true,
                },
                fee: {
                    name: 'fee',
                    desc: false,
                },
                recomend: {
                    name: 'recomend',
                    desc: false,
                },
                create_date: {
                    name: 'create_date',
                    desc: false,
                },
                status: {
                    name: 'status',
                    desc: false,
                },
            }
        }
    },
    watch: {
        query:
        {
            handler() {
                this.filterData()
            },
            deep: true
        }
    },
    methods: {
        sortData: function (column) {
            this.catalogPaymentMethods.sort((a, b) => {
                const _a = column.desc ? a : b
                const _b = column.desc ? b : a

                if (column.alphabetically) {
                    return _a[column.name].localeCompare(_b[column.name])
                } else {
                    return _a[column.name] - _b[column.name]
                }
            });

            column.desc = !column.desc
        },
        filterData: function () {
            this.catalogPaymentMethods = this.catalogPaymentMethodsAux

            this.catalogPaymentMethods = this.catalogPaymentMethods.filter((catalogPaymentMethod) => {
                return catalogPaymentMethod.code.toLowerCase().includes(this.query.toLowerCase()) || catalogPaymentMethod.currency.toLowerCase().includes(this.query.toLowerCase()) || catalogPaymentMethod.fee.toString().includes(this.query.toLowerCase()) || catalogPaymentMethod.description.includes(this.query.toLowerCase())
            })
        },
        toggleEditingFee: function (catalogPaymentMethod) {
            catalogPaymentMethod.editingFee = !catalogPaymentMethod.editingFee
        },
        savePaymentMethodFee: function (catalogPaymentMethod) {
            this.UserSupport.savePaymentMethodFee({catalog_payment_method_id: catalogPaymentMethod.catalog_payment_method_id, fee : catalogPaymentMethod.fee},(response)=>{
                if(response.s == 1)
                {
                    this.toggleEditingFee(catalogPaymentMethod)
                }
            })
        },
        inactivePaymentMethod: function (catalogPaymentMethod) {
            this.UserSupport.inactivePaymentMethod({catalog_payment_method_id: catalogPaymentMethod.catalog_payment_method_id},(response)=>{
                if(response.s == 1)
                {
                    catalogPaymentMethod.status = response.status
                }
            })
        },
        activePaymentMethod: function (catalogPaymentMethod) {
            this.UserSupport.activePaymentMethod({catalog_payment_method_id: catalogPaymentMethod.catalog_payment_method_id},(response)=>{
                if(response.s == 1)
                {
                    catalogPaymentMethod.status = response.status
                }
            })
        },
        enableRecomendation: function (catalogPaymentMethod) {
            this.UserSupport.enableRecomendation({catalog_payment_method_id: catalogPaymentMethod.catalog_payment_method_id},(response)=>{
                if(response.s == 1)
                {
                    catalogPaymentMethod.recomend = 1
                }
            })
        },
        disableRecomendation: function (catalogPaymentMethod) {
            this.UserSupport.disableRecomendation({catalog_payment_method_id: catalogPaymentMethod.catalog_payment_method_id},(response)=>{
                if(response.s == 1)
                {
                    catalogPaymentMethod.recomend = 0
                }
            })
        },
        deletePaymentMethod: function (catalogPaymentMethod) {
            this.UserSupport.deletePaymentMethod({catalog_payment_method_id: catalogPaymentMethod.catalog_payment_method_id},(response)=>{
                if(response.s == 1)
                {
                    this.getAllPaymentMethods()
                }
            })
        },
        getAllPaymentMethods: function () {
            this.UserSupport.getAllPaymentMethods({}, (response) => {
                if (response.s == 1) {
                    this.catalogPaymentMethodsAux = response.catalogPaymentMethods
                    this.catalogPaymentMethods = this.catalogPaymentMethodsAux
                }
            })
        },
    },
    mounted() {
        this.getAllPaymentMethods()
    },
    template: `
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <i class="bi bi-pie-chart-fill"></i>
                            </div>
                            <div class="col fw-semibold text-dark">
                                <div class="small">Métodos de pago</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-header">
                        <input v-model="query" :autofocus="true" type="text" class="form-control" placeholder="Buscar..." />
                    </div>
                    <div
                        v-if="Object.keys(catalogPaymentMethods).length > 0" 
                        class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr class="align-items-center">
                                        <th @click="sortData(columns.catalog_payment_method_id)" class="text-center c-pointer text-uppercase text-secondary font-weight-bolder opacity-7">
                                            <span v-if="columns.catalog_payment_method_id.desc">
                                                <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                            </span>    
                                            <span v-else>    
                                                <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                            </span>    
                                            <u class="text-sm ms-2">ID</u>
                                        </th>
                                        <th 
                                            @click="sortData(columns.currency)"
                                            class="text-center c-pointer text-uppercase text-primary text-secondary font-weight-bolder opacity-7">
                                            <span v-if="columns.currency.desc">
                                                <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                            </span>    
                                            <span v-else>    
                                                <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                            </span>    
                                            <u class="text-sm ms-2">Método de pago</u>
                                        </th>
                                        <th 
                                            @click="sortData(columns.fee)"
                                            class="text-center c-pointer text-uppercase text-primary text-secondary font-weight-bolder opacity-7">
                                            <span v-if="columns.fee.desc">
                                                <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                            </span>    
                                            <span v-else>    
                                                <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                            </span>    
                                            <u class="text-sm ms-2">FEE</u>
                                        </th>
                                        <th 
                                            @click="sortData(columns.create_date)"
                                            class="text-center c-pointer text-uppercase text-primary text-secondary font-weight-bolder opacity-7">
                                            <span v-if="columns.create_date.desc">
                                                <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                            </span>    
                                            <span v-else>    
                                                <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                            </span>    
                                            <u class="text-sm ms-2">Fecha de ingreso</u>
                                        </th>
                                        <th 
                                            @click="sortData(columns.recomend)"
                                            class="text-center c-pointer text-uppercase text-primary text-secondary font-weight-bolder opacity-7">
                                            <span v-if="columns.recomend.desc">
                                                <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                            </span>    
                                            <span v-else>    
                                                <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                            </span>    
                                            <u class="text-sm ms-2">Recomendado</u>
                                        </th>
                                        <th 
                                            @click="sortData(columns.status)"
                                            class="text-center c-pointer text-uppercase text-primary text-secondary font-weight-bolder opacity-7">
                                            <span v-if="columns.status.desc">
                                                <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                            </span>    
                                            <span v-else>    
                                                <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                            </span>    
                                            <u class="text-sm ms-2">Estatus</u>
                                        </th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Información adicional</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Opciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="catalogPaymentMethod in catalogPaymentMethods">
                                        <td class="align-middle text-center text-sm">
                                            <p class="font-weight-bold mb-0">{{catalogPaymentMethod.catalog_payment_method_id}}</p>
                                        </td>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    <img :src="catalogPaymentMethod.image" class="avatar avatar-sm me-3" :alt="catalogPaymentMethod.currency">
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{catalogPaymentMethod.payment_method}}

                                                        <span v-if="catalogPaymentMethod.code != catalogPaymentMethod.currency"
                                                            class="badge bg-secondary">
                                                            {{catalogPaymentMethod.currency}}
                                                        </span>
                                                    </h6>
                                                    <p class="text-xs text-secondary mb-0">{{catalogPaymentMethod.description}}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <div v-if="!catalogPaymentMethod.editingFee"
                                                class="text-primary cursor-pointer"
                                                @click="toggleEditingFee(catalogPaymentMethod)">
                                                <u>{{catalogPaymentMethod.fee.numberFormat(2)}} %</u>
                                            </div>
                                            <div v-else>
                                                <input 
                                                    :class="catalogPaymentMethod.fee != null ? 'is-valid' : ''"
                                                    v-model="catalogPaymentMethod.fee" 
                                                    @keydown.enter.exact.prevent="savePaymentMethodFee(catalogPaymentMethod)"
                                                    class="form-control"
                                                    type="numer" placeholder="%Fee">
                                            </div>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            {{catalogPaymentMethod.create_date.formatDate()}} 
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span v-if="catalogPaymentMethod.recomend"
                                                class="badge bg-gradient-warning">
                                                Recomendado
                                            </span>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span v-if="catalogPaymentMethod.status == -1"
                                                class="badge bg-danger">
                                                Eliminado
                                            </span>
                                            <span v-else-if="catalogPaymentMethod.status == 0"
                                                class="badge bg-secondary">
                                                Inactivo
                                            </span>
                                            <span v-else-if="catalogPaymentMethod.status == 1"
                                                class="badge bg-success">
                                                Activo
                                            </span>
                                        </td>
                                        <td>
                                            <div v-if="catalogPaymentMethod.additional_data">
                                                <div>
                                                    <div class="text-xs text-secondary">Banco</div>
                                                    <div class="text-semibold text-dark">{{catalogPaymentMethod.additional_data.bank}}</div>
                                                </div>
                                                <div>
                                                    <div class="text-xs text-secondary">Cuenta</div>
                                                    <div class="text-semibold text-dark">{{catalogPaymentMethod.additional_data.account}}</div>
                                                </div>
                                                <div>
                                                    <div class="text-xs text-secondary">CLABE</div>
                                                    <div class="text-semibold text-dark">{{catalogPaymentMethod.additional_data.clabe}}</div>
                                                </div>
                                                <div>
                                                    <div class="text-xs text-secondary">Beneficiario</div>
                                                    <div class="text-semibold text-dark">{{catalogPaymentMethod.additional_data.beneficiary}}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <div class="btn-group">
                                                <button type="button" class="btn px-3 btn-outline-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">

                                                </button>
                                                <ul class="dropdown-menu shadow">
                                                    <li 
                                                        v-if="catalogPaymentMethod.status == 1">
                                                        <button class="dropdown-item" @click="inactivePaymentMethod(catalogPaymentMethod)">Inactivar</button>
                                                    </li>
                                                    <li v-else>
                                                        <button class="dropdown-item" @click="activePaymentMethod(catalogPaymentMethod)">Activar</button>
                                                    </li>
                                                    <li v-if="catalogPaymentMethod.recomend">
                                                        <button class="dropdown-item" @click="disableRecomendation(catalogPaymentMethod)">Quitar de recomendados</button>
                                                    </li>
                                                    <li v-else>
                                                        <button class="dropdown-item" @click="enableRecomendation(catalogPaymentMethod)">Añadir a recomendados</button>
                                                    </li>
                                                    
                                                    <li>
                                                        <button class="dropdown-item" @click="deletePaymentMethod(catalogPaymentMethod)">Eliminar</button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `
}

export { AdminpaymentmethodsViewer }