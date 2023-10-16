import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.5'   

const AdminlandingViewer = {
    name : 'adminlanding-viewer',
    data() {
        return {
            UserSupport: new UserSupport,
            landings: null,
            landingsAux: null,
            query: null,
            STATUS: {
                DELETED: -1,
                UNPUBLISH: 0,
                PUBLISH: 1,
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
            this.landings = this.landingsAux
            this.landings = this.landings.filter((client) => {
                return client.name.toLowerCase().includes(this.query.toLowerCase()) || client.email.toLowerCase().includes(this.query.toLowerCase()) 
            })
        },
        editLanding(landing) {
            
        },
        deleteLanding(landing) {
            this.UserSupport.deleteLanding({landing_id:landing.landing_id}, (response) => {
                if (response.s == 1) {
                    landing.status = response.status
                }
            })
        },
        publishLanding(landing) {
            this.UserSupport.publishLanding({landing_id:landing.landing_id}, (response) => {
                if (response.s == 1) {
                    landing.status = response.status
                }
            })
        },
        unpublishLanding(landing) {
            this.UserSupport.unpublishLanding({landing_id:landing.landing_id}, (response) => {
                if (response.s == 1) {
                    landing.status = response.status
                }
            })

        },
        getAllLandings() {
            return new Promise((resolve,reject)=> {
                this.UserSupport.getAllLandings({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.landings)
                    }

                    reject()
                })
            })
        },
    },
    mounted() 
    {       
        this.getAllLandings().then((landings)=>{
            this.landings = landings
            this.landingsAux = landings
        }).catch(() => {
            this.landingsAux = false
            this.landings = false
        })
    },
    template : `
        <div v-if="landingsAux"
            class="card mb-4 overflow-hidden">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col fw-semibold text-dark">
                        <div><span class="badge p-0 text-dark">Total {{landings.length}}</span></div>
                        Lista de landings
                    </div>
                    <div class="col-auto">
                        <a class="btn btn-primary px-3 mb-0 btn-sm shadow-none" href="../../apps/admin-landing/add">Añade una aquí</a>
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
                                <th>Título</th>
                                <th>Path</th>
                                <th>Video</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="landing in landings">
                                <td class="align-middle text-center">
                                    {{landing.landing_id}}
                                </td>
                                <td class="align-middle text-center">
                                    <div>
                                        <span class="badge bg-danger" v-if="landing.status == STATUS.DELETE">Eliminada</span>
                                        <span class="badge bg-secondary" v-else-if="landing.status == STATUS.UNPUBLISH">Sin publicar</span>
                                        <span class="badge bg-success" v-else-if="landing.status == STATUS.PUBLISH">Publicada</span>
                                    </div>
                                    <div class="fw-semibold">{{landing.title}}</div>
                                </td>
                                <td class="align-middle text-center">
                                    {{landing.path.getLandingPathFormatted(landing.landing)}}
                                </td>
                                <td class="align-middle text-center">
                                    <a :href="landing.video" target="_blank">
                                        {{landing.video}}
                                    </a>
                                </td>
                                <td class="text-center text-xs fw-semibold">
                                    <div class="dropdown">
                                        <button class="btn btn-primary mb-0 px-3 shadow-none dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        
                                        </button>

                                        <ul class="dropdown-menu">
                                            <div>
                                                <li>
                                                    <button @click="editLanding(landing)" class="dropdown-item">Editar</button> 
                                                    <button @click="deleteLanding(landing)" class="dropdown-item">Eliminar</button> 
                                                    <button v-if="landing.status == STATUS.UNPUBLISH" @click="publishLanding(landing)" class="dropdown-item">Publicar</button> 
                                                    <button v-if="landing.status == STATUS.PUBLISH" @click="unpublishLanding(landing)" class="dropdown-item">Despublicar</button> 
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
        </div>
        <div v-else-if="landingsAux == false">
            <div class="alert alert-info text-center text-white">
                <div class="lead">No tenemos landings aún</div>

                <a class="btn btn-light mb-0 px-3 mt-3 btn-sm shadow-none" href="../../apps/admin-landing/add">Añade una aquí</a>
            </div>
        </div>
    `,
}

export { AdminlandingViewer } 