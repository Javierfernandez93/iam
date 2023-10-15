import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.4'

const AdminexerciseViewer = {
    name : 'adminexercise-viewer',
    data() {
        return {
            UserSupport: new UserSupport,
            users: null,
            usersAux: null,
            currentUser: null,
            query: null,
            status : null,
            STATUS : {
                WAITING: {
                    status: 0,
                    text: 'Esperando credenciales',
                },
                IN_PROGRESS: {
                    status: 1,
                    text: 'En progreso',
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
                    text: 'Reprobado',
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
        getExercises() {
            return new Promise((resolve,reject) => {
                this.UserSupport.getExercises({status:this.status.status,query:this.query}, (response) => {
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
                title: `Aprobar a a <b>${user.names}</b>`,
                subTitle: `<div class="mb-3">¿Estás seguro de aprobar la cuenta demo de <b>${user.names}</b>?. Envíaremos un Whatsapp con los datos que ingreses a</div>`,
                size: 'modal-fullscreen',
                inputs: [
                    {
                        type: 'text',
                        name: 'user_name',
                        id: 'user_name',
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
                        text: 'Aceptar',
                        handler: data => {
                            data.status = this.STATUS.FINISH.status

                            this.UserSupport.approbeExercise({...user, ...data}, (response) => {
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
        setExerciseAs(user,status) {   
            const alert = alertCtrl.create({
                title: `Cambiar de estado a ${this.getStatusText(status)}`,
                subTitle: `¿Estás seguro de cambiar el estado de <b>${user.names}</b>?`,
                buttons: [
                    { 
                        text: 'Aceptar',
                        handler: data => {
                            this.UserSupport.setExerciseAs({exercise_id : user.exercise_id,status : status}, (response) => {
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
        sendWhatsAppExerciseCredentials(user,status) {   
            const alert = alertCtrl.create({
                title: `Reenviar credenciales`,
                subTitle: `¿Estás seguro de reenviar credenciales de <b>${user.names}</b>?`,
                buttons: [
                    { 
                        text: 'Sí, reenviar',
                        handler: data => {
                            this.UserSupport.sendWhatsAppExerciseCredentials(user, (response) => {
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
        setExerciseCredentials(user) {
            const alert = alertCtrl.create({
                title: `Añade las credenciales <b>${user.names}</b>`,
                subTitle: `<div class="mb-3">Ingresa los datos para <b>${user.names}</b> al ingresar sus credenciales su cuenta demo y sus ejercicios comenzarán a correr.</div>`,
                size: 'modal-fullscreen',
                inputs: [
                    {
                        id: 'user_name',
                        placeholder: 'Login',
                        name: 'user_name',
                        type: 'text'
                    },
                    {
                        id: 'password',
                        placeholder: 'Password',
                        name: 'password',
                        type: 'text'
                    },
                    {
                        id: 'trader',
                        placeholder: 'Trader',
                        name: 'trader',
                        type: 'text'
                    },
                    {
                        type: 'text',
                        name: 'server',
                        id: 'server',
                        placeholder: 'Servidor',
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
                            if(data.user_name)
                            {
                                if(data.password)
                                {
                                    if(data.server)
                                    {
                                        if(data.server)
                                        {
                                            this.UserSupport.setExerciseCredentials({...user, ...data}, (response) => {
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
                                            alertHtml('Debes de ingresar un trader válido');
                                        }
                                    } else {
                                        alertHtml('Debes de ingresar un server válido');
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
            this.getExercises().then((users) => {
                this.users = users
                this.usersAux = users
            }).catch(() => this.users = false)
        },
    },
    mounted() {
        this.status = this.STATUS.WAITING
    },
    template : `
        <div class="card mb-3">
            <div class="input-group input-group-lg input-group-merge">
                <input
                    v-model="query"
                    :autofocus="true"
                    @keydown.enter.exact.prevent="search"
                    type="text" class="form-control border-0 shadow-lg" placeholder="Buscar usuario..."/>

                <select class="form-select border-0" v-model="status" aria-label="Estatus">
                    <option v-for="_status in STATUS" v-bind:value="_status">
                        {{ _status.text }}
                    </option>
                </select>

                <button @click="search" class="btn btn-light mb-0"><i class="bi bi-search"></i></button>
            </div>
        </div>

        <div v-if="users">
            <div class="card">
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsives p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Usuario</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Fecha de creación</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ejercicios</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Progreso</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Estatus</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Opciones</th>
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
                                    <td class="align-middle text-center text-sm">{{user.create_date.formatFullDate()}}</td>
                                    <td class="align-middle text-center text-sm">
                                        {{user.tests.length}}
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        {{user.progress}}%
                                    </td>
                                    <td class="align-middle text-center text-xs">
                                        <span class="badge bg-secondary" v-if="user.status == STATUS.WAITING.status">
                                            Esperando cuenta demo
                                        </span>
                                        <span class="badge bg-primary" v-if="user.status == STATUS.IN_PROGRESS.status">
                                            En progreso
                                        </span>
                                        <span class="badge bg-success" v-if="user.status == STATUS.FINISH.status">
                                            Aprobado
                                        </span>
                                        <span class="badge bg-warning" v-if="user.status == STATUS.EXPIRED.status">
                                            Expirado
                                        </span>
                                        <span class="badge bg-danger" v-if="user.status == STATUS.DECLINED.status">
                                            Reprobado
                                        </span>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <div v-if="user.status != STATUS.DECLINED.status" class="dropdown dropstart">
                                            <button class="btn btn-outline-primary btn-sm px-3 mb-0 shadow-none dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            
                                            </button>

                                            <ul class="dropdown-menu">
                                                <li v-if="user.status == STATUS.IN_PROGRESS.status">
                                                    <button  
                                                            @click="sendWhatsAppExerciseCredentials(user)"
                                                            class="dropdown-item">Enviar datos de cuenta demo por WS</button>
                                                </li>
                                                <li v-if="user.status == STATUS.IN_PROGRESS.status">
                                                    <button  
                                                        @click="setExerciseAs(user,STATUS.DECLINED.status)"
                                                        class="dropdown-item">Reprobar</button>
                                                </li>
                                                <li v-if="user.status == STATUS.IN_PROGRESS.status">
                                                    <button  
                                                        @click="approbeExercise(user)"
                                                        class="dropdown-item">Aprobar</button>
                                                </li>
                                                <li v-if="user.status == STATUS.WAITING.status">
                                                    <button  
                                                        @click="setExerciseCredentials(user)"
                                                        class="dropdown-item">Añadir credenciales cuenta demo</button>
                                                </li>
                                                <li v-if="user.status == STATUS.WAITING.status">
                                                    <button  
                                                        @click="viewConfiguration(user)"
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
            </div>
        </div>
        <div v-else-if="users == false" class="alert alert-light fw-semibold text-center">    
            No tenemos usuarios con ese filtro
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

export { AdminexerciseViewer } 