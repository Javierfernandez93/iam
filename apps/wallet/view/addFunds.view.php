<div class="container-fluid py-4" id="app">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <div class="row">
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-header pb-0 px-3">
                            <div class="row">
                                <div class="col-md-12">
                                    <h6 class="mb-0">Añadir fondos</h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div v-if="!checkoutData">
                                <label>Monto a fondear (en dólares)</label>
                                <div class="input-group mb-3">
                                    <input 
                                        :autofocus="true"
                                        v-model="transaction.ammount"
                                        type="text" class="form-control" placeholder="0.0" />
                                        
                                    <select 
                                        class="form-select" v-model="transaction.catalog_currency_id" aria-label="">
                                        <option v-for="catalogCurrency in catalogCurrencies" v-bind:value="catalogCurrency.catalog_currency_id">
                                            {{catalogCurrency.description}} - {{catalogCurrency.code}}
                                        </option>
                                    </select>
                                </div>


                                <button
                                    :disabled="!transaction.ammount || loading" 
                                    @click="createTransactionRequirement" class="btn btn-primary">

                                    <span v-if="!loading">
                                        Añadir fondos
                                    </span>
                                    <span v-else>
                                        <div class="spinner-border" role="status">
                                            <span class="sr-only"></span>
                                        </div>
                                    </span>
                                </button>
                            </div>
                            <div v-else>
                                <div class="alert alert-info text-white text-center">
                                    <strong>Aviso</strong>
                                    Haz clic en "IR A PAGAR" y sigue las instrucciones para fondear tu cuenta.
                                </div>
                                <div class="mb-3">
                                    <span class="text-xs">
                                        Cantidad ({{transaction.code}})
                                    </span>
                                    <h6 class="mb-1 text-dark text-sm">
                                        {{ checkoutData.amount }} {{transaction.code}}
                                    </h6>
                                </div>

                                <div class="mb-3">
                                    <span class="text-xs">
                                        Dirección ({{transaction.code}})
                                    </span>
                                    <h6 class="mb-1 text-dark text-sm">
                                        {{ checkoutData.address }} 
                                    </h6>
                                </div>
                                
                                <div class="mb-3">
                                    <span class="text-xs">
                                        Código QR
                                    </span>
                                    <h6 class="mb-1 text-dark text-sm">
                                        <img class="img-fluid" :src="checkoutData.qrcode_url" />
                                    </h6>
                                </div>
                                    
                            </div>
                        </div>
                        <div
                            v-if="checkoutData"
                            class="card-footer">
                            <h6 class="mb-1 text-dark text-sm">
                                <a target="_blank" class="btn btn-success w-100" :href="checkoutData.checkout_url">Ir a pagar</a>
                            </h6>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-header pb-0 px-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="mb-0">Últimos fondeos</h6>
                                </div>
                                <div class="col-md-6 d-flex justify-content-end align-items-center">
                                    <i class="far fa-calendar-alt me-2" aria-hidden="true"></i><!-- <small>23 - 30 March 2020</small> -->
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-4 p-3">
                            <div
                                v-if="lastTransactions.length > 0">

                                <ul class="list-group">
                                    <li 
                                        v-for="lastTransaction in lastTransactions"
                                        class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                        <div class="d-flex align-items-center">
                                            <button class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 btn-sm d-flex align-items-center justify-content-center">
                                                <i class="fas fa-arrow-up" aria-hidden="true"></i>
                                            </button>
                                            <div class="d-flex flex-column">
                                                <h6 class="mb-1 text-dark text-sm">
                                                    Fondeo -
                                                    <span v-if="lastTransaction.status == 1"
                                                        class="text-danger">
                                                        Pendiente
                                                    </span>
                                                    <span v-else-if="lastTransaction.status == 2"
                                                        class="text-success">
                                                        Depósitado
                                                    </span>
                                                </h6>
                                                <span class="text-xs">
                                                    {{lastTransaction.create_date.formatFullDate()}}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center text-danger text-gradient text-sm font-weight-bold">
                                            $ {{lastTransaction.ammount.numberFormat(2)}}
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>