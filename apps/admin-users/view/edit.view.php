<div class="container-fluid py-4" id="app">
    <div class="card">
        <div class="card-header pb-0 px-3"> 
            <div class="small">Editar usuario</div>
            <h6 class="text-uppercase text-primary">{{user.names}}</h6>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-12 col-xl-3">
                    <label>Usuario</label>
                    <input 
                        :autofocus="true"
                        :class="user.names ? 'is-valid' : ''"
                        @keydown.enter.exact.prevent="$refs.email.focus()"
                        v-model="user.names"
                        ref="names"
                        type="text" class="form-control" placeholder="Nombre(s)">
                </div>
                <div class="col-12 col-xl-3">
                    <label>Correo</label>
                    <input 
                        v-model="user.email"
                        :class="user.email ? 'is-valid' : ''"
                        @keydown.enter.exact.prevent="$refs.password.focus()"
                        ref="email"
                        type="text" class="form-control" placeholder="Email">
                </div>
                <div class="col-12 col-xl-3">
                    <label>Contraseña</label>       
                    <input 
                        v-model="user.password"
                        :class="user.password ? 'is-valid' : ''"
                        @keydown.enter.exact.prevent="$refs.phone.focus()"
                        ref="password"
                        type="text" class="form-control" placeholder="Password">
                </div>
                <div class="col-12 col-xl-3">
                    <label>Teléfono</label>
                    <input 
                        v-model="user.phone"
                        :class="user.phone ? 'is-valid' : ''"
                        @keydown.enter.exact.prevent="updateUser"
                        ref="phone"
                        type="text" class="form-control" placeholder="Número">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12 col-xl-4">
                    <label>Referido por</label>
                    <input 
                        v-model="user.referral.user_login_id"
                        :class="user.referral.user_login_id ? 'is-valid' : ''"
                        @keydown.enter.exact.prevent="$refs.signup_date.focus()"
                        ref="referral_id"
                        type="text" class="form-control" placeholder="Referido por">
                </div>
                <div class="col-12 col-xl-4">
                    <label>Nombre</label>
                    <input 
                        v-model="user.referral.names"
                        :class="user.referral.names ? 'is-valid' : ''"
                        @keydown.enter.exact.prevent="$refs.commission.focus()"
                        ref="referral_id"
                        type="text" class="form-control" placeholder="Referido por">
                </div>
                <div class="col-12 col-xl-4">
                    <label>Ganancia por referidos (%) de {{user.names}}</label>
                    <input 
                        v-model="user.referral.commission"
                        :class="user.referral.commission ? 'is-valid' : ''"
                        @keydown.enter.exact.prevent="$refs.signup_date.focus()"
                        ref="commission"
                        type="text" class="form-control" placeholder="%">
                </div>
            </div>
            <div class="mb-3">
                <label>Fecha de ingreso</label>
                <input 
                    v-model="user.signup_date"
                    :class="user.signup_date ? 'is-valid' : ''"
                    @keydown.enter.exact.prevent="$refs.phone.focus()"
                    ref="signup_date"
                    type="date" class="form-control" placeholder="Fecha de ingreso">
            </div>
        
            <button 
                :disabled="!userComplete"
                ref="button"
                type="submit" class="btn btn-primary" @click="updateUser">Actualizar usuario</button>
        </div>
    </div>
</div>