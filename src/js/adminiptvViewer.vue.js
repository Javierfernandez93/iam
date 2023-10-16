import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.5'   

const AdminiptvViewer = {
    name : 'adminiptv-viewer',
    data() {
        return {
            UserSupport: new UserSupport,
            clients: null,
            clientsAux: null,
            query: null,
            STATUS_DEMO: {
                DELETE: { 
                    code: -1,
                    text: 'Eliminada',
                    _class : 'bg-dark',
                },
                FOR_ACTIVATE: { 
                    code: 0,
                    text: 'Esperando demo',
                    _class : 'bg-secondary',
                },
                IN_USE: { 
                    code: 1,
                    text: 'En uso',
                    _class : 'bg-success',
                },
                EXPIRED: { 
                    code: 2,
                    text: 'Expirado',
                    _class : 'bg-warning',
                },
            },
            STATUS_SERVICE: {
                DELETE: { 
                    code: -1,
                    text: 'Eliminada',
                    _class : 'bg-dark',
                },
                FOR_ACTIVATE: { 
                    code: 0,
                    text: 'Esperando servicio',
                    _class : 'bg-secondary',
                },
                IN_USE: { 
                    code: 1,
                    text: 'En uso',
                    _class : 'bg-success',
                },
                EXPIRED: { 
                    code: 2,
                    text: 'Expirado',
                    _class : 'bg-warning',
                },
            }
        }
    },
    watch: {
        query: {
            handler() {
                this.filterData()
            },
            deep: true
        },
    },
    methods: {
        filterData() {
            this.clients = this.clientsAux
            this.clients = this.clients.filter((client) => {
                return client.name.toLowerCase().includes(this.query.toLowerCase()) || client.email.toLowerCase().includes(this.query.toLowerCase()) 
            })
        },
        addClient() {
            this.feedBack = null

            if(this.credits > 0)
            {
                this.User.addClient(this.client, (response) => {
                    if (response.s == 1) {
                        alertInfo({
                            icon:'<i class="bi bi-ui-checks"></i>',
                            message: `<div class="h3 text-white">¡Felicidades!</div> <div>Hemos dado de alta tu cliente <strong>${response.name}</strong></div> <div>Daremos de alta tu cliente y tendrá acceso a la brevedad.</div> <div class="mt-2 fw-semibold">Podrás ver su contraseña y opciones para ver su transmisión en el menú IPTV > Mis clientes</div>`,
                            size: 'modal-md',
                            _class:'bg-gradient-success text-white'
                        })
                        
                        this.client = {
                            name : null,
                            email : '',
                            adult : true,
                        }
                    } else if(response.r == "NOT_SAVE") {
                        this.feedBack = this.STATUS.CLIENT_EXIST
                    }
                })
            } else {
                this.feedBack = this.STATUS.NOT_ENOUGH_CREDITS
            }
        },
        copyToClipboard(text,event) {
            navigator.clipboard.writeText(text).then(() => {
                event.target.innerText = 'Copiado'
            });
        },
        setUpIptvService(client) {
            this.addCredentials(client).then((data)=>{
                client.user_name = data.user_name
                client.client_password = data.client_password

                this.UserSupport.setUpIptvService(client,(response)=>{
                    if(response.s == 1)
                    {
                        client.service.active_date = response.active_date
                        client.service.status = response.status

                        this.sendServiceCredentials(client)
                    }
                })
            })
        },
        setUpIptvDemo(client) {
            this.addCredentials(client).then((data)=>{
                client.user_name = data.user_name
                client.client_password = data.client_password

                this.UserSupport.setUpIptvDemo(client,(response)=>{
                    if(response.s == 1)
                    {
                        client.demo.active_date = response.active_date
                        client.demo.status = response.status

                        this.sendDemoCredentials(client)
                    }
                })
            })
        },
        sendDemoCredentials(client) {
            this.UserSupport.sendDemoCredentials(client,(response)=>{
                if(response.s == 1)
                {
                    
                }
            })
        },
        getClientIptvApi(client) {
            this.UserSupport.getClientIptvApi({user_name:client.user_name},(response)=>{
                if(response.s == 1)
                {
                    
                }
            })
        },
        sendServiceCredentials(client) {
            this.UserSupport.sendServiceCredentials(client,(response)=>{
                if(response.s == 1)
                {
                    
                }
            })
        },
        setAsRenovated(client) {
            this.UserSupport.setAsRenovated(client,(response)=>{
                if(response.s == 1)
                {
                    alertInfo({
                        icon:'<i class="bi bi-ui-checks"></i>',
                        message: `<div class="h3 text-white">Hemos enviado el mensaje de renovación</div>`,
                        size: 'modal-md',
                        _class:'bg-gradient-success text-white'
                    })
                    
                }
            })
        },
        addCredentials(client) {
            return new Promise((resolve) => {
                let alert = alertCtrl.create({
                    title: "Añadir credenciales",
                    subTitle: `<div class="mb-3 text-secondary fw-sembold text-xs">Ingresa las credenciales abajo</div>`,
                    inputs: [
                        {
                            type: 'text',
                            name: 'user_name',
                            placeholder: 'Usuario',
                            id: 'user_name',
                        },
                        {
                            type: 'text',
                            placeholder: 'Contraseña',
                            name: 'client_password',
                            id: 'client_password',
                        }
                    ],
                    buttons: [
                        {
                            text: "Añadir credenciales",
                            class: 'btn-success',
                            role: "cancel",
                            handler: (data) => {
                                this.UserSupport.addClientIptvCredentials({...data, client_id:client.client_id},(response)=>{
                                    if(response.s == 1)
                                    {
                                        resolve(data)
                                    }
                                })
                            },
                        },
                        {
                            text: "Cancelar",
                            role: "cancel",
                            handler: (data) => {
                                reject()
                            },
                        },
                    ],
                })

                alertCtrl.present(alert.modal);
            })
        },
        getAllIptvClients() {
            return new Promise((resolve,reject)=> {
                this.UserSupport.getAllIptvClients({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.clients)
                    }

                    reject()
                })
            })
        },
    },
    mounted() 
    {       
        this.getAllIptvClients().then((clients)=>{
            this.clients = clients
            this.clientsAux = clients
        }).catch(() => this.clients = false)
    },
    template : `
    <div
        v-if="clientsAux"
            class="card mb-4 overflow-hidden">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col fw-semibold text-dark">Lista de clientes</div>
                    <div class="col-auto"><span class="badge text-dark">Total {{clients.length}}</span></div>
                </div>
            </div>

            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <input type="search" class="form-control" v-model="query" placeholder="buscar por nombre o correo"/>
                    </div>
                    <div class="col-auto">
                        <select class="form-select" v-model="status" aria-label="Filtro">
                            <option v-bind:value="null">Todas</option>
                            <option v-for="_STATUS in STATUS" v-bind:value="_STATUS.code">
                                {{ _STATUS.text }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table table-striped table-borderless align-items-center mb-0">
                        <thead>
                            <tr class="font-bold text-center text-dark text-secondary text-uppercase opacity-7">
                                <th>ID</th>
                                <th>Franquicia</th>
                                <th>Cliente</th>
                                <th>Datos de acceso</th>
                                <th>Estatus</th>
                                <th>Información</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="client in clients">
                                <td class="align-middle text-center">
                                    {{client.client_id}}
                                </td>
                                <td class="text-center fw-sembold text-secondary text-xs">
                                    <div>{{client.user_login_id}}</div>
                                    <div>{{client.sponsor.names}}</div>
                                </td>
                                <td class="text-center fw-sembold text-secondary text-xs">
                                    <h6 class="">{{client.name}}</h6>
                                    <div>WhatsApp {{client.whatsapp}}</div>
                                    <div>{{client.email}}</div>
                                </td>
                                <td class="text-center">
                                    <div v-if="client.user_name" class="mb-3">Usuario <strong>{{client.user_name}}</strong> <button @click="copyToClipboard(client.user_name,$event)" class="btn ms-2 btn-sm mb-0 btn-outline-dark px-3">Copiar</button></div>
                                    <div v-if="client.client_password">Contraseña <strong>{{client.client_password}}</strong> <button @click="copyToClipboard(client.client_password,$event)" class="btn ms-2 btn-sm mb-0 btn-outline-dark px-3">Copiar</button></div>
                                </td>
                                <td class="text-center">
                                    <div v-if="client.demo">
                                        <div><span class="badge bg-primary me-2">DEMO</span></div>
                                        <div><span class="badge bg-danger me-2" v-if="client.demo.adult">Canales para adultos</span></div>

                                        <span class="badge" v-if="client.demo.status == STATUS_DEMO.DELETE.code" :class="STATUS_DEMO.DELETE._class">
                                            {{STATUS_DEMO.DELETE.text}}
                                        </span>
                                        <span class="badge" v-else-if="client.demo.status == STATUS_DEMO.FOR_ACTIVATE.code" :class="STATUS_DEMO.FOR_ACTIVATE._class">
                                            {{STATUS_DEMO.FOR_ACTIVATE.text}}
                                        </span>
                                        <span class="badge" v-else-if="client.demo.status == STATUS_DEMO.IN_USE.code" :class="STATUS_DEMO.IN_USE._class">
                                            {{STATUS_DEMO.IN_USE.text}}
                                        </span>
                                        <span class="badge" v-else-if="client.demo.status == STATUS_DEMO.EXPIRED.code" :class="STATUS_DEMO.EXPIRED._class">
                                            {{STATUS_DEMO.EXPIRED.text}}
                                        </span>
                                    </div>
                                    <div v-else-if="client.service">
                                        <div><span class="badge bg-primary me-2">SERVICIO</span></div>
                                        <div><span class="badge bg-danger me-2" v-if="client.service.adult">Canales para adultos</span></div>

                                        <span class="badge" v-if="client.service.status == STATUS_SERVICE.DELETE.code" :class="STATUS_SERVICE.DELETE._class">
                                            {{STATUS_SERVICE.DELETE.text}}
                                        </span>
                                        <span class="badge" v-else-if="client.service.status == STATUS_SERVICE.FOR_ACTIVATE.code" :class="STATUS_SERVICE.FOR_ACTIVATE._class">
                                            {{STATUS_SERVICE.FOR_ACTIVATE.text}}
                                        </span>
                                        <span class="badge" v-else-if="client.service.status == STATUS_SERVICE.IN_USE.code" :class="STATUS_SERVICE.IN_USE._class">
                                            {{STATUS_SERVICE.IN_USE.text}}
                                        </span>
                                        <span class="badge" v-else-if="client.service.status == STATUS_SERVICE.EXPIRED.code" :class="STATUS_SERVICE.EXPIRED._class">
                                            {{STATUS_SERVICE.EXPIRED.text}}
                                        </span>
                                    </div>
                                    <div v-else>
                                        <span class="badge bg-secondary">Sin información</span>
                                    </div>
                                </td>
                                <td class="text-center text-xs fw-semibold">
                                    <div v-if="client.demo">
                                        <span v-if="client.demo.left.active">
                                            <div class="text-left text-xs pb-2">Quedan {{client.demo.left.minutes}} minutos(s)</div>
                                            <div class="progress w-100">
                                                <div style="height:0.5rem":style="{width: client.demo.left.percentaje+'%'}" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </span>
                                        <span v-else class="badge bg-danger">
                                            Expirada
                                        </span>
                                    </div>
                                    <div v-else-if="client.service">
                                        <span v-if="client.service.left.active">
                                            <div class="text-left text-xs pb-2">Quedan {{client.service.left.days}} dias(s)</div>
                                            <div class="progress w-100">
                                                <div style="height:0.5rem":style="{width: client.service.left.percentaje+'%'}" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </span>
                                        <span v-else class="badge bg-danger">
                                            Expirada
                                        </span>
                                        <span v-if="client.service.request_renovation" class="badge bg-danger mt-3">
                                            Renovación pendiente
                                        </span>
                                    </div>
                                </td>
                                <td class="text-center text-xs fw-semibold">
                                    <div class="dropdown">
                                        <button class="btn btn-secondary px-3 shadow-none dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        
                                        </button>

                                        <ul class="dropdown-menu">
                                            <div v-if="client.service">
                                                <li v-if="client.service.status == STATUS_SERVICE.FOR_ACTIVATE.code">
                                                    <button @click="setUpIptvService(client)" class="dropdown-item">Añadir usuario y contraseña para servicio</button> 
                                                </li>
                                                <li v-else-if="client.service.status == STATUS_SERVICE.IN_USE.code">
                                                    <button @click="sendServiceCredentials(client)" class="dropdown-item">Reenviar credenciales</button> 
                                                    <button @click="getClientIptvApi(client)" class="dropdown-item">Ver datos de api</button> 
                                                </li>
                                                <li if="client.service.request_renovation">
                                                    <button @click="setAsRenovated(client)" class="dropdown-item">Marcar como renovado</button> 
                                                </li>
                                            </div>
                                            
                                            <div v-if="client.demo">
                                                <li v-if="client.demo.status == STATUS_DEMO.FOR_ACTIVATE.code">
                                                    <button @click="setUpIptvDemo(client)" class="dropdown-item">Añadir usuario y contraseña para demo</button> 
                                                </li>
                                                <li v-else-if="client.demo.status == STATUS_DEMO.IN_USE.code">
                                                    <button @click="sendDemoCredentials(client)" class="dropdown-item">Reenviar credenciales</button> 
                                                    <button @click="getClientIptvApi(client)" class="dropdown-item">Ver datos de api</button> 
                                                </li>
                                            </div>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-auto"><span class="badge bg-secondary">Cantidad de franquicias {{clients.length}}</span></div>
                </div>
            </div>
        </div>
        <div v-else-if="clients == false">
            <div class="alert alert-light text-center">
                <div>No tenemos clientes aún</div>
            </div>
        </div>
    `,
}

export { AdminiptvViewer } 