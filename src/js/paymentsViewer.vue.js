import { User } from '../../src/js/user.module.js?v=2.6.5'   

const PaymentsViewer = {
    name : 'payments-viewer',
    data() {
        return {
            User: new User,
            query: null,
            paymentsAux: null,
            payments: null,
            totals: {
                total_capital: 0
            },
            paymentsTitle: null,
            PAYMENTS: {
                1 : 'Activaciones',
                3 : 'Mensualidades',
            },
            STATUS: {
                DELETED: {
                    code: -1,
                    text: 'Eliminadas'
                },
                EXPIRED: {
                    code: 0,
                    text: 'Expiradas'
                },
                PENDING: {
                    code: 1,
                    text: 'Pendientes'
                },
                VALIDATED: {
                    code: 2,
                    text: 'Validadas'
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
        approbeReferralPayment(payment) {
            let alert = alertCtrl.create({
                title: "Aviso",
                subTitle: `¿Estás seguro de validar este pago con orden de compra <b>${payment.invoice_id}</b>?`,
                buttons: [
                    {
                        text: "Sí, aprobar",
                        class: 'btn-success',
                        role: "cancel",
                        handler: (data) => {
                            this.User.approbeReferralPayment({invoice_id:payment.invoice_id}, (response) => {
                                if (response.s == 1) {
                                    payment.status = response.status
                                    
                                    alertInfo({
                                        icon:'<i class="bi bi-ui-checks"></i>',
                                        message: "Hemos validado el pago correctamente",
                                        _class:'bg-gradient-success text-white'
                                    },500)
                                } else if(response.r == "NOT_LICENCES") {
                                    alertInfo({
                                        icon:'<i class="bi bi-ui-checks"></i>',
                                        message: `No tienes más licencias, para comprar una da click <a class="text-white text-decoration-underline" href="../../apps/store/package">aquí</a></div>`,
                                        _class:'bg-danger text-white'
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
        deleteReferralPayment(payment) {
            let alert = alertCtrl.create({
                title: "Aviso",
                subTitle: `¿Estás seguro de cancelar este pago con orden de compra <b>${payment.invoice_id}</b>?`,
                buttons: [
                    {
                        text: "Sí, cancelar",
                        class: 'btn-success',
                        role: "cancel",
                        handler: (data) => {
                            this.User.deleteReferralPayment({invoice_id:payment.invoice_id}, (response) => {
                                if (response.s == 1) {
                                    payment.status = response.status
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
        viewObservation(observation) {
            alertInfo({
                icon:'<i class="bi bi-chat-left-dots-fill text-primary"></i>',
                message: `<div class="text-lowercase text-primary">${observation}</div>`,
                _class:'bg-primary-success text-white',
                size: 'modal-md'
            })
        },
        truncate(text) {
            if(text)
            {
                return text.length > 20 ? text.slice(0, 20) + "..." : text
            } 

            return ''
        },
        getReferralPayments(catalog_package_type_id) {
            return new Promise((resolve,reject) => {
                this.User.getReferralPayments({catalog_package_type_id:catalog_package_type_id}, (response) => {
                    if (response.s == 1) {
                        resolve(response.payments)
                    }

                    reject()
                })
            })
        }
    },
    mounted() {
        if(getParam("cptid"))
        {
            const catalog_payment_method_id = getParam("cptid")

            this.paymentsTitle = this.PAYMENTS[catalog_payment_method_id]

            this.getReferralPayments(catalog_payment_method_id).then((payments)=>{
                this.payments = payments
                this.paymentsAux = payments
            }).catch((error) => {
                this.payments = false
                this.paymentsAux = false
            })
        }
    },
    template : `
        <div v-if="paymentsAux" class="card mb-4 overflow-hidden">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col fw-semibold text-dark">{{paymentsTitle}}</div>
                    <div class="col-auto"><span class="badge text-dark">Total {{payments.length}}</span></div>
                </div>
            </div>
        
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <input type="search" class="form-control" v-model="query" placeholder="buscar por nombre o estado"/>
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
                    <table class="table table-stripeds table-borderless align-items-center mb-0">
                        <thead>
                            <tr class="font-bold text-center text-sm fw-sembold text-dark text-secondary text-uppercase opacity-7">
                                <th class="text-uppercase">Orden de compra</th>
                                <th class="text-uppercase">Usuario</th>
                                <th class="text-uppercase">Correo</th>
                                <th class="text-uppercase">Nota</th>
                                <th class="text-uppercase">Estatus</th>
                                <th class="text-uppercase">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="payment in payments">
                                <td class="align-middle text-center text-sm">
                                    {{payment.invoice_id}}
                                </td>
                                <td class="text-center">
                                    <h6 class="mb-0 text-capitalize text-sm">{{payment.names}}</h6>
                                </td>
                                <td class="text-center">
                                    <p class="text-secondary text-sm mb-0">{{payment.email}}</p>
                                </td>
                                <td class="text-center">
                                    <p class="text-secondary mb-0">{{truncate(payment.ipn_data.observation)}}</p>
                                </td>
                                <td class="text-center">
                                    <span v-if="payment.status == STATUS.VALIDATED.code" class="badge bg-success">Activo</span>
                                    <span v-else-if="payment.status == STATUS.PENDING.code" class="badge bg-secondary">No activo</span>
                                    <span v-else-if="payment.status == STATUS.EXPIRED.code" class="badge bg-secondary">Expirada</span>
                                    <span v-else-if="payment.status == STATUS.DELETED.code" class="badge bg-danger">Eliminada</span>
                                </td>
                                <td class="align-middle text-center" v-if="payment.ipn_data">
                                    <div v-if="payment.status == STATUS.PENDING.code">
                                        <div class="mb-2"><button @click="viewObservation(payment.ipn_data.observation)" class="btn w-100 btn-sm px-3 btn-primary shadow-none mb-0">Ver Nota</button></div>
                                        <div class="mb-2"><a v-if="payment.ipn_data.image" :href="payment.ipn_data.image" target="_blank" class="btn w-100 btn-sm px-3 btn-light shadow-none mb-0">Ver ticket</a></div>
                                        <div class="mb-2"><button v-if="payment.ipn_data.image" @click="approbeReferralPayment(payment)" class="btn w-100 btn-sm px-3 btn-success shadow-none mb-0">Aprobar pago</button></div>
                                        <div><button v-if="payment.ipn_data.image" @click="deleteReferralPayment(payment)" class="btn btn-sm px-3 btn-danger w-100 shadow-none mb-0">Declinar pago</button></div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-auto"><span class="badge bg-secondary">Cantidad de pagos {{payments.length}}</span></div>
                </div>
            </div>
        </div>
        <div v-else-if="paymentsAux == false"class="alert alert-light text-center">
            <div><strong>Importante</strong></div>
            Aquí te mostraremos las <strong>activaciones</strong> y <strong>mensualidades</strong> que tus franquicias realicen para la validación de sus pagos
        </div>
    `,
}

export { PaymentsViewer } 