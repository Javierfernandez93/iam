<div class="container-fluid py-4" id="app">
    <div class="card">
        <div class="card-header pb-0 px-3">
            <h6 class="mb-0">Editar administrador</h6>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label>Nombre</label>
                <input :autofocus="true" :class="administrator.names ? 'is-valid' : ''" @keydown.enter.exact.prevent="$refs.email.focus()" v-model="administrator.names" ref="names" type="text" class="form-control" placeholder="nombre">
            </div>
            <div class="mb-3">
                <label>Correo electrónico</label>
                <input v-model="administrator.email" :class="administrator.email ? 'is-valid' : ''" @keydown.enter.exact.prevent="$refs.password.focus()" ref="email" type="text" class="form-control" placeholder="Email">
            </div>
            <div class="mb-3">
                <label>Contraseña</label>
                <input 
                    v-model="administrator.password" 
                    @keydown.enter.exact.prevent="editAdministrator" 
                    ref="password" 
                    :class="administrator.password ? 'is-valid' : ''"
                    type="text" class="form-control" placeholder="Password">
            </div>
            <?php if($UserSupport->hasPermission('edit_permissions')) { ?>
                <div class="mb-3">
                    <label>Permisos</label>
                    <ul class="list-group">
                        <li v-for="permission in administrator.permissions" class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="form-check form-switch ps-0">
                                        <input 
                                            v-model="permission.checked"
                                            class="form-check-input ms-auto" type="checkbox" id="referral_email" />
                                    </div>
                                </div>
                                <div class="col">
                                    <div><span class="small text-primary">{{permission.permission}}</span></div>
                                    <div>{{permission.description}}</div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            <?php } ?>

            <button ref="button" type="submit" class="btn btn-primary" @click="editAdministrator">Editar adminsitrador</button>
        </div>
    </div>
</div>