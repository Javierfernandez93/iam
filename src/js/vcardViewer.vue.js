import { User } from '../../src/js/user.module.js?v=2.6.5'   

const VcardViewer = {
    name : 'vcard-viewer',
    props : [],
    emits : [],
    data() {
        return {
            User: new User,
            query: null,
            aviableToAddVCard: false,
            vcardsAux: null,
            vcards: null,
            STATUS : {
                UNPUBLISHED: 0,
                PUBLISHED: 1
            }
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
            this.vcards = this.vcardsAux
            this.vcards = this.vcards.filter(vcard => vcard.title.toLowerCase().includes(this.query.toLowerCase()))
        },
        publishVcard(vcard) {
            this.User.publishVcard({vcard_per_user_id:vcard.vcard_per_user_id}, (response) => {
                if (response.s == 1) {
                    vcard.status = response.status
                }
            })
        },
        unPublishVcard(vcard) {
            this.User.unPublishVcard({vcard_per_user_id:vcard.vcard_per_user_id}, (response) => {
                if (response.s == 1) {
                    vcard.status = response.status
                }
            })
        },
        deleteCampaign(vcard_per_user_id) {
            this.User.deleteCampaign({vcard_per_user_id:vcard_per_user_id}, (response) => {
                if (response.s == 1) {
                    this._getCampaigns()
                }
            })
        },
        getAllVCards() {
            return new Promise((resolve) => {
                this.User.getAllVCards({}, (response) => {
                    
                    resolve(response)
                })
            })
        },
        copyLink(link,target) {   
            navigator.clipboard.writeText(link).then(() => {
                target.innerText = 'copiado'
            });
        },
        goToEditVCard(vcard_per_user_id)
        {
            window.location.href = `../../apps/vCard/edit?vpulid=${vcard_per_user_id}`
        },
        goToPreview(vcard_per_user_id)
        {
            window.location.href = `../../apps/vCard/preview.php?vpulid=${vcard_per_user_id}`
        },
        goToAddVCard()
        {
            window.location.href = '../../apps/vCard/add'
        },
    },
    mounted() {
        this.getAllVCards().then((response) => {
            this.aviableToAddVCard = response.aviableToAddVCard
            this.vcardsAux = response.vcards
            this.vcards = response.vcards
        }).catch(() => this.vcards = false)
    },
    template : `
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="avatar">
                            <span class="avatar fs-6 bg-dark"><i class="bi bi-funnel-fill"></i></span>
                        </div>
                    </div>
                    <div class="col">
                        <span v-if="vcards" class="badge p-0 text-dark">Total {{vcards.length}}</span>
                        <div class="fw-semibold">VCards</div>
                    </div>
                    <div class="col-auto">
                        <button 
                            :disabled="!aviableToAddVCard"
                            @click="goToAddVCard" class="btn btn-sm px-3 shadow-none btn-light m-0">Añadir vcard</button>
                    </div>
                </div>
            </div>
        </div>
    
        <div v-if="vcards">
            <div class="card mb-3">
                <div class="input-group input-group-lg input-group-merge">
                    <input
                        v-model="query"
                        :autofocus="true"
                        type="text" class="form-control border-0 shadow-lg" placeholder="Buscar vcard..."/>
                </div>
            </div>
            <div v-for="vcard in vcards" class="card mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="avatar">
                                <span class="avatar bg-dark">
                                    V
                                </span>
                            </div>
                        </div>
                        <div class="col">                
                            <div>
                                <span class="badge text-xxs"
                                    v-text="vcard.status ? 'Publicada' : 'Sin pubicar'"
                                    :class="vcard.status ? 'bg-success' : 'bg-light'"></span>
                            
                                <span class="badge text-xxs border ms-2 text-secondary border-secondary">Creada hace {{vcard.create_date.timeSince()}}</span>
                            </div>            

                            <div class="fs-5 fw-semibold text-dark py-2">{{vcard.title}}</div>
                            
                            <div>         
                                <span class="badge bg-light text-dark">         
                                    <a class="text-primary text-xs" :href="vcard.route.getFullVCardRoute()" target="_blank">                            
                                        {{vcard.route.getFullVCardRoute()}}
                                    </a>
        
                                    <button 
                                        @click="copyLink(vcard.route.getFullVCardRoute(),$event.target)"
                                        class="btn shadow-none p-0 btn-light mb-0 ms-2"><i class="bi bi-clipboard-check-fill"></i> copiar</button>
                                </span>
                            </div>
                        </div>
                        <div class="col-auto text-end">         
                            <div class="fs-3 fw-sembold text-dark">{{vcard.view.numberFormat(0)}} <i class="bi bi-graph-up-arrow text-sm text-secondary"></i></div>
                            <div><span class="badge text-secondary p-0">Clicks totales</span></div>
                        </div>
                        <div class="col-auto">
                            <div class="dropdown">
                                <a class="btn btn-primary dropdown-toggle m-0" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false"></a>
                            
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                    <li><button v-if="vcard.status == STATUS.PUBLISHED" @click="goToEditVCard(vcard.vcard_per_user_id)" class="dropdown-item">Editar</button></li>
                                    <li><button v-if="vcard.status == STATUS.PUBLISHED" @click="unPublishVcard(vcard)" class="dropdown-item">Pausar</button></li>
                                    <li><button v-if="vcard.status == STATUS.UNPUBLISHED" @click="publishVcard(vcard)" class="dropdown-item">Activar</button></li>
                                    <li><button v-if="vcard.status == STATUS.PUBLISHED" @click="goToPreview(vcard.vcard_per_user_id)" class="dropdown-item">Previsualizar</button></li>
                                    <li><button @click="deleteCampaign(vcard.vcard_per_user_id)" class="dropdown-item">Eliminar</button></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else-if="vcards == false" class="alert alert-light fw-semibold text-center">    
            No tienes vcards aún por favor añade una tarjeta virtual dando clic en el botón "Añadir Vcard"
        </div>
    `,
}

export { VcardViewer } 