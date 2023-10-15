<div class="container-fluid py-4" id="app">
    
    <div v-if="Object.keys(plans).length > 0">
        <div
            class="card mb-3">
            <div class="card-header pb-0">
                <div class="row align-items-center">
                    <div class="col fw-semibold text-dark">Planes</div>
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Plan</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Rendimiento mensual</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Monto mínimo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="plan in plans">
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div class="pe-2">
                                            <i class="bi bi-graph-up-arrow text-primary"></i>
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h3 class="mb-0 text-sm">PLAN {{plan.name.numberFormat(0)}}</h3>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <h3 class="mb-0 text-sm">{{plan.profit}} <i class="bi bi-percent"></i></h3>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    <h3 class="mb-0 text-sm"><i class="bi bi-currency-dollar"></i> {{plan.name.numberFormat()}} USD </h3>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div v-else>
        <div class="alert alert-secondary text-center text-white">
            No tenemos planes aún. Vuelve más tarde
        </div>
    </div>
</div>