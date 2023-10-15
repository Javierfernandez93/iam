<div class="container-fluid py-4" id="app">
    <div class="row">
        <div class="col-12" v-if="user">
            <div class="card mb-3">
                <div class="card-header pb-0 px-3">
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="mb-0">Usuario</h6>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-auto">
                            <div class="avatar avatar-xl overflow-hidden img-upload position-relative">
                                <img class="w-100 border-radius-lg shadow-sm" :src="user.image">
                            </div>
                        </div>
                        <div class="col-auto">
                            <span class="text-xs">ID</span>
                            <h6 class="mb-1 text-dark text-sm">
                                {{user.company_id}}
                            </h6>
                        </div>

                        <div class="col-auto">
                            <span class="text-xs">Nombre</span>
                            <h6 class="mb-1 text-dark text-sm">
                                {{user.names}}
                            </h6>
                        </div>
                        <div class="col-auto">
                            <span class="text-xs">Email</span>
                            <h6 class="mb-1 text-dark text-sm">
                                {{user.email}}
                            </h6>
                        </div>
                        <div class="col-auto">
                            <span class="text-xs">Teléfono</span>
                            <h6 class="mb-1 text-dark text-sm">
                                {{user.phone}}
                            </h6>
                        </div>
                        <div class="col">
                            <span class="text-xs">Miembro desde</span>
                            <h6 class="mb-1 text-dark text-sm">
                                {{user.signup_date.formatDate()}}
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-xl-8">
            <div class="card">
                <div class="card-header pb-0 px-3">
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="mb-0">Activación de usuario</h6>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label>Plan</label>
                        <div v-if="selectedPlan" class="fw-semibold text-primary">
                            <u>
                                PLAN $ {{selectedPlan.name.numberFormat(0)}} - % {{selectedPlan.profit}} Profit
                            </u>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Monto invertido</label>
                        <input @keydown.enter.exact.prevent="$refs.sponsor_profit.focus()" v-model="user.ammount" type="text" class="form-control" placeholder="$0" aria-label="$0" aria-describedby="basic-addon1">
                    </div>

                    <div class="mb-3">
                        <label>Profit para patrocinador</label>
                        <input @keydown.enter.exact.prevent="$refs.aditional_profit.focus()" v-model="user.sponsor_profit" ref="sponsor_profit" type="text" class="form-control" placeholder="0%" aria-label="0%" aria-describedby="basic-addon1">
                    </div>

                    <div class="mb-3">
                        <label>Profit adicional</label>
                        <input @keydown.enter.exact.prevent="updatePlan" v-model="user.additional_profit" ref="aditional_profit" type="text" class="form-control" placeholder="0%" aria-label="0%" aria-describedby="basic-addon1">
                    </div>


                    <div v-if="user.additional_profit > 0" class="alert alert-secondary text-white text-center fw-semibold">

                        <strong>Aviso</strong> El profit final para éste usuario:
                        <div>PLAN $ {{selectedPlan.name.numberFormat(0)}} = {{selectedPlan.profit}}% + </div>
                        <div>Profit adicional = {{user.additional_profit}}%</div>
                        <div>Profit sponsor = {{user.sponsor_profit}}%</div>
                        <div class="fw-semibold">Total = {{parseFloat(user.additional_profit) + parseFloat(selectedPlan.profit) + parseFloat(user.sponsor_profit)}}% </div>
                    </div>
                    <button @click="updatePlan" ref="buttonPlan" class="btn btn-primary" type="button">Guardar/Actualizar</button>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4" v-if="user">
            <?php if ($UserSupport->hasPermission('add_old_commission')) { ?>
                <div class="card bg-gradient-light mb-3">
                    <div class="card-header bg-transparent pb-0 px-3">
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="mb-0">Correr comisiones pasadas</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info text-white">
                            Elige el rango de fechas que correrán las ganancias para éste usuario
                        </div>
                        <div class="mb-3">
                            <label>Fecha inicio</label>
                            <input v-model="runComission.start_date" type="date" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Fecha fin</label>
                            <input v-model="runComission.end_date" type="date" class="form-control">
                        </div>

                        <button 
                            :disabled="!runComission.start_date || !runComission.end_date"
                            @click="runOldComissions(user.company_id)" ref="button" class="btn btn-primary" type="button">Correr ganancias</button>
                    </div>

                    <ul v-if="responses.length > 0" class="list-group">
                        <li v-for="response in responses" class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span v-if="response.s == 1">
                                        <i class="bi bi-bookmark-check text-success"></i>
                                    </span>
                                    <span v-else>
                                        <i class="bi bi-exclamation text-danger"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="small fw-semibold text-muted">
                                        {{response.day}}
                                    </div>
                                    {{response.r}}
                                </div>
                                <div class="col-auto text-end small text-muted">
                                    <div>
                                        Time
                                    </div>
                                    <div>
                                        <span v-if="response.time">
                                            {{response.time.numberFormat(2)}} segs.
                                        </span>
                                        <span v-else>
                                            N/A
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            <?php } ?>
            <?php if ($UserSupport->hasPermission('add_deposit')) { ?>
                <div class="card bg-gradient-light mb-3">
                    <div class="card-header bg-transparent pb-0 px-3">
                        <div class="row">
                            <div class="col">
                                <h6 class="mb-0">Añadir depósito</h6>
                            </div>
                            <?php if($UserSupport->hasPermission('list_deposit')) { ?>
                                <div class="col-auto">
                                    <button class="btn btn-secondary btn-sm" @click="viewDeposits(user.user_login_id)">
                                        Ver fondeos
                                    </button>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label>Monto a depósitar</label>
                            <input @keydown.enter.exact.prevent="$refs.buttonAddDeposit.focus()" v-model="deposit.ammount" type="text" class="form-control" placeholder="$0" aria-label="$0" aria-describedby="basic-addon1">
                        </div>
                        <button :disabled="!deposit.ammount" @click="addDeposit" ref="buttonAddDeposit" class="btn btn-primary" type="button">Añadir depósito</button>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>