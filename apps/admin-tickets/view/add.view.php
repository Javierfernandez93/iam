<div class="container-fluid py-4" id="app">
    <div class="card">
        <div class="card-header pb-0 px-3"> 
            <div class="small">Añadir usuario</div>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label>Usuario</label>
                <input 
                    :autofocus="true"
                    :class="user.names ? 'is-valid' : ''"
                    @keydown.enter.exact.prevent="$refs.email.focus()"
                    v-model="user.names"
                    ref="names"
                    type="text" class="form-control" placeholder="Nombre(s)">
            </div>
            <div class="mb-3">
                <label>Correo</label>
                <input 
                    v-model="user.email"
                    :class="user.email ? 'is-valid' : ''"
                    @keydown.enter.exact.prevent="$refs.password.focus()"
                    ref="email"
                    type="text" class="form-control" placeholder="Email">
            </div>
            <div class="mb-3">
                <label>Contraseña</label>
                <input 
                    v-model="user.password"
                    :class="user.password ? 'is-valid' : ''"
                    @keydown.enter.exact.prevent="$refs.referral.focus()"
                    ref="password"
                    type="text" class="form-control" placeholder="Password">
            </div>

            <div class="mb-3">
                <label>Referido por</label>
                <input 
                    v-model="user.referral.user_login_id"
                    :class="user.referral.user_login_id ? 'is-valid' : ''"
                    @keydown.enter.exact.prevent="$refs.phone.focus()"
                    ref="referral"
                    type="text" class="form-control" placeholder="Referido">
            </div>

            <div class="mb-3">
                <label>Teléfono</label>
                <div class="row">
                    <div class="col-auto">
                        <select class="form-select" v-model="user.country_id" aria-label="Selecciona tu país">
                            <option>Selecciona tu país</option>
                            <option v-for="country in countries" v-bind:value="country.country_id">
                                {{ country.nicename }} <span v-if="country.phone_code > 0">+ {{ country.phone_code }}</span>
                            </option>
                        </select>
                    </div>
                    <div class="col">
                        <div class="mb-3">
                            <input 
                                :class="user.phone ? 'is-valid' : ''"
                                ref="phone"
                                type="text" ref="phone" v-model="user.phone" class="form-control" @keydown.enter.exact.prevent="saveUser" placeholder="Teléfono" aria-label="Teléfono" aria-describedby="basic-addon1">
                        </div>
                    </div>
                </div>
            </div>

            <div
                v-if="feedback" 
                class="alert alert-secondary text-white text-center">
                {{feedback}}
            </div>
        
            <button 
                :disabled="!userComplete"
                ref="button"
                type="submit" class="btn btn-primary" @click="saveUser">Guardar usuario</button>
        </div>
    </div>
</div>