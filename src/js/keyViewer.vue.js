import { User } from '../../src/js/user.module.js?v=2.6.5'   

const KeyViewer = {
    name : 'key-viewer',
    data() {
        return {
            User: new User,
            licences: false,
            licencesAux: false,
            status: null,
            query: null,
            STATUS: {
                DELETED: {
                    code: -1,
                    text: 'Eliminadas'
                },
                UNAVIABLE: {
                    code: 0,
                    text: 'No disponibes'
                },
                AVIABLE: {
                    code: 1,
                    text: 'Disponibles'
                },
                USED: {
                    code: 2,
                    text: 'Usadas'
                },
                EXPIRED: {
                    code: 3,
                    text: 'Expirada'
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
        status: {
            handler() {
                this.filterDataByStatus()
            },
            deep: true
        }
    },
    methods: {
        filterData() {
            this.licences = this.licencesAux
            this.licences = this.licences.filter((licence) => {
                return licence.code.toLowerCase().includes(this.query.toLowerCase()) 
            })
        },
        sendWhatsapp(name,whatsapp,phone_code) {
            whatsapp = phone_code + whatsapp
            window.open(whatsapp.sendWhatsApp(`*Hola ${name}*, te recuerdo que está próxima a vencer tu licencia en *DummieTrading*. *Renuévala* cuanto antes para que *no pierdas* tus *beneficios*`))
        },
        filterDataByStatus() {
            this.licences = this.licencesAux
            if(this.status != null)
            {
                this.licences = this.licences.filter((licence) => {
                    return licence.status == this.status
                })
            }
        },
        copy(text,target) {
            navigator.clipboard.writeText(text).then(() => {
               target.innerText = 'Copiado'
            })
        },
        getLicences() {
            return new Promise((resolve,reject)=> {
                this.User.getLicences({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.licences)
                    }

                    reject()
                })

            })
        },
    },
    mounted() 
    {       
        this.getLicences().then((licences)=>{
            this.licences = licences
            this.licencesAux = licences
        }).catch(() => this.licences = false)
    },
    template : `
        <div v-if="licencesAux">
            <div class="card shadow-xs mb-3 overflow-auto">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-12 col-xl-6">
                            <div class="h4">Mis licencias</div>
                            <div class="text-xs text-secondary">Listado de todas tus licencias</div>
                            <div v-if="query" class="text-dark">
                                Resultados para <strong>{{query}}</strong> - <b>{{ licences.length }}</b> resultado(s)
                            </div>
                        </div>
                        <div class="col-12 col-xl-6">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="position-relative">
                                        <span class="position-absolute start-0 top-0 mt-2 ms-3"><i class="bi bi-search"></i></span>
                                        <input type="search" class="form-control ps-5" v-model="query" placeholder="buscar por licencia, nombre o estado"/>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <select class="form-select" v-model="status" aria-label="Filtro">
                                        <option v-bind:value="null">Todas</option>
                                        <option v-for="_STATUS in STATUS" v-bind:value="_STATUS.code">
                                            {{ _STATUS.text }}
                                        </option>
                                    </select>
                                </div>

                                <div class="col-auto">
                                    <span class="badge border border-secondary text-secondary">Total {{licencesAux.length}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <table class="table">
                    <thead>
                        <tr class="text-xs text-dark fw-semibold">
                            <th class="border-top px-2 border-light border-bottom"></th>
                            <th class="border-top px-2 border-light border-bottom" scope="col">#</th>
                            <th class="border-top px-2 border-light border-bottom" scope="col">Licencia</th>
                            <th class="border-top px-2 border-light border-bottom" scope="col">Estado</th>
                            <th class="border-top px-2 border-light border-bottom" scope="col">Por</th>
                            <th class="border-top px-2 border-light border-bottom" scope="col">Tiempo restante</th>
                            <th class="border-top px-2 border-light border-bottom text-center" scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(licence,key) in licences">
                            <td></td>
                            <td class=" text-left">
                                {{key+1}}
                            </td>
                            <td>
                                {{licence.code}}
                            </td>
                            <td>
                                <span class="badge bg-primary" v-if="licence.status == STATUS.USED.code">En uso</span>
                                <span class="badge bg-success" v-else-if="licence.status == STATUS.AVIABLE.code">Disponible</span>
                                <span class="badge bg-secondary" v-else-if="licence.status == STATUS.UNAVIABLE.code">No disponbile</span>
                                <span class="badge bg-danger" v-else-if="licence.status == STATUS.DELETED.code">Eliminada</span>
                                <span class="badge bg-danger" v-else-if="licence.status == STATUS.EXPIRED.code">Expirada</span>
                            </td>
                            <td>
                                <span class="fw-semibold text-capitalize text-dark" v-if="licence.status == STATUS.USED.code">
                                    {{licence.names}}  
                                </span>
                            </td>
                            <td>
                                <div v-if="licence.status == STATUS.USED.code">
                                    <div class="text-left text-xs pb-2">Quedan {{licence.left.days}} día(s)</div>
                                    <div class="progress w-100">
                                        <div style="height:0.5rem":style="{width: licence.left.percentaje+'%'}" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span v-if="licence.status == STATUS.USED.code">
                                    <span v-if="licence.left.days <= 10">
                                        <button @click="sendWhatsapp(licence.names,licence.phone,licence.phone_code)" class="btn btn-success mb-0 shadow-none">Envíar recordatorio WhatsApp</button>
                                    </span>
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    `,
}

export { KeyViewer } 