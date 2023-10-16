import { User } from '../../src/js/user.module.js?v=2.6.5'   

const ReferralsViewer = {
    name : 'referrals-viewer',
    data() {
        return {
            User: new User,
            referralsAux: null,
            query: null,
            referrals: null,
            totals: {
                commission: 0
            },
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
            this.referrals = this.referralsAux
            this.referrals = this.referrals.filter((referral) => {
                return referral.names.toLowerCase().includes(this.query.toLowerCase()) 
            })
        },
        sendWhatsApp(name,whatsapp,phone_code) {
            whatsapp = phone_code + whatsapp
            
            window.open(whatsapp.sendWhatsApp(`*Hola ${name}*, vi que te registraste en DummieTrading y aún no has activado tu cuenta ¿Tienes alguna duda?`))
        },
        showInfo(names) {
            alertInfo({
                icon:'<i class="bi bi-whatsapp"></i>',
                size: 'modal-md',
                message: `Enviaremos un <b>WhatsApp</b> a <b>${names}</b> para preguntar por qué aún no realiza su activación. Si estás de acuerdo presiona en el botón "Envíar WhatsApp de activación"`,
                _class:'bg-gradient-success text-white'
            })
        },
        getTotals() {
            this.referrals.map((referral) =>{
               this.totals.commission += parseInt(referral.commission)
            })
        },
        getReferrals() {
            return new Promise((resolve,reject) => {
                this.User.getReferrals({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.referrals)
                    }

                    reject()
                })
            })
        }
    },
    mounted() {
        this.getReferrals().then((referrals)=>{
            this.referrals = referrals
            this.referralsAux = referrals

            this.getTotals()
        }).catch((error) => {this.referrals = false})
    },
    template : `
        <div
            v-if="referralsAux"
            class="card mb-4 overflow-hidden">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-12 col-xl">
                        <div class="h3">Referidos</div>
                    </div>
                </div>
            </div>
        
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <input type="search" class="form-control" v-model="query" placeholder="buscar por nombre o correo"/>
                    </div>
                    <div class="col-auto">
                        <select class="form-select" v-model="status" aria-label="Filtro">
                            <option v-bind:value="null">Todos</option>
                            <option v-for="_STATUS in STATUS" v-bind:value="_STATUS.code">
                                {{ _STATUS.text }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table table-borderless align-items-center mb-0">
                        <thead>
                            <tr class="font-bold text-center text-dark text-secondary text-uppercase opacity-7">
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>País</th>
                                <th>Mensaje activación</th>
                                <th>Se unió</th>
                                <th class="d-none">Estatus</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="referral in referrals">
                                <td class="align-middle text-center">
                                    {{referral.company_id}}
                                </td>
                                <td class="text-center fw-semibold">
                                    <p class="text-dark mb-0">{{referral.names}}</p>
                                    <p class="text-secondary text-xs mb-0">{{referral.email}}</p>
                                    <p class="text-secondary mb-0">{{referral.phone}}</p>
                                </td>
                                <td class="text-center">
                                    <div v-if="referral.country">
                                        <img v-if="referral.country" :src="referral.country_id.getCoutryImage()" style="width:16px"/>
                                        {{referral.country}}
                                    </div>
                                    <div v-else>
                                        N/A
                                    </div>
                                </td>
                                
                                <td class="text-center fs-4 text-dark fw-sembold">
                                    <button @click="sendWhatsApp(referral.names,referral.phone,referral.phone_code)" class="btn btn-success btn-sm px-3 rounded-0 rounded-start mb-0">Enviar WhatsApp de activación</button>
                                    <button @click="showInfo(referral.names)" class="btn btn-secondary btn-sm px-3 rounded-0 rounded-end mb-0">?</button>
                                </td>
                                <td class="align-middle text-center">
                                    <p class="text-secondary mb-0">{{referral.signup_date.formatFullDate()}}</p>
                                </td>
                                
                                <td class="text-center d-none">
                                    <div v-if="referral.hasAccount" class="text-success">
                                        Cuenta Trader
                                    </div>
                                    <div v-else-if="referral.hasDemo" class="text-primary">
                                        Cuenta demo
                                    </div>
                                    <div v-else class="text-secondary">
                                        Sin cuenta
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-auto"><span class="badge bg-secondary">Cantidad de referidos {{referrals.length}}</span></div>
                </div>
            </div>
        </div>
        <div v-else-if="referrals == false">
            <div class="alert alert-info text-center text-white fs-5">
                <div class="fw-sembold">Comienza a hacer crecer tu lista de referidos</div>
                <div>Puedes encontrar tu Link personalizado en tu oficina virtual en <b>Programa de referidos</b></div>

                <a href="../../apps/backoffice" class="btn btn-round btn-outline-white mt-3">
                    Ir a mi oficina virtual
                    <i class="fas fa-arrow-right text-sm ms-1" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    `,
}

export { ReferralsViewer } 