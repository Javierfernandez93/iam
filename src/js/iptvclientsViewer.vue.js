import { User } from '../../src/js/user.module.js?v=2.6.4'   

const IptvclientsViewer = {
    name : 'iptvclients-viewer',
    data() {
        return {
            User: new User,
            clients: null,
            credits: 0,
            clientsAux: null,
            feedBack: null,
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
            STATUS: {
                NOT_ENOUGH_CREDITS: {
                    _class: "alert-light text-center fw-semibold",
                    code: 0,
                    html: `<strong>Aviso importante</strong>. No tienes suficientes créditos`
                },
                CLIENT_EXIST: {
                    _class: "alert-light text-center fw-semibold",
                    code: 1,
                    html: `<strong>Aviso importante</strong>. El cliente ya exíste en tu cartera de clientes`
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
        sendCredentialsForWhatsApp(client) {
            window.open(client.whatsapp.sendWhatsApp(encodeURIComponent(`*¡Hola!* te enviamos tus datos de acceso a *DummieTrading*: \n\n Usuario : *${client.user_name}* \n Contraseña : *${client.client_password}*\n\nSi necesitas ayuda para el correcto funcionamiento de las apps por favor da clic en: https://zuum.link/AyudaDummieTrading`)))
        },
        requestRenovation(client) {
            this.feedBack = null

            if(this.credits > 0)
            {
                let alert = alertCtrl.create({
                    title: "Aviso",
                    subTitle: `<div class="h5">¿Estás seguro de renovar el servicio para <strong>${client.name}?</strong></div>`,
                    buttons: [
                        {
                            text: "Sí, pedir servicio",
                            class: 'btn-success',
                            role: "cancel",
                            handler: (data) => {
                                this.User.requestRenovation(client,(response)=>{
                                    if(response.s == 1)
                                    {
                                        client.demo = null
                                        client.service = {
                                            status : this.STATUS_SERVICE.FOR_ACTIVATE
                                        }

                                        this._getIptvCredits()

                                        alertInfo({
                                            icon:'<i class="bi bi-ui-checks"></i>',
                                            message: `<div class="h3 text-white">¡Gracias!</div> <div>Pronto la renovación de servicio a tu cliente <strong>${client.name}</strong></div>`,
                                            size: 'modal-md',
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
            } else {
                this.feedBack = this.STATUS.NOT_ENOUGH_CREDITS
            }
        },
        requestService(client) {
            this.feedBack = null

            if(this.credits > 0)
            {
                let alert = alertCtrl.create({
                    title: "Aviso",
                    subTitle: `<div class="h5">¿Estás seguro de pedir el servicio para <strong>${client.name}?</strong></div>`,
                    buttons: [
                        {
                            text: "Sí, pedir servicio",
                            class: 'btn-success',
                            role: "cancel",
                            handler: (data) => {
                                this.User.requestClientService(client,(response)=>{
                                    if(response.s == 1)
                                    {
                                        client.demo = null
                                        client.service = {
                                            status : this.STATUS_SERVICE.FOR_ACTIVATE
                                        }

                                        alertInfo({
                                            icon:'<i class="bi bi-ui-checks"></i>',
                                            message: `<div class="h3 text-white">¡Gracias!</div> <div>Pronto enviaremos las credenciales para tu cliente <strong>${client.name}</strong></div>`,
                                            size: 'modal-md',
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
            } else {
                this.feedBack = this.STATUS.NOT_ENOUGH_CREDITS
            }
        },
        copyToClipboard(client,event) {
            const text = `usuario ${client.user_name} contraseña ${client.client_password}`
            navigator.clipboard.writeText(text).then(() => {
                event.target.innerText = 'Copiado'
            });
        },
        getIptvClients() {
            return new Promise((resolve,reject)=> {
                this.User.getIptvClients({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.clients)
                    }

                    reject()
                })
            })
        },
        _getIptvCredits() {
            this.getIptvCredits().then((credits)=>{
                this.credits = credits
            }).catch(() => this.credits = 0)
        },
        getIptvCredits() {
            return new Promise((resolve,reject)=> {
                this.User.getIptvCredits({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.credits)
                    }

                    reject()
                })
            })
        },
    },
    mounted() 
    {       
        this._getIptvCredits()
        this.getIptvClients().then((clients)=>{
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
                    <div class="col-auto"><span class="badge text-dark">Creditos {{credits.numberFormat(2)}}</span></div>
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

            <div v-if="feedBack" class="alert"
                :class="feedBack._class">
                {{feedBack.text}}
            </div>

            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table table-striped table-borderless align-items-center mb-0">
                        <thead>
                            <tr class="font-bold text-center text-dark text-secondary text-uppercase opacity-7">
                                <th>ID</th>
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
                                <td class="text-center fw-semibold text-xs text-secondary">
                                    <h6 class="">{{client.name}}</h6>
                                    <div>{{client.whatsapp}}</div>
                                    <div>{{client.email}}</div>
                                </td>
                                <td class="text-center">
                                    <div  v-if="client.user_name && client.client_password" class="row align-items-center">
                                        <div class="col-12 col-xl">
                                            <div class="mb-1">Usuario <strong>{{client.user_name}}</strong></div>
                                            <div>Contraseña <strong>{{client.client_password}}</strong></div>
                                        </div>
                                        <div class="col-12 col-xl-auto">
                                            <button @click="copyToClipboard(client,$event)" class="btn px-3 btn-sm mb-0 btn-outline-dark px-3">Copiar</button>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div v-if="client.demo">
                                        <div><span class="badge bg-primary me-2">DEMO</span></div>
                                        <div><span class="badge bg-danger me-2" v-if="client.demo.adult">Canales +18</span></div>

                                        <div>
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
                                    </div>
                                    <div v-else-if="client.service">
                                        <div><span class="badge bg-danger me-2" v-if="client.service.adult">Canales +18</span></div>
                                        <div><span class="badge bg-primary me-2">SERVICIO</span></div>

                                        <div>
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
                                    </div>
                                    <div v-else>
                                        <span class="badge bg-secondary">Sin información</span>
                                    </div>
                                </td>
                                <td class="text-center text-xs fw-semibold">
                                    <div v-if="client.demo">
                                        <span v-if="client.demo.status != STATUS_DEMO.FOR_ACTIVATE.code">
                                            <span v-if="client.demo.left">
                                                <span v-if="client.demo.left.active">
                                                    <div class="text-left text-xs pb-2">Quedan {{client.demo.left.minutes}} minutos(s)</div>
                                                    <div class="progress w-100">
                                                        <div style="height:0.5rem":style="{width: client.demo.left.percentaje+'%'}" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </span>
                                                <span v-else class="badge bg-danger">
                                                    Expirada
                                                </span>
                                            </span>
                                        </span>
                                    </div>
                                    <div v-else-if="client.service">
                                        <span v-if="client.service.status != STATUS_SERVICE.FOR_ACTIVATE.code">
                                            <span v-if="client.service.left">
                                                <span v-if="client.service.left.active">
                                                    <div class="text-left text-xs pb-2">Quedan {{client.service.left.days}} dias(s)</div>
                                                    <div class="progress w-100">
                                                        <div style="height:0.5rem":style="{width: client.service.left.percentaje+'%'}" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </span>
                                                <span v-else class="badge bg-danger">
                                                    Expirada
                                                </span>
                                            </span>
                                        </span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div v-if="client.service">
                                        <div v-if="client.service.status == STATUS_SERVICE.EXPIRED.code">
                                            <button :disabled="client.service.request_renovation" @click="requestRenovation(client)" class="btn btn-danger btn-sm shadow-none">Renovar suscripción</button>
                                        </div>
                                    </div>
                                    
                                    <div v-if="client.demo"><button :disabled="credits == 0" @click="requestService(client)" class="btn shadow-none shadow-nombre btn-sm px-3 btn-primary">Pedir servicio</button></div>
                                    <div v-if="client.user_name && client.client_password">
                                        <button @click="sendCredentialsForWhatsApp(client)" class="btn btn-sm px-3 btn-success mb-0 shadow-none">Enviar datos por WhatsApp</button>
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
                <div>Comienza a generar clientes para tu negocio</div>
                <div class="fw-semibold fs-5">Puedes comenzar entrando a nuestra educación <a class="text-white" href="../../apps/academy/"><u>Ver educación</u></a></div>
            </div>
        </div>
    `,
}

export { IptvclientsViewer } 