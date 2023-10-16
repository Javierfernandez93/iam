import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.6'

const AdminusersViewer = {
    name : 'adminusers-viewer',
    data() {
        return {
            UserSupport: new UserSupport,
            busy: false,
            users: null,
            outputUsers: null,
            usersAux: null,
            query: null,
            columns: { // 0 DESC , 1 ASC 
                company_id: {
                    name: 'company_id',
                    desc: false,
                },
                signup_date: {
                    name: 'signup_date',
                    desc: false,
                },
                licences: {
                    name: 'licences',
                    desc: false,
                },
                active: {
                    name: 'active',
                    desc: false,
                },
                phone: {
                    name: 'phone',
                    desc: false,
                },
                names: {
                    name: 'names',
                    desc: false,
                    alphabetically: true,
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
        sortData(column) {
            this.users.sort((a, b) => {
                const _a = column.desc ? a : b
                const _b = column.desc ? b : a

                return column.alphabetically ? _a[column.name].localeCompare(_b[column.name]) : _a[column.name] - _b[column.name]
            })

            column.desc = !column.desc
        },
        filterData() {
            this.users = this.usersAux
            this.users = this.users.filter(user =>  user.names.toLowerCase().includes(this.query.toLowerCase()) || user.email.toLowerCase().includes(this.query.toLowerCase()) || user.company_id.toString().includes(this.query.toLowerCase()))
        },
        getInBackoffice(company_id) {
            this.UserSupport.getInBackoffice({ company_id: company_id }, (response) => {
                if (response.s == 1) {
                    window.open('../../apps/backoffice')
                }
            })
        },
        addTrial(user) {
            let alert = alertCtrl.create({
                title: "Alert",
                subTitle: `¿Estás seguro de activar el trial a <b>${user.names}</b>?`,
                buttons: [
                    {
                        text: "Sí",
                        class: 'btn-success',
                        role: "cancel",
                        handler: (data) => {
                            
                            this.UserSupport.addTrial({user_login_id:user.user_login_id},(response)=>{
                                if(response.s == 1)
                                {
                                    alertInfo({
                                        icon:'<i class="bi bi-ui-checks"></i>',
                                        message: `Hemos activado al usuario ${user.names}`,
                                        _class:'bg-gradient-success text-white'
                                    })
                                } else {
                                    alertInfo({
                                        icon:'<i class="bi bi-x"></i>',
                                        message: 'Error al activar al usuario',
                                        _class:'bg-gradient-danger text-white'
                                    })
                                }
                            })
                        },
                    },
                    {
                        text: "Cancel",
                        role: "cancel",
                        handler: (data) => {
                            
                        },
                    },
                ],
            })

            alertCtrl.present(alert.modal)  
        },
        deleteUser(company_id) {
            this.UserSupport.deleteUser({ company_id: company_id }, (response) => {
                if (response.s == 1) {

                    alertInfo({
                        icon:'<i class="bi bi-check"></i>',
                        size: 'modal-md',
                        message: `<div class="pb-5">Hemos borrado al usuario correctamente</div>`,
                        _class:'bg-gradient-success text-white'
                    })

                    this.getUsersMaster()
                }
            })
        },
        goToEdit(company_id) {
            window.location.href = '../../apps/admin-users/edit?ulid=' + company_id
        },
        addLicenceToUser(company_id) {
            let alert = alertCtrl.create({
                title: "Añadir Licencias",
                subTitle: `<div class="text-xs text-secondary fw-sembold">Ingresa la cantidad</div>`,
                inputs : [
                    {
                        type : 'number',
                        id: 'amount',
                        name: 'amount',
                        placeholder: 'Escribe aqui'
                    }
                ],
                buttons: [
                    {
                        text: "Sí, añadir",
                        role: "cancel",
                        class: 'btn-danger',
                        handler: (data) => {
                            this.UserSupport.addLicenceToUser({ company_id: company_id, amount: data.amount }, (response) => {
                                if (response.s == 1) {
                                    alertInfo({
                                        icon:'<i class="bi bi-ui-checks"></i>',
                                        message: `<div class="text-white">Licencias añadidas</div>`,
                                        _class:'bg-gradient-success text-white'
                                    },500)
                                }
                            })
                        },
                    },
                    {
                        text: "Cancelar",
                        role: "cancel",
                        handler: (data) => {
                        },
                    },
                ],
            })

            alertCtrl.present(alert.modal); 
        },
        addCreditToUser(company_id) {
            let alert = alertCtrl.create({
                title: "Añadir créditos",
                subTitle: `<div class="text-xs text-secondary fw-sembold">Ingresa la cantidad</div>`,
                inputs : [
                    {
                        type : 'number',
                        id: 'amount',
                        name: 'amount',
                        placeholder: 'Escribe aqui'
                    }
                ],
                buttons: [
                    {
                        text: "Sí, añadir",
                        role: "cancel",
                        class: 'btn-danger',
                        handler: (data) => {
                            this.UserSupport.addCreditToUser({ company_id: company_id, amount: data.amount }, (response) => {
                                if (response.s == 1) {
                                    alertInfo({
                                        icon:'<i class="bi bi-ui-checks"></i>',
                                        message: `<div class="text-white">Créditos añadidos</div>`,
                                        _class:'bg-gradient-success text-white'
                                    },500)
                                }
                            })
                        },
                    },
                    {
                        text: "Cancelar",
                        role: "cancel",
                        handler: (data) => {
                        },
                    },
                ],
            })

            alertCtrl.present(alert.modal); 
        },
        deletePlan(company_id) {
            let alert = alertCtrl.create({
                title: "Aviso",
                subTitle: "¿Estás seguro de eliminar el plan de éste usuario?. Ya no recibirá más ganancias a partir de ahora",
                buttons: [
                    {
                        text: "Sí, eliminar",
                        role: "cancel",
                        class: 'btn-danger',
                        handler: (data) => {
                            this.UserSupport.deletePlan({ company_id: company_id }, (response) => {
                                if (response.s == 1) {
                                    this.getUsersMaster()
                                }
                            })
                        },
                    },
                    {
                        text: "Cancelar",
                        role: "cancel",
                        handler: (data) => {
                        },
                    },
                ],
            })

            alertCtrl.present(alert.modal); 
        },
        viewEwallet(user) {
            this.UserSupport.viewEwallet({user_login_id:user.user_login_id}, (response) => {
                if (response.s == 1) {
                    user.ewallet = response.ewallet
                }
            })
        },
        goToViewPublicKey(publicKey) {
            window.location.href = `../../apps/admin-wallet/?publicKey=${publicKey}`
        },
        sendActivationWhatsApp(user) {
            let alert = alertCtrl.create({
                title: "Enviar WhatsApp",
                subTitle: `Envíaremos un WhatsApp con un mensaje preguntando por que aun ${user.names} no compra su cuenta`,
                buttons: [
                    {
                        text: "Sí, envíar",
                        role: "cancel",
                        class: 'btn-success',
                        handler: (data) => {
                            this.UserSupport.sendActivationWhatsApp(user, (response) => {
                                if (response.s == 1) {

                                }
                            })
                        },
                    },
                    {
                        text: "Cancelar",
                        role: "cancel",
                        handler: (data) => {
                        },
                    },
                ],
            })

            alertCtrl.present(alert.modal); 
        },
        takeUnactivedUsers(publicKey) {
            let alert = alertCtrl.create({
                title: "Aviso",
                subTitle: `Ingresa la información para enviar`,
                inputs: [
                    {
                        type: 'number',
                        name: 'leftDays',
                        id: 'leftDays',
                        placeholder: 'Ingresa el número de días limite de usuario',
                    }
                ],
                buttons: [
                    {
                        text: "Tomar usuarios",
                        class: 'btn-success',
                        role: "cancel",
                        handler: (data) => {
                            this.outputUsers = this.users.filter((user)=>{
                                let show = false

                                if(user.buy)
                                {
                                    if(user.buy.leftDays <= data.leftDays)
                                    {
                                        show = true
                                    }
                                }

                                return show 
                            })
                        },
                    },
                    {
                        text: "Cancelar",
                        role: "cancel",
                        handler: (data) => {
                        },
                    },
                ],
            })

            alertCtrl.present(alert.modal);
        },
        copyPublicKey(publicKey) {
            navigator.clipboard.writeText(publicKey).then(function() {
                console.log('Async: Copying to clipboard was successful!');
            }, function(err) {
                console.error('Async: Could not copy text: ', err);
            });
        },
        getUsers() {
            return new Promise((resolve,reject) => {
                this.busy = true
                this.UserSupport.getUsers({}, (response) => {
                    this.busy = false
                    if (response.s == 1) {
                        resolve(response.users)
                    }

                    reject()
                })
            })
        },
        getUsersMaster() {
            this.query = null
            this.users = null
            this.usersAux = null

            this.getUsers().then((users) => {
                this.usersAux = users
                this.users = users
            }).catch((err) => {
                this.users = false
                this.usersAux = false
            })
        },
    },
    mounted() {
        this.getUsersMaster()
    },
    template : `
    
        <div class="card border-radius-2xl mb-4">
            <div class="card-header pb-0">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <i class="bi bi-pie-chart-fill text-gradient text-primary fs-3"></i>
                    </div>
                    <div v-if="users" class="col fw-semibold text-dark">
                        <div><span class="badge bg-secondary text-xxs">Total {{users.length}}</span></div>
                        <div class="fs-5">Usuarios</div>
                    </div>
                    <div class="col-auto text-end d-none">
                        <div><a href="../../apps/admin-users/add" type="button" class="btn shadow-none mb-0 btn-success px-3 btn-sm">Añadir usuario</a></a></div>
                    </div>
                </div>
            </div>
            <div class="card-header">
                <input v-model="query" :autofocus="true" type="text" class="form-control" placeholder="Buscar..." />
            </div>

            <div v-if="outputUsers" class="card-body">
                <div v-for="user in outputUsers">
                    <div v-if="user.phone">
                        {{user.phone.formatPhoneNumber(user.countryData.phone_code).getNumber()}}
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-center py-3">
                <span v-if="busy" class="spinner-grow spinner-grow-sm" aria-hidden="true">
                </span>
            </div>
            <div
                v-if="users" 
                class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr class="align-items-center">
                                <th @click="sortData(columns.company_id)" class="text-center c-pointer text-uppercase text-secondary font-weight-bolder opacity-7">
                                    <span v-if="columns.company_id.desc">
                                        <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                    </span>    
                                    <span v-else>    
                                        <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                    </span>    
                                    <u class="text-sm ms-2">ID</u>
                                </th>
                                <th 
                                    @click="sortData(columns.names)"
                                    class="text-start c-pointer text-uppercase text-primary font-weight-bolder opacity-7">
                                    <span v-if="columns.names.desc">
                                        <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                    </span>    
                                    <span v-else>    
                                        <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                    </span>    
                                    <u class="text-sm ms-2">Usuario</u>
                                </th>
                                <th 
                                    @click="sortData(columns.active)"
                                    class="text-center c-pointer text-uppercase text-primary font-weight-bolder opacity-7">
                                    <span v-if="columns.active.desc">
                                        <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                    </span>    
                                    <span v-else>    
                                        <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                    </span>    
                                    <u class="text-sm ms-2">Tipo cuenta</u>
                                </th>
                                <th 
                                    @click="sortData(columns.active)"
                                    class="text-center c-pointer text-uppercase text-primary font-weight-bolder opacity-7">
                                    <span v-if="columns.active.desc">
                                        <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                    </span>    
                                    <span v-else>    
                                        <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                    </span>    
                                    <u class="text-sm ms-2">País</u>
                                </th>
                                <th 
                                    @click="sortData(columns.phone)"
                                    class="text-center c-pointer text-uppercase text-primary font-weight-bolder opacity-7">
                                    <span v-if="columns.phone.desc">
                                        <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                    </span>    
                                    <span v-else>    
                                        <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                    </span>    
                                    <u class="text-sm ms-2">Teléfono</u>
                                </th>
                                <th 
                                    @click="sortData(columns.signup_date)"
                                    class="text-center c-pointer text-uppercase text-primary font-weight-bolder opacity-7">
                                    <span v-if="columns.signup_date.desc">
                                        <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                    </span>    
                                    <span v-else>    
                                        <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                    </span>    
                                    <u class="text-sm ms-2">Miembro desde</u>
                                </th>
                                <th class="text-center text-uppercase text-xxs font-weight-bolder opacity-7">Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in users">
                                <td class="align-middle text-center text-sm">
                                    <p class="font-weight-bold mb-0">{{user.company_id}}</p>
                                </td>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div v-if="user.image" class="avatar avatar-sm me-2">
                                            <img :src="user.image" alt="referido"
                                                class="border-radius-lg shadow">
                                        </div>
                                        <div v-else>
                                            <div v-if="user.names" class="avatar avatar-sm me-2 bg-dark">
                                                {{ user.names.getFirstLetter() }}
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">{{user.names}}</h6>
                                            <p class="text-xs text-secondary mb-0">{{user.email}}</p>
                                        </div>
                                    </div>
                                    <div v-if="user.ewallet" class="border-top pt-3 mt-3">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <span class="badge p-0 text-secondary">Public key</span>
                                            </div>
                                            <div class="col-auto">
                                                <button @click="goToViewPublicKey(user.ewallet.public_key)" class="btn btn-light btn-sm px-3 me-2 mb-0 shadow-none">Enviar</button>
                                                <button @click="copyPublicKey(user.ewallet.public_key)" class="btn btn-light btn-sm px-3 mb-0 shadow-none">Copiar</button>
                                            </div>
                                            <div>
                                                <span class="text-s">{{user.ewallet.public_key}}</span>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="badge p-0  text-secondary">Balance</span>
                                            <div class="text-dark fw-semibold">$ {{user.ewallet.amount.numberFormat(2)}} USD</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle text-center">
                                    <span v-if="user.buy" class="badge bg-primary">
                                        Días restantes {{user.buy.leftDays}}
                                    </span>
                                </td>
                                <td class="align-middle text-center">
                                    <span v-if="user.country_id" class="badge border border-secondary text-secondary">
                                        <img :src="user.country_id.getCoutryImage()" style="width:16px"/>
                                        {{user.countryData.country}}
                                    </span>
                                </td>
                                <td class="align-middle text-center text-xs">
                                    <span v-if="user.phone">
                                        <a :href="user.phone.formatPhoneNumber(user.countryData.phone_code).sendWhatsApp('¡Hola *'+user.names+'*! te contactamos de IAM')">
                                            +{{user.phone.formatPhoneNumber(user.countryData.phone_code)}}
                                        </a>
                                    </span>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    <p class="text-xs font-weight-bold mb-0">Fecha</p>
                                    <p class="text-xs text-secondary mb-0">{{user.signup_date.formatDate()}}</p>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-outline-primary px-3 btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">

                                        </button>
                                        <ul class="dropdown-menu shadow">
                                            <li><button class="dropdown-item" @click="goToEdit(user.user_login_id)">Editar</button></li>
                                            <li><button class="d-none dropdown-item" @click="viewEwallet(user)">Ver e-wallet</button></li>
                                            
                                            <li><button class="dropdown-item" @click="getInBackoffice(user.user_login_id)">Acceder a backoffice</button></li>
                                            <li><button class="dropdown-item" @click="deleteUser(user.user_login_id)">Eliminar</button></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div v-else-if="users == false"
                class="card-body">
                <div class="alert alert-secondary text-white text-center">
                    <div>No tenemos usuarios aún</div>
                </div>
            </div>
        </div>
    `,
}

export { AdminusersViewer } 