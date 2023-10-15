<div class="container-fluid py-4" id="app">
    <div class="alert shadow-xl c-pointer alert-info text-white" @click="openVideo">
        <div class="row align-items-center">
            <div class="col-auto">
                <i class="bi bi-camera-video"></i>
            </div>
            <div class="col">
                <div class="fw-bold text-white">Tutorial de retiros </div>
                <div>Da clic aquí para ver el tutorial de retiros</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="row">
                <div class="col-xl-7 mb-xl-0 mb-4">
                    <div class="card bg-transparent shadow-xl">
                        <div class="overflow-hidden position-relative border-radius-xl" style="background-image: url('../../src/img/curved-images/curved14.jpg');">
                            <span class="mask bg-gradient-dark"></span>
                            <div class="card-body position-relative z-index-1 p-3">
                                <i class="fas fa-wifi text-white p-2" aria-hidden="true"></i>
                                <div class="mt-4 mb-5">
                                    <h5 class="text-white text-uppercase pb-2">DummieTrading</h5>
                                    <h5 class="text-white text-uppercase pb-2">$ {{balance.numberFormat(2)}}</h5>
                                </div>
                                <div class="d-flex">
                                    <div class="d-flex">
                                        <div class="me-4">
                                            <p class="text-white text-sm opacity-8 mb-0">Card Holder</p>
                                            <h6 class="text-white mb-0">{{ user.names }}</h6>
                                        </div>
                                        <div>
                                            <p class="text-white text-sm opacity-8 mb-0">Expires</p>
                                            <h6 class="text-white mb-0">11/29</h6>
                                        </div>
                                    </div>
                                    <div class="ms-auto w-20 d-flex align-items-end justify-content-end">
                                        <img class="w-60 mt-2" src="../../src/img/logos/mastercard.png" alt="logo">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-auto">
                                    <div class="icon icon-shape icon-sm bg-gradient-primary shadow text-center border-radius-lg">
                                        <i class="fas fa-landmark opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>
                                <div clasS="col">
                                    <h6>Retiro de dinero</h6>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label>
                                    Método de retiro
                                </label>
                                <select class="form-control" v-model="withdraw.catalog_withdraw_method_id">
                                    <option value="0">Método de retiro</option>
                                    <option v-for="withdraw_method in withdraw_methods" v-bind:value="withdraw_method.catalog_withdraw_method_id">
                                        {{withdraw_method.method}} - {{withdraw_method.account}}
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>
                                    Cantidad
                                </label>
                                <input 
                                    v-model="withdraw.ammount"
                                    type="number" class="form-control"  placeholder="$0"/>
                            </div>
                            <div
                                v-if="feedback" 
                                class="alert alert-secondary text-white">
                                <strong>Aviso</strong>
                                <div>{{feedback}}</div>
                            </div>
                            <button 
                                :disabled="!withdrawComplete"
                                @click="doWithdraw"
                                class="btn btn-primary" type="submit">Retirar dinero</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mb-lg-0 mb-4">
                    <div class="card mt-4">
                        <div class="card-header pb-0 p-3">
                            <div class="row">
                                <div class="col-6 d-flex align-items-center">
                                    <h6 class="mb-0">Método de retiro</h6>
                                </div>
                                <div class="col-6 text-end d-none">
                                    <a class="btn bg-gradient-dark mb-0" href="javascript:;"><i class="fas fa-plus" aria-hidden="true"></i>&nbsp;&nbsp;Add New Card</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                <div
                                    v-for="all_withdraw_method in all_withdraw_methods"
                                    class="col-md-6 mb-4">
                                    <div class="card card-body border card-plain border-radius-lg d-flex align-items-center flex-row">
                                        <img class="w-10 me-3 mb-0" :src="all_withdraw_method.image" alt="logo">
                                        <div class="w-100">
                                            <div><h6 class="mb-0 text-xxs text-secondary">{{all_withdraw_method.method}}</h6></div>
                                            <div v-if="!all_withdraw_method.editing"
                                                @click="toggleEditing(all_withdraw_method)">
                                                <div v-if="all_withdraw_method.account"
                                                    class="text-truncate">
                                                    <h6 class="mb-0">{{all_withdraw_method.account}} - {{all_withdraw_method.wallet}}</h6>
                                                </div>
                                                <div v-else>
                                                    Configurar cuenta
                                                </div>
                                            </div>
                                            <div v-else>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <label>Account alias</label>

                                                        <input 
                                                            v-model="all_withdraw_method.account"
                                                            @keydown.enter.exact.prevent="editWithdrawMethod(all_withdraw_method)"
                                                            placeholder="Account alias"
                                                            type="text" class="form-control"/>
                                                    </div>
                                                    <div class="col-6">
                                                        <label>Wallet alias</label>

                                                        <input 
                                                            v-model="all_withdraw_method.wallet"
                                                            @keydown.enter.exact.prevent="editWithdrawMethod(all_withdraw_method)"
                                                            placeholder="Wallet alias"
                                                            type="text" class="form-control"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <span 
                                            v-if="!all_withdraw_method.editing"
                                            @click="toggleEditing(all_withdraw_method)"
                                            class="ms-auto">
                                            <i class="fas fa-pencil-alt text-dark cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="top" aria-hidden="true" aria-label="Edit Card"></i>
                                            <span class="sr-only">Editar tarjeta</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100 mb-4">
                <div class="card-header pb-0 px-3">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-0">Últimos retiros</h6>
                        </div>
                        <div class="col-md-6 d-flex justify-content-end align-items-center">
                            <i class="far fa-calendar-alt me-2" aria-hidden="true"></i>
                            <!-- <small>23 - 30 March 2020</small> -->
                        </div>
                    </div>
                </div>
                <div
                    v-if="withdraws.length > 0" 
                    class="card-body pt-4 p-3">
                    <ul class="list-group">
                        <li 
                            v-for="withdraw in withdraws"
                            :class="withdraw.status == 2 ?  'opacity-50': ''"
                            class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <button class="btn btn-icon-only btn-rounded btn-outline-danger mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"><i class="fas fa-arrow-down" aria-hidden="true"></i></button>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">
                                        Retiro -
                                        <span v-if="withdraw.status == 1"
                                            class="text-danger">
                                            Pendiente
                                        </span>
                                        <span v-else-if="withdraw.status == 2"
                                            class="text-success">
                                            Depósitado
                                        </span>
                                    </h6>
                                    <span class="text-xs">
                                        {{withdraw.create_date.formatFullDate()}}
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center text-danger text-gradient text-sm font-weight-bold">
                                $ {{withdraw.ammount.numberFormat(2)}}
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>