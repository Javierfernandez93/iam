import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.6'

const AdmingainsViewer = {
    name : 'admingains-viewer',
    data() {
        return {
            UserSupport: new UserSupport,
            users: null,
            usersAux: null,
            currentUser: null,
            query: null,
            status : null,
            total: { 
                gains : 0 
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
        getAdminGainsTradingAccounts() {
            return new Promise((resolve,reject) => {
                this.UserSupport.getAdminGainsTradingAccounts({status:1}, (response) => {
                    if (response.s == 1) {
                        resolve(response.users)
                    }

                    reject()
                })
            })
        },
        getTotals() {
            this.users.map((user) => {
                if(user.hasGain)
                {
                    this.total.gains += parseFloat(user.gain)
                }
            })
        },
        addProfit(user) {
            const alert = alertCtrl.create({
                title: `Añade el profit para <b>${user.names}</b>`,
                subTitle: `<div class="mb-3">Ingresa la cantidad en USD.</div>`,
                size: 'modal-md',
                inputs: [
                    {
                        id: 'amount',
                        placeholder: 'Monto en USD',
                        name: 'amount',
                        type: 'number'
                    },
                ],
                buttons: [
                    { 
                        text: 'Enviar ganancias',
                        handler: data => {
                            if(data.amount)
                            {
                                this.UserSupport.addProfit({...user, ...data}, (response) => {
                                    if (response.s == 1) {
                                        user.gain = data.amount
                                        user.hasGain = true
                                        
                                        alertInfo({
                                            icon:'<i class="bi bi-ui-checks"></i>',
                                            message: `Hemos enviado el profit al usuario <b>${user.names}</b>`,
                                            _class:'bg-gradient-success text-white'
                                        })

                                        this.getTotals()
                                    } else if(response.r == 'INVALID_PERMISSION') {
                                        alertHtml('No tienes permisos necesarios para hacer esta acción. <strong>El incidente será reportado.</strong>')
                                    }
                                })
                            } else {
                                alertHtml('Debes de ingresar un monto válido');
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
    },
    mounted() {
        this.getAdminGainsTradingAccounts().then((users) => {
            this.users = users
            this.usersAux = users
            this.getTotals()
        }).catch(() => this.users = false)
    },
    template : `

        <div v-if="users">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-12 col-xl">
                            <span class="badge text-secondary p-0">{{users.lenght}}</span>
                            <div class="fs-4 text-primary fw-semibold">
                                Ganancias de usuarios 
                            </div>
                        </div>
                        <div class="col-12 col-xl-auto">
                            <div class="input-group input-group-lg input-group-merge">
                                <input
                                    v-model="query"
                                    :autofocus="true"
                                    @keydown.enter.exact.prevent="search"
                                    type="text" class="form-control" placeholder="Buscar usuario..."/>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table table-striped align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Usuario</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ganancia</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(user,index) in users">
                                    <td class="align-middle text-center">
                                        {{index+1}}
                                    </td>
                                    <td class="align-middle text-center">
                                       {{user.user_login_id}}
                                    </td>
                                    <td class="align-middle text-center text-capitalize text-dark fw-semibold">
                                       {{user.names}}
                                    </td>
                                    <td class="align-middle text-center">
                                        <span v-if="user.hasGain" class="text-success fw-semibold">
                                            $ {{user.gain.numberFormat(2)}} USD 
                                        </span>
                                        <span v-else>
                                            -
                                        </span>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <div v-if="!user.hasGain" class="dropdown">
                                            <button class="btn btn-primary btn-sm px-3 mb-0 shadow-none dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            
                                            </button>

                                            <ul class="dropdown-menu">
                                                <li>
                                                    <button  
                                                        @click="addProfit(user)"
                                                        class="dropdown-item">Añadir ganancia</button>
                                                </li>
                                            </ul>
                                        </div>
                                        <div v-else>
                                            -
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="align-middle text-success fw-semibold text-center">
                                       $ {{total.gains.numberFormat(2)}} USD 
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div v-else-if="users == false" class="alert alert-light fw-semibold text-center">    
            No tenemos usuarios con ese filtro
        </div>
    `,
}

export { AdmingainsViewer } 