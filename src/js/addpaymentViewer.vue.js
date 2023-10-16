import { User } from '../../src/js/user.module.js?v=2.6.6'   

const AddpaymentViewer = {
    name : 'addpayment-viewer',
    data() {
        return {
            User: new User,
            aviable: null,
            active: null,
            query: null,
            loading: false,
            package: null,
            paymentMethod: null,
            feedBack: null,
            STATUS: {
                PAYMENT_SENT: {
                    _class: 'alert-success text-white',
                    text: `El pago fué enviado y será revisado. <div class="text-sm">Revisa el estatus del pago en el menú "licencias" > "<a class="text-white text-decoration-underline" href="../../apps/store/invoices">ver compras</a>"</div>`
                },
            },
            buy: {
                catalog_payment_method_id: 6, // FRANCHISE
                ipn_data: {
                    image: null,
                    observation: null
                }
            }
        }
    },
    watch: {
        buy: {
            handler() {
                this.isBuyFilled = this.buy.ipn_data.observation != null && this.buy.ipn_data.image != null
            },
            deep: true
        },
    },
    methods: {
        filterData() {
            this.payments = this.paymentsAux
            this.payments = this.payments.filter((payment) => {
                return payment.names.toLowerCase().includes(this.query.toLowerCase()) 
            })
        },
        viewTicket(ipn_data) {
            if(ipn_data != null)
            {
                const { image } = ipn_data

                if(image != null)
                {
                    window.location.href = image
                }
            }
        },
        openFileManager() 
        {
            this.$refs.file.click()
        },
        uploadFile() 
        {
            $(".progress").removeClass("d-none")

            let files = $(this.$refs.file).prop('files');
            var form_data = new FormData();
          
            form_data.append("file", files[0]);
          
            this.User.uploadPaymentImage(form_data,$(".progress-chat").find(".progress-bar"),(response)=>{
              if(response.s == 1)
              {
                  this.buy.ipn_data.image = response.target_path
              }
            });
        },
        addReferralPayment(payment) {
            let alert = alertCtrl.create({
                title: "Aviso",
                subTitle: `¿Estás seguro de subir tu evidencia de pago?`,
                buttons: [
                    {
                        text: "Sí, aprobar",
                        class: 'btn-success',
                        role: "cancel",
                        handler: (data) => {
                            this.addReferralPayment()
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
        addReferralPayment() {
            this.loading = true
            this.User.initCart({},(response)=> {
                if(response.s == 1)
                {
                    this.User.addPackage({package_id:this.buy.package.package_id},(response)=> {
                        if(response.s == 1)
                        {
                            this.User.selectCatalogPaymentMethodId({catalog_payment_method_id:this.buy.catalog_payment_method_id},(response)=> {
                                if(response.s == 1)
                                {
                                    this.User.saveBuy({},(response)=> {
                                        if(response.s == 1)
                                        {
                                            this.User.addReferralPayment({
                                                ...{invoice_id:response.invoice_id},
                                                ...this.buy.ipn_data
                                            },(response)=>{
                                                this.loading = false
                                                
                                                alertInfo({
                                                    icon:'<i class="bi bi-ui-checks"></i>',
                                                    message: this.STATUS.PAYMENT_SENT.text,
                                                    _class:'bg-gradient-success text-white'
                                                })
                                            })
                                        } else {
                                            this.loading = false
                                        }
                                    })
                                } else {
                                    this.loading = false
                                }
                            })
                        } else {
                            this.loading = false
                        }
                    })
                } else {
                    this.loading = false
                }
            })
        },
        getPackageForReferralPayment() {
            return new Promise((resolve,reject) => {
                this.User.getPackageForReferralPayment({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.package)
                    }

                    reject()
                })
            })
        },
        isSponsorActive() {
            return new Promise((resolve,reject) => {
                this.User.isSponsorActive({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.active)
                    }

                    reject()
                })
            })
        },
        isAviableToAddPayment() {
            return new Promise((resolve,reject) => {
                this.User.isAviableToAddPayment({}, (response) => {
                    if (response.s == 1) {
                        resolve(true)
                    }

                    reject()
                })
            })
        },
        getPaymentMethodByReferral() {
            return new Promise((resolve,reject) => {
                this.User.getPaymentMethodByReferral({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.paymentMethod)
                    }

                    reject()
                })
            })
        }
    },
    mounted() {
        this.isSponsorActive().then((active) => {
            this.active = active

            if(active)
            {
                this.isAviableToAddPayment().then(() => {
                    this.aviable = true
                
                    this.getPaymentMethodByReferral().then((paymentMethod) => {
                        this.paymentMethod = paymentMethod
                    })
        
                    this.getPackageForReferralPayment().then((_package) => {
                        this.buy.package = _package
                    }).catch(error => console.log(error))
                }).catch(error => {
                    this.aviable = false
                    
                    alertInfo({
                        icon:'<i class="bi bi-x fs-3"></i>',
                        message: `<div class="text-white">Solo puedes subir evidencia de pago 10 días antes de que termine tu licencia</div>`,
                        _class:'bg-gradient-warning text-white'
                    })
                })
            }
        }).catch(error => console.log(error))
    },
    template : `
        <div v-if="active && aviable">
            <div class="alert alert-danger border-0 text-center text-white">
                <strong>Aviso</strong> Puedes incluir una fotografía en formato: png, jpg o jpeg y una nota para tu franquiciatario. 
            </div>
            <div class="row">
                <div v-if="paymentMethod" class="col-12 col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="text-sm text-secondary">Realiza el pago a la siguiente cuenta</div>
                            <div class="h3">Datos bancarios</div>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-6 text-secondary">
                                        Banco 
                                    </div>
                                    <div class="col-6">
                                        <strong>{{paymentMethod.bank}}</strong>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-6 text-secondary">
                                        Cuenta 
                                    </div>
                                    <div class="col-6">
                                        <strong>{{paymentMethod.account}}</strong>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-6 text-secondary">
                                        CLABE 
                                    </div>
                                    <div class="col-6">
                                        <strong>{{paymentMethod.clabe}}</strong>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-12 col-xl">
                    <div class="card">
                        <div v-if="buy.package" class="card-header">
                            <div class="card-header-title">
                                <div class="mb-n2">Subir evidencia de pago para tu </div>
                                <div class="fs-4 fw-semibold text-dark">{{buy.package.title}}</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center mb-3">
                                <div class="col-12"
                                    :class="buy.ipn_data.image ? 'col-xl-8' : col-xl-12">
                                    <div class="border rounded-2 p-3 text-center mb-3 shadow position-relative">
                                        <div class="fw-semibold text-primary">
                                            <div v-if="buy.ipn_data.image">Cambiar evidencia </div>
                                            <div v-else>Subir evidencia </div>
                                            <div>(Da clic aquí o arrastra la imagen para subir tu evidencia)</div>
                                        </div>
                                        <input class="opacity-0 cursor-pointer bg-dark w-100 h-100 start-0 top-0 position-absolute" ref="file" @change="uploadFile($event)" capture="filesystem" type="file" accept=".jpg, .png, .jpeg" />
                                    </div>
                                </div>
                                <div v-if="buy.ipn_data.image" class="col-12 col-xl-4">
                                    <img :src="buy.ipn_data.image" class="img-fluid img-thumbnail" title="payment">
                                    <div class="text-center py-2 text-xs text-secondary">Vista previa de la evidencia subida</div>
                                </div>
                            </div>

                            <div class="input-group mb-3">
                                <span class="input-group-text">Agregar nota para el franquiciatario</span>
                                <textarea v-model="buy.ipn_data.observation" class="form-control p-3" aria-label="Nota"></textarea>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-end">
                            <button :disabled="!isBuyFilled || loading" @click="addReferralPayment" class="btn btn-primary shadow-none mb-0">
                                <span v-if="loading"><span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span></span>
                                <span v-else>Subir evidencia de pago</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else-if="active == false" class="alert alert-danger text-white text-center">
            <strong>
                Aviso importante
            </strong>
            <div class="lead">
                <div>Tu franquiciatario (o patrocinador) no se encuentra activo. </div>
                Para que pueda recibir tu pago es necesario que él se active. Por favor pídele que se active cuanto antes.
            </div>
        </div>
        <div v-else-if="aviable == false" class="alert alert-danger text-white text-center">
            <strong>
                Aviso importante
            </strong>
            <div class="lead">
                <div>Aún no puedes subir tu evidencia de pago. </div>
                Puedes hacerlo 10 días antes que se termine tu licencia
            </div>
        </div>
    `,
}

export { AddpaymentViewer } 