<div class="row d-flex justify-content-center align-items-center vh-100" id="app">
    <div class="col-12 col-xl-6 img-bg bg-primary order-1">
        <div class="oblique position-absolute top-0 h-100 d-md-block d-none me-n8">
            <div class="oblique-image bg-cover position-absolute fixed-top ms-auto h-100 z-index-0 ms-n6" style="background-image:url('../../src/img/city.jpg')"></div>
        </div>
    </div>
    <div class="col-12 col-xl-6">
        <div class="row justify-content-center text-center">
            <div class="col-11 col-xl-6">
                <div
                    v-if="paswordReseted == false" 
                    class="card text-start shadow p-3">
                    <div class="card-header bg-transparent border-0">
                        <div class="fs-4 fw-bold">Cambiar contraseña</div>
                        <div class="text-muted">Ingresa tu nueva contraseña para <span class="fw-semibold text-dark">{{user.email}}</span></div>
                    </div>
                    <div class="card-body">

                        <label>Contraseña</label>
                        <div class="input-group mb-3">
                            <input 
                                :class="user.password ? 'is-valid' : ''"
                                :autofocus="true"
                                type="password" ref="password" v-model="user.password" class="form-control" @keydown.enter.exact.prevent="$refs.passwordVerificator.focus()" placeholder="Password" aria-label="Password" aria-describedby="basic-addon1">
                        </div>

                        <label>Contraseña de nuevo</label>
                        <div class="input-group mb-3">
                            <input 
                                :class="hasValidPasswords ? 'is-valid' : ''"
                                type="password" ref="passwordVerificator" v-model="user.passwordVerificator" class="form-control" @keydown.enter.exact.prevent="changePassword" placeholder="Password" aria-label="Password" aria-describedby="basic-addon1">
                        </div>

                        <div class="text-end">
                            <a href="../../apps/login/">¿Quieres ingresar a tu cuenta?</a>
                        </div>
                    </div>
                    <div class="card-footer pt-0">
                        <div v-show="feedback" class="alert alert-secondary text-white alert-dismissible fade show" role="alert">
                            {{ feedback }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>

                        <button :disabled="!hasValidPasswords" class="btn btn-lg btn-primary w-100" @click="changePassword" id="button">
                            Cambiar contraseña
                        </button>
                    </div>
                </div>    
                <div
                    v-else
                    class="card text-start shadow p-3">
                    <div class="card-header bg-transparent border-0">
                        <div class="fs-4 fw-bold">Cambiar contraseña</div>
                        <div class="row align-items-center">
                            <div class="col-auto fs-4 text-success">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div class="col">
                                Hemos cambiado tu contraseña a la contraseña indicada. Ya puedes acceder a tu cuenta
                            </div>
                        </div>
                    </div>
                    <div class="card-footer pt-0">
                        <div v-show="feedback" class="alert alert-secondary text-white alert-dismissible fade show" role="alert">
                            {{ feedback }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>

                        <a class="btn btn-lg btn-primary w-100" href="../../apps/login">
                            Ingresa a tu cuenta
                        </a>
                    </div>
                </div>    
            </div>
        </div>
    </div>
</div>