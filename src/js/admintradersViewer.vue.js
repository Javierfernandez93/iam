import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.4'

const AdmintradersViewer = {
    name : 'admintraders-viewer',
    data() {
        return {
            UserSupport: new UserSupport,
            users: null,
            usersAux: null,
            currentUser: null,
            kind: 1,
            query: null,
            status : null,
            KINDS : {
                DEMO: {
                    status: 1,
                    text: 'demo',
                },
                LIVE: {
                    status: 0,
                    text: 'live',
                },
            },
            STATUS : {
                WAITING: {
                    status: 0,
                    text: 'Esperando credenciales',
                },
                IN_PROGRESS: {
                    status: 1,
                    text: 'Activo',
                },
                FINISH: {
                    status: 2,
                    text: 'Aprobado',
                },
                EXPIRED: {
                    status: 3,
                    text: 'Expirada',
                },
                DECLINED: {
                    status: 4,
                    text: 'Eliminado',
                },
            },
            columns: { // 0 DESC , 1 ASC 
                company_id: {
                    name: 'company_id',
                    desc: false,
                },
                signup_date: {
                    name: 'signup_date',
                    desc: false,
                },
                names: {
                    name: 'names',
                    desc: false,
                    alphabetically: true,
                },
            },
        }
    },
    watch : {
        status : {
            handler() {
                this.search()
            },
            deep: true
        },
        kind : {
            handler() {
                this.search()
            },
            deep: true
        },
        query : {
            handler() {
                this.filterData()
            },
            deep: true
        }
    },
    methods: {
        filterData() {
            this.users = this.usersAux

            this.users = this.users.filter((user) => {
                return user.names.toLowerCase().includes(this.query.toLowerCase()) 
            })
        },
        getStatusText(status) {
            let statusText = null

            Object.keys(this.STATUS).forEach((key, index) => {
                let _status = this.STATUS[key]
                if(_status.status == status) {
                    statusText = _status.text
                }
            })

            return statusText
        },
        goToAddRulesUserTrading(user_trading_account_id) {
            window.location.href = `../../apps/admin-users/rules?utaid=${user_trading_account_id}`
        },
        getAdminTradingAccounts() {
            return new Promise((resolve,reject) => {
                this.UserSupport.getAdminTradingAccounts({status:this.status.status,query:this.query,demo:this.kind}, (response) => {
                    if (response.s == 1) {
                        resolve(response.users)
                    }

                    reject()
                })
            })
        },
        copyToClipboard(text,target) {   
            navigator.clipboard.writeText(text).then(() => {
                target.innerText = 'Copiado'
            })
        },
        approbeExercise(user,status) {   
            const alert = alertCtrl.create({
                title: `Aprobar a a ${user.names}`,
                subTitle: `¿Estás seguro de cambiar el estado de <b>${user.names}</b>?`,
                size: 'modal-md',
                inputs: [
                    {
                        type: 'text',
                        name: 'login',
                        id: 'login',
                        placeholder: 'Nombre de usuario',
                    },
                    {
                        type: 'text',
                        name: 'password',
                        id: 'password',
                        placeholder: 'Contraseña',
                    },
                    {
                        type: 'text',
                        name: 'url',
                        id: 'url',
                        placeholder: 'URL',
                    }
                ],
                buttons: [
                    { 
                        text: 'Aceptar',
                        handler: data => {
                            data.user_login_id = user.user_login_id
                            data.exercise_id = user.exercise_id
                            data.status = this.STATUS.FINISH.status

                            this.UserSupport.approbeExercise(data, (response) => {
                                if (response.s == 1) {
                                    user.status = response.status

                                    alertInfo({
                                        icon:'<i class="bi bi-ui-checks"></i>',
                                        message: `Hemos aprobado al usuario y creado su cuenta real. Puedes visualizar la lista de usuarios en el apartado <a href="../../apps/admin-users/trading">Usuarios > Trading</a>`,
                                        _class:'bg-gradient-success text-white'
                                    })
                                } else if(response.r == 'INVALID_PERMISSION') {
                                    alertHtml('No tienes permisos necesarios para hacer esta acción. <strong>El incidente será reportado.</strong>')
                                }

                                alert.modal.dismiss();
                            })
                        }              
                    },
                    {
                        text: 'Cancelar',
                        role: 'cancel', 
                        handler: data => {
                        }
                    },  
                ]
            });
          
            alertCtrl.present(alert.modal);
        },
        setTraddingAccountAs(user,status) {   
            const alert = alertCtrl.create({
                title: `Cambiar de estado a ${this.getStatusText(status)}`,
                subTitle: `¿Estás seguro de cambiar el estado de <b>${user.names}</b>?`,
                buttons: [
                    { 
                        text: 'Aceptar',
                        handler: data => {
                            this.UserSupport.setTraddingAccountAs({user_trading_account_id : user.user_trading_account_id, status : status}, (response) => {
                                if (response.s == 1) {
                                    user.status = status
                                } else if(response.r == 'INVALID_PERMISSION') {
                                    alertHtml('No tienes permisos necesarios para hacer esta acción. <strong>El incidente será reportado.</strong>')
                                }

                                alert.modal.dismiss();
                            })
                        }              
                    },
                    {
                        text: 'Cancelar',
                        role: 'cancel', 
                        handler: data => {
                        }
                    },  
                ]
            });
          
            alertCtrl.present(alert.modal);
        },
        sendWhatsAppUserTradingCredentials(user,status) {   
            const alert = alertCtrl.create({
                title: `Reenviar credenciales`,
                subTitle: `¿Estás seguro de reenviar credenciales de <b>${user.names}</b>?`,
                buttons: [
                    { 
                        text: 'Sí, reenviar',
                        handler: data => {
                            this.UserSupport.sendWhatsAppUserTradingCredentials(user, (response) => {
                                if (response.s == 1) {
                                    alertInfo({
                                        icon:'<i class="bi bi-ui-checks"></i>',
                                        message: `Hemos reenviado sus credenciales por WhatsApp`,
                                        _class:'bg-gradient-success text-white'
                                    })
                                } else if(response.r == 'INVALID_PERMISSION') {
                                    alertHtml('No tienes permisos necesarios para hacer esta acción. <strong>El incidente será reportado.</strong>')
                                }

                                alert.modal.dismiss();
                            })
                        }              
                    },
                    {
                        text: 'Cancelar',
                        role: 'cancel', 
                        handler: data => {
                        }
                    },  
                ]
            });
          
            alertCtrl.present(alert.modal);
        },
        setUserTradingCredentials(user) {
            const initial_balance = user.package_acelerated != null ? user.package_acelerated.initial_balance : 0
            const initial_drawdown = user.package_acelerated != null ? user.package_acelerated.initial_drawdown : 0
            console.log(user)
            const alert = alertCtrl.create({
                title: `Añade las credenciales <b>${user.names}</b>`,
                subTitle: `<div class="mb-3">Ingresa los datos para <b>${user.names}</b> al ingresar sus credenciales su cuenta.</div>`,
                size: 'modal-fullscreen',
                inputs: [
                    {
                        type: 'text',
                        name: 'login',
                        id: 'login',
                        placeholder: 'Login',
                    },
                    {
                        type: 'text',
                        name: 'password',
                        id: 'password',
                        placeholder: 'Contraseña',
                    },
                    {
                        type: 'text',
                        name: 'trader',
                        id: 'trader',
                        placeholder: 'Trader',
                    },
                    {
                        type: 'text',
                        name: 'server',
                        id: 'server',
                        placeholder: 'Servidor',
                    },
                    {
                        type: 'number',
                        name: 'initial_drawdown',
                        id: 'initial_drawdown',
                        value: initial_drawdown,
                        placeholder: 'Drawdown MAX',
                    },
                    {
                        type: 'number',
                        name: 'initial_balance',
                        id: 'initial_balance',
                        value : initial_balance,
                        placeholder: 'Balance inicial',
                    },
                    {
                        id: 'sendWhatsApp',
                        text: 'Enviar WhatsApp',
                        value: 1,
                        name: 'sendWhatsApp',
                        type: 'checkbox',
                        checked: false
                    },
                    {
                        id: 'sendEmail',
                        text: 'Enviar correo electrónico',
                        value: 1,
                        name: 'sendEmail',
                        type: 'checkbox',
                        checked: false
                    }
                ],
                buttons: [
                    { 
                        text: 'Añadir credenciales',
                        handler: data => {
                            if(data.login)
                            {
                                if(data.password)
                                {
                                    if(data.server)
                                    {
                                        data.status = this.STATUS.FINISH.status

                                        this.UserSupport.setUserTradingCredentials({...user, ...data}, (response) => {
                                            if (response.s == 1) {
                                                user = {...user,...data}
                                                user.status = this.STATUS.IN_PROGRESS.status
                                                
                                                alertInfo({
                                                    icon:'<i class="bi bi-ui-checks"></i>',
                                                    message: `Hemos dado de alta el usuario y envíado sus credenciales por WhatsApp`,
                                                    _class:'bg-gradient-success text-white'
                                                })
                                            } else if(response.r == 'INVALID_PERMISSION') {
                                                alertHtml('No tienes permisos necesarios para hacer esta acción. <strong>El incidente será reportado.</strong>')
                                            }
                                        })
                                    } else {
                                        alertHtml('Debes de ingresar una URL válida');
                                    }
                                } else {
                                    alertHtml('Debes de ingresar una contraseña');
                                }
                            } else {
                                alertHtml('Debes de ingresar un nombre de usuario');
                            }
                        }              
                    },
                    {
                        text: 'Cancelar',
                        role: 'cancel', 
                        handler: data => {
                        }
                    },  
                ]
            });
          
            alertCtrl.present(alert.modal);
        },
        deleteBuyByAdmin(buy) {
            const alert = alertCtrl.create({
                title: `¿Deseas eliminar éste pago?`,
                subTitle: `Eliminar orden #${user.invoice_id}`,
                buttons: [
                    { 
                        text: 'Aceptar',
                        handler: data => {
                            this.UserSupport.deleteBuyByAdmin({invoice_id:user.invoice_id}, (response) => {
                                if (response.s == 1) {
                                    user.status = this.STATUS.DELETED.status
                                } else if(response.r == 'INVALID_PERMISSION') {
                                    alertHtml('No tienes permisos necesarios para hacer esta acción. <strong>El incidente será reportado.</strong>')
                                }

                                alert.modal.dismiss();
                            })
                        }              
                    },
                    {
                        text: 'Cancelar',
                        role: 'cancel', 
                        handler: data => {
                        }
                    },  
                ]
            });
          
            alertCtrl.present(alert.modal);
        },
        viewConfiguration(user) {
            this.currentUser = user

            $(this.$refs.modal).modal('show')
        },
        setBuyAsPendingByAdmin(buy) {
            const alert = alertCtrl.create({
                title: `¿Deseas cambiar a pendiente éste pago?`,
                subTitle: `Orden #${user.invoice_id}`,
                buttons: [
                    { 
                        text: 'Aceptar',
                        handler: data => {
                            this.UserSupport.setBuyAsPendingByAdmin({invoice_id:user.invoice_id}, (response) => {
                                if (response.s == 1) {
                                    user.status = this.STATUS.PENDING.status
                                } else if(response.r == 'INVALID_PERMISSION') {
                                    alertHtml('No tienes permisos necesarios para hacer esta acción. <strong>El incidente será reportado.</strong>')
                                }

                                alert.modal.dismiss();
                            })
                        }              
                    },
                    {
                        text: 'Cancelar',
                        role: 'cancel', 
                        handler: data => {
                        }
                    },  
                ]
            });
          
            alertCtrl.present(alert.modal);
        },
        search() {
            this.getAdminTradingAccounts().then((users) => {
                this.users = users
                this.usersAux = users
            }).catch(() => this.users = false)
        },
    },
    mounted() {
        this.status = this.STATUS.WAITING
    },
    template : `
        <div>
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-12 col-xl">
                            <span v-if="users" class="badge text-secondary p-0">Total {{users.length}}</span>
                            <div class="fs-4 fw-semibold text-primary">Lista de traders <span v-text="kind ? 'Demo':'Live'"></span> </div>
                        </div>
                        <div class="col-12 col-xl-7">
                            <div class="input-group input-group-lg input-group-merge">
                                <input
                                    v-model="query"
                                    :autofocus="true"
                                    @keydown.enter.exact.prevent="search"
                                    type="text" class="form-control" placeholder="Buscar usuario..."/>
                
                                <select class="form-select" v-model="status" aria-label="Estatus">
                                    <option v-for="_status in STATUS" v-bind:value="_status">
                                        {{ _status.text }}
                                    </option>
                                </select>
                
                                <select class="form-select" v-model="kind" aria-label="Estatus">
                                    <option v-for="_kind in KINDS" v-bind:value="_kind.status">
                                        {{ _kind.text }}
                                    </option>
                                </select>

                                <button @click="search" class="btn mb-0 shadow-none btn-primary"><i class="bi bi-bootstrap-reboot"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="users">
                    <div class="table-responsive p-0">
                        <table class="table table-striped align-items-center mb-0">
                            <thead>
                                <tr class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    <th>#</th>
                                    <th>Usuario</th>
                                    <th>Login</th>
                                    <th>Password</th>
                                    <th>Server</th>
                                    <th>DD</th>
                                    <th>Balance</th>
                                    <th>Fecha ingreso</th>
                                    <th>Estatus</th>
                                    <th>Acelerada</th>
                                    <th>Demo</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(user,index) in users">
                                    <td class="align-middle text-center text-xs">
                                        {{index+1}}
                                    </td>
                                    <td class="align-middle text-center text-xs">
                                       {{user.names}}
                                    </td>
                                    <td class="align-middle text-center text-xs">
                                       {{user.login}}
                                    </td>
                                    <td class="align-middle text-center text-xs">
                                       {{user.password}}
                                    </td>
                                    <td class="align-middle text-center text-xs">
                                       {{user.server}}
                                    </td>
                                    <td class="align-middle text-center text-xs">
                                       {{user.drawdown}}
                                    </td>
                                    <td class="align-middle text-center text-xs">
                                       {{user.balance.numberFormat()}}
                                    </td>
                                    <td class="align-middle text-center text-sm">{{user.create_date.formatDate()}}</td>
                                    <td class="align-middle text-center text-xs">
                                        <span class="text-secondary" v-if="user.status == STATUS.WAITING.status">
                                            Esperando cuenta real
                                        </span>
                                        <span class="text-primary" v-if="user.status == STATUS.IN_PROGRESS.status">
                                            Activo
                                        </span>
                                        <span class="text-success" v-if="user.status == STATUS.FINISH.status">
                                            Aprobado
                                        </span>
                                        <span class="text-warning" v-if="user.status == STATUS.EXPIRED.status">
                                            Expirado
                                        </span>
                                        <span class="text-danger" v-if="user.status == STATUS.DECLINED.status">
                                            Eliminado
                                        </span>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span v-if="user.acelerated" class="badge bg-success">
                                            {{user.package_acelerated.title}}
                                        </span>
                                        <span v-else class="badge bg-warning">
                                            No
                                        </span>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span v-if="user.demo" class="badge bg-success">
                                            Sí 
                                        </span>
                                        <span v-else class="badge bg-warning">
                                            No
                                        </span>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <div class="dropdown">
                                            <button class="btn btn-primary btn-sm px-3 mb-0 shadow-none dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            
                                            </button>

                                            <ul class="dropdown-menu">
                                                <li v-if="user.status == STATUS.IN_PROGRESS.status">
                                                    <button  
                                                        @click="sendWhatsAppUserTradingCredentials(user)"
                                                        class="dropdown-item">Enviar cuenta real por WhatsApp</button>
                                                </li>
                                                <li v-if="user.status == STATUS.WAITING.status || user.status == STATUS.IN_PROGRESS.status">
                                                    <button  
                                                        @click="setTraddingAccountAs(user,STATUS.DECLINED.status)"
                                                        class="dropdown-item">Eliminar cuenta</button>
                                                </li>
                                                <li v-if="user.status == STATUS.DECLINED.status">
                                                    <button  
                                                        @click="setTraddingAccountAs(user,STATUS.IN_PROGRESS.status)"
                                                        class="dropdown-item">Habilitar</button>
                                                </li>
                                                <li v-if="user.status == STATUS.WAITING.status">
                                                    <button  
                                                        @click="setUserTradingCredentials(user)"
                                                        class="dropdown-item">Añadir credenciales cuenta real</button>
                                                </li>
                                                <li v-if="user.status == STATUS.WAITING.status && !user.acelerated">
                                                    <button  
                                                        @click="viewConfiguration(user)"
                                                        class="dropdown-item">Ver configuración</button>
                                                </li>

                                                <li v-if="user.status == STATUS.IN_PROGRESS.status">
                                                    <button  
                                                        @click="goToAddRulesUserTrading(user.user_trading_account_id)"
                                                        class="dropdown-item">Ver configuración</button>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div v-else-if="users == false" class="card-body">    
                    <div class="alert alert-light fw-semibold text-center">    
                        No tenemos traders con ese filtro
                    </div>
                </div>
            </div>
        </div>

    
        <div class="modal fade" ref="modal" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Configuración de la cuenta demo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div v-if="currentUser" class="modal-body">
                        <div class="row align-items-center vh-100 justify-content-center">
                            <div class="col-10">
                                <div class="alert mb-3 text-white fw-semibold alert-info">
                                    <strong>Aviso</strong>
                                    Puedes copiar la información dando clic en el botón a un lado del valor de la configuración
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <ul class="list-group list-group-flush">
                                            <li v-for="configuration in currentUser.configuration" class="list-group-item">
                                                <div class="row">
                                                    <div class="col">
                                                        <h3>{{configuration.description}}</h3>
                                                    </div>
                                                    <div class="col-auto">
                                                        <h3>{{configuration.value}}</h3>
                                                    </div>
                                                    <div class="col-auto">
                                                        <button class="btn btn-sm mb-0 shadow-none px-3 btn-primary" @click="copyToClipboard(configuration.value,$event.target)">Copiar</button>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { AdmintradersViewer } 