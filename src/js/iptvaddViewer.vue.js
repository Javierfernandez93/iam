import { User } from '../../src/js/user.module.js?v=2.6.6'   

const IptvaddViewer = {
    name : 'iptvadd-viewer',
    data() {
        return {
            User: new User,
            isClientFilled: false,
            feedBack: null,
            loading: false,
            client: {
                name : null,
                whatsapp : '',
                email : '',
                demo: {
                    enabled: false
                },
                service: {
                    connection: 1,
                    month: 1,
                    adult : true,
                }
            },
            credits: 0,
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
            }
        }
    },
    watch: {
        client: {
            handler() {
                this.isClientFilled = this.client.name != null && this.client.email.isValidMail() && this.client.whatsapp.isValidPhone()
            },
            deep: true
        },
    },
    methods: {
        addClient() {
            this.feedBack = null
            this.loading = true

            if(this.credits > 0 || this.client.demo.enabled)
            {
                this.User.addClient(this.client, (response) => {
                    this.loading = false
                    if (response.s == 1) {
                        alertInfo({
                            icon:'<i class="bi bi-ui-checks"></i>',
                            message: `<div class="h3 text-white">¡Felicidades!</div> <div>Hemos dado de alta tu cliente <strong>${response.name}</strong></div> <div>Daremos de alta tu cliente y tendrá acceso a la brevedad.</div> <div class="mt-2 fw-semibold">Podrás ver su contraseña y opciones para ver su transmisión en el menú IPTV > Mis clientes</div>`,
                            size: 'modal-md',
                            _class:'bg-gradient-success text-white'
                        },500)
                        
                        this._getIptvCredits()
                        
                        this.client = {
                            name : null,
                            email : '',
                            whatsapp : '',
                            demo: {
                                enabled: false
                            },
                            service: {
                                connection: 1,
                                month: 1,
                                adult : true,
                            }
                        }
                    } else if(response.r == "NOT_SAVE") {
                        this.feedBack = this.STATUS.CLIENT_EXIST
                    }
                })
            } else {
                this.feedBack = this.STATUS.NOT_ENOUGH_CREDITS
            }
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
    },
    template : `
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-12 col-xl">
                                    <div class="fs-4 fw-sembold">Alta cliente</div>
                                    <div class="text-xs text-secondary">(* campos requerídos)</div>
                                </div>
                                <div class="col-12 col-xl-auto text-end">
                                <div class="text-xs text-secondary">Créditos</div>
                                    <div class="h3 fw-sembold">{{credits.numberFormat(2)}}</div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-12 mb-3">
                                    <div class="form-floating">
                                        <input :autofocus="true" v-model="client.name" :class="client.name ? 'is-valid' : ''" @keypress.enter.exact="$refs.email.focus()" type="text" class="form-control" id="name" placeholder="name@example.com">
                                        <label for="anme">* Nombre</label>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="form-floating">
                                        <input v-model="client.email" :class="client.email.isValidMail() ? 'is-valid' : ''" @keypress.enter.exact="$refs.whatsapp.focus()" ref="email" type="email" class="form-control" id="email" placeholder="Password">
                                        <label for="email">* Correo electrónico</label>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="form-floating">
                                        <input v-model="client.whatsapp" :class="client.whatsapp.isValidPhone() ? 'is-valid' : ''" @keypress.enter.exact="$refs.adult.focus()" ref="whatsapp" type="whatsapp" class="form-control" id="whatsapp" placeholder="Password">
                                        <label for="whatsapp">* WhatsApp (COMPLETO)</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" ref="demo" v-model="client.demo.enabled" type="checkbox" id="demo">
                                        <label class="form-check-label" for="demo">Demo</label>
                                    </div>
                                </div>
                                <div v-if="client.demo.enabled" class="alert text-center text-white border-0 alert-success">
                                    <strong>Aviso</strong>
                                    Enviaremos la demo a tu cliente de inmediato
                                </div>
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" ref="adult" v-model="client.adult" type="checkbox" id="adult">
                                        <label class="form-check-label" for="adult">Canales para adultos</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="feedBack" class="card-footer">
                            <div class="alert"
                                :class="feedBack._class">
                                <span v-html="feedBack.html"></span>
                            </div>
                        </div>
                        <div class="card-footer d-grid">
                            <button :disabled="!isClientFilled" @click="addClient" class="btn btn-primary btn-lg mb-0 shadow-none">
                                <span v-if="loading">  
                                    <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true">
                                    </span>                                
                                </span>
                                <span v-else>
                                    Dar de alta
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { IptvaddViewer } 