import { User } from '../../src/js/user.module.js?v=2.6.6'   

const LandingpageViewer = {
    name : 'landingpage-viewer',
    props : [],
    emits : [],
    data() {
        return {
            User : new User,
            landings : null,
            active : false,
            landingsAux : null,
            query: null,
            ERRORS: {
                ALREADY_EXIST_ROUTE : {
                    code:1,
                    text: 'La ruta especificada para ésta landing ya está en uso. Por favor utiliza otra.'
                }
            }
        }
    },
    watch : {
        
    },
    methods: {
        getLandings : function() {
            this.User.getLandings({},(response)=>{
                if(response.s == 1)
                {
                    this.active = response.active
                    this.landings = response.landings
                }
            })
        },
        saveLanding : function(landing,target) {
            landing.error = null
            this.User.saveLanding({catalog_landing_id:landing.catalog_landing_id,route:landing.userLanding.route},(response)=>{
                if(response.s == 1)
                {
                    landing.editing = false

                    if(target)
                    {
                        target.innerText = 'Guardado con éxito'
                    }
                } else if(response.r == 'ALREADY_EXIST_ROUTE') {
                    landing.error = this.ERRORS.ALREADY_EXIST_ROUTE
                }
            })
        },
        copyToClipBoard : function(text,target) {
            navigator.clipboard.writeText(text).then(() => {
                if(target)
                {
                    target.innerText = 'Landing copiada'
                }
            });
        },
    },
    updated() {
    },
    mounted() 
    {   
        this.getLandings()
    },
    template : `
        <div v-if="landings" class="row justify-content-center">
            <div class="col-11 col-xl-12">
                <div v-if="!active">
                    <div class="row justify-content-center align-items-center">
                        <div class="col-12 col-xl-6">
                            <div class="card min-height-300 border-radius-2xl overflow-hidden text-center" style="background-image: url('../../src/img/landing-pages.jpg'); background-size:cover; background-position:center">
                                <div class="card-body row align-items-center text-white">
                                    <div class="col-12">
                                        <span class="mask bg-gradient-dark opacity-6"></span>
                                        <div class="position-relative" style="z-index:2">
                                            <div class="fs-2 fw-semibold">¡Tu landing en DummieTrading!</div>
                                            <div class="fs-5">Activate para poder configurar tu landing page</div>

                                            <a href="../../apps/store/network" class="btn mt-3 btn-outline-light mb-0">
                                                Ir a comprar mi curso
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <div v-for="landing in landings" class="card f-zoom-element-sm border-radius-xl mb-3">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-12 col-xl-2 pb-3 pb-xl-0">
                                    <img :src="landing.image" class="w-100 rounded img-thumbnail cursor-pointer f-zoom-element-xl" title="Landing" alt="Landing"/>
                                </div>
                                <div class="col pb-3 pb-xl-0">
                                    <div class="">
                                        <span class="fw-semibold text-dark fs-5">{{landing.landing}}</span>
                                        <span v-if="landing.userLanding" class="badge ms-2 text-xxs border border-primary text-primary">
                                            <i class="bi bi-eye-fill"></i> Vistas {{landing.userLanding.visit}}
                                        </span>
                                    </div>
                                    <div class="badge p-0 text-secondary">{{landing.description}}</div>
                                </div>
                                <div class="col pb-3 pb-xl-0">
                                    <span class="badge p-0 text-secondary">Ruta</span>
                                    <div v-if="!landing.editing">
                                        <div v-if="landing.userLanding.route" class="text-decoration-underline cursor-pointer text-primary">
                                            <span @click="copyToClipBoard(landing.path.formatLandingRoute(landing.userLanding.route))" >
                                                {{landing.path.formatLandingRoute(landing.userLanding.route)}}
                                            </span>

                                            <button 
                                                @click="copyToClipBoard(landing.path.formatLandingRoute(landing.userLanding.route),$event.target)" 
                                                class="btn btn-sm ms-1 px-3 mb-0 shadow-none btn-primary"><i class="bi bi-clipboard"></i></button>
                                        </div>
                                        <div v-else>
                                            <span class="text-secondary">
                                                {{landing.path}}
                                            </span>
                                        </div>
                                    </div>
                                    <div v-else class="input-group">
                                        <span class="input-group-text text-xxs" id="basic-addon1">{{landing.path}}</span>
                                        <input 
                                            v-model="landing.userLanding.route"
                                            @keydown.space.prevent
                                            @keydown.enter.exact.prevent="saveLanding(landing)"
                                            type="text" class="form-control" placeholder="Ruta" aria-label="Ruta" aria-describedby="basic-addon1">
                                    </div>
                                </div>
                                <div class="col-12 col-xl-2 pb-3 pb-xl-0">
                                    <button 
                                        :disabled="!active"
                                        v-if="!landing.editing" @click="landing.editing = !landing.editing" class="btn w-100 btn-primary shadow-none mb-n4">Configurar Landing</button>
                                    <button v-else
                                        :disabled="!landing.userLanding.route"
                                        @click="saveLanding(landing,$event.target)" class="btn w-100 btn-success shadow-none mb-n4">Guardar</button>
                                </div>
                            </div>
                        </div>
                        <div v-if="landing.error" class="card-footer pt-0">
                            <div class="alert alert-danger fw-semibold border-0 text-white mb-0">
                                <strong>Aviso</strong>
                                {{landing.error.text}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { LandingpageViewer } 