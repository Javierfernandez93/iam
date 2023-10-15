import { UserSupport } from './userSupport.module.js?t=4'
import { Loader } from './loader.module.js?t=4'

const IntentViewer = {
    name : 'intent-viewer',
    data() {
        return {
            UserSupport: new UserSupport,
            Loader: new Loader,
            query: null,
            intentsAux: null,
            intents: null,
        }
    },
    watch : {
        query: {
            handler()
            {
                this.filterData()
            },
            deep: true
        },
    },
    methods: {
        filterData() {
            this.intents = this.intentsAux

            this.intents = this.intents.filter((intent) => {
                return intent.tag.toLowerCase().includes(this.query.toLowerCase()) || intent.words.toLowerCase().includes(this.query.toLowerCase())  
            })
        },
        getAllIntents()
        {
            this.Loader.show()

            this.UserSupport.getAllIntents({},(response)=>{
                this.Loader.hide()

                if(response.s == 1)
                {
                    this.intentsAux = response.intents
                    this.intents = this.intentsAux
                }
            })
        },
    },
    mounted() 
    {   
        this.getAllIntents()
    },
    template : `
        <div class="row d-flex justify-content-center">
            <div class="col-11">
                <div class="card bg-transparent shadow-none mb-3">
                    <div class="row">
                        <div class="col-12 col-xl">
                            <input 
                                :autofocus="true"
                                v-model="query" type="text" class="form-control" placeholder="Buscar intent...">
                        </div>
                        <div class="col-12 col-xl-auto">
                            <a href="../../apps/admin-intent/add" class="btn mb-0 shadow-none btn-primary">Añadir </a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div v-for="intent in intents" class="col-12">
                        <div class="card z-zoom-element-sm mb-3">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="avatar avatar-xl">
                                            <span class="avatar-title text-uppercase bg-success-soft text-success rounded">
                                                {{intent.tag.getAcronime()}}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="text-xs text-secondary">
                                            <span v-if="intent.create_date" class="badge text-secondary p-0"><t>Actualizado hace</t> {{intent.create_date.timeSince()}}</span>
                                        </div>

                                        <div>
                                            <span class="badge text-truncate p-0 text-primary">{{intent.tag}}</span>
                                        </div>

                                        
                                        <div class="row mt-3 cursor-pointer border-top pt-3">
                                            <div v-if="intent.words" class="col-12">
                                                <div><span class="badge p-0 text-secondary">Frases</span></div>
                                                <p v-for="word in intent.words" class="text-primary z-zoom-element">
                                                    {{word}}
                                                </p>
                                            </div>
                                            
                                            <div v-if="intent.replys" class="col-12">
                                                <div><span class="badge p-0 text-secondary">Respuestas</span></div>
                                                
                                                <p v-for="reply in intent.replys" class="text-success z-zoom-element">
                                                    {{reply}}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="btn-group">
                                            <button class="btn btn-outline-primary px-3 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                <i class="mdi mdi-dots-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <button @click="deleteIntent(intent.intent_chat_id)" class="dropdown-item"><t>Eliminar</t></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { IntentViewer } 