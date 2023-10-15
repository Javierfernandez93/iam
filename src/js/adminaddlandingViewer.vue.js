import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.4'   

const AdminaddlandingViewer = {
    name : 'adminaddlanding-viewer',
    data() {
        return {
            UserSupport: new UserSupport,
            isFilled: false,
            editor: null,
            landing: {
                title: null,
                content: null,
                path: '',
                description: null,
                share_text: null,
                background: null,
                // video: 'https://www.youtube.com/watch?v=BWIPPx1zeR0',
                video: 'https://vimeo.com/manage/videos/818479223',
                catalog_landing_action_id: 1,
                text: 'Enviar WhatsApp',
                action: null,
                text_action: null,
            },
            spinVars: [
                {
                    text: 'Nombre de usuario',
                    spin: '{{names}}'
                },
                {
                    text: 'WhatsApp de usuario',
                    spin: '{{whatsApp}}'
                },
                {
                    text: 'Nombre de compañía',
                    spin: '{{company}}'
                }
            ],
            ACTIONS: {
              NONE : 0,
              WHATSAPP : 1,
              LINK : 2,
              SIGNUP : 3
            },
            catalog_landing_actions: null
        }
    },
    watch : {
        landing : {
            handler() {
                this.isFilled = this.landing.title != null && this.landing.video != null && this.landing.text != null && this.landing.description != null
            },
            deep: true
        },
    },
    methods: {
        isYoutubeVideo(url)
        {
            const preg = /^(?:https?:\/\/)?(?:m\.|www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
            
            return url.match(preg) ? true : false
        },
        isVimeoVideo(url)
        {
            const preg = /^.+vimeo.com\/(.*\/)?([^#\?]*)/

            return url.match(preg) ? true : false
        },
        initEditor()
        {
            var toolbarOptions = [
                ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
                [{ 'header': 1 }, { 'header': 2 }],               // custom button values
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],

                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                [ 'link'],          // add's image support
                [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
                [{ 'font': [] }],
                [{ 'align': [] }],

                ['clean']                                         // remove formatting button
            ];

            this.editor = new Quill('#editor', {
                modules: {
                    toolbar: toolbarOptions
                },
                theme: 'snow'
            });

            this.editor.on('text-change', () => {
                this.landing.content = this.editor.root.innerHTML
            });
        },
        addSpinVar(spinVar) {
            const selection = this.editor.getSelection(true)
            this.editor.insertText(selection.index, spinVar.spin)
        },
        addLanding() {
            this.UserSupport.addLanding(this.landing, (response) => {
                if (response.s == 1) {
                    alertInfo({
                        icon:'<i class="bi bi-ui-checks"></i>',
                        message: `Hemos guardado la landing`,
                        _class:'bg-gradient-success text-white'
                    })
                }
            })
        },
        getAllCatalogLandingActions() {
            return new Promise((resolve,reject)=> {
                this.UserSupport.getAllCatalogLandingActions({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.catalog_landing_actions)
                    }

                    reject()
                })
            })
        },
    },
    mounted() 
    {      
        this.getAllCatalogLandingActions().then((catalog_landing_actions) => {
            this.initEditor()
            this.catalog_landing_actions = catalog_landing_actions
        })
    },
    template : `
        <div class="row">
            <div class="col-12 col-xl-8">
                <div class="card">
                    <div class="card-header">   
                        <div class="row">   
                            <div class="col-12 col-xl">   
                                Añade una landing
                            </div>
                            <div class="col-12 col-xl-auto">   
                                <button :disabled="!isFilled" @click="addLanding" class="btn px-3 btn-primary btn-sm mb-0 shadow-none">Guardar</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-12 col-xl">
                                <div class="form-floating">
                                    <input v-model="landing.title" @keydown.enter.exact="$refs.path.focus()" type="text" class="form-control" id="title" placeholder="Título">
                                    <label for="title">Título</label>
                                </div>
                            </div>
                            <div class="col-12 col-xl">
                                <div class="form-floating">
                                    <input ref="path" v-model="landing.path" @keydown.enter.exact="$refs.description.focus()" @keydown.space.prevent type="text" class="form-control" id="path" placeholder="Path">
                                    <label for="path">URL (iam.com.mx/{{landing.path}}/LandingUsuario)</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 col-xl">
                                <div class="form-floating">
                                    <input ref="description" v-model="landing.description" type="text" class="form-control" id="description" placeholder="description">
                                    <label for="description">Descripción (para identificar)</label>
                                </div>
                            </div>
                            <div v-if="landing.catalog_landing_action_id == ACTIONS.WHATSAPP" class="col-12 col-xl" >
                                <div class="form-floating">
                                    <input v-model="landing.share_text" type="text" class="form-control" id="share_text" placeholder="Título">
                                    <label for="share_text">Texto al compartir por WhatsApp</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 col-xl">
                                <div class="form-floating">
                                    <select class="form-select" id="catalog_landing_action_id" v-model="landing.catalog_landing_action_id" aria-label="Acción">
                                        <option value="0">Ninguno</option>
                                        <option v-for="catalog_landing_action in catalog_landing_actions" v-bind:value="catalog_landing_action.catalog_landing_action_id">
                                            {{ catalog_landing_action.text }}
                                        </option>
                                    </select>
                                    <label for="catalog_landing_action_id">Acción</label>
                                </div>
                            </div>
                            <div v-if="landing.catalog_landing_action_id != 0" class="col-12 col-xl-auto">
                                <div class="form-floating">
                                    <input v-model="landing.text" type="text" class="form-control" id="text" placeholder="Texto botón">
                                    <label for="text">Texto botón</label>
                                </div>
                            </div>
                            <div v-if="landing.catalog_landing_action_id == ACTIONS.LINK" class="col-12 col-xl-auto">
                                <div class="form-floating">
                                    <input v-model="landing.action" type="text" class="form-control" id="action" placeholder="URL">
                                    <label for="action">URL (liga)</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-floating mb-3">
                            <input v-model="landing.video" type="text" class="form-control" id="video" placeholder="Video">
                            <label for="video">Link de Video (Youtube/Vimeo)</label>
                        </div>

                        <div v-if="spinVars">
                            <span v-for="spinVar in spinVars" @click="addSpinVar(spinVar)" class="badge bg-primary me-2 cursor-pointer mb-2">
                                {{spinVar.text}} ({{spinVar.spin}})
                            </span>
                        </div>

                        <div class="mb-3">
                            <label>Descripción</label>
                            
                            <div id="editor" style="height:350px">
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-4">
                <div class="card">
                    <div class="card-header">
                        Previsualización
                    </div>
                    <div class="card-body">
                        <div>{{landing.title}}</div>

                        <span v-html="landing.content"></span>

                        <div v-if="isYoutubeVideo(landing.video)">
                            <span v-html="landing.video.getYoutubeVideoFrame()"></span>
                        </div>
                        <div v-else-if="isVimeoVideo(landing.video)">
                            <span v-html="landing.video.getVimeoFrameOld()"></span>
                        </div>

                        <div class="mt-3">
                            <div v-if="landing.catalog_landing_action_id == ACTIONS.WHATSAPP" class="d-grid">
                                <button class="btn btn-success mb-0 shadow-none btn-lg">
                                    {{landing.text}}
                                </button>
                            </div>
                            <div v-else="landing.catalog_landing_action_id == ACTIONS.LINK" class="d-grid">
                                <button class="btn btn-primary mb-0 shadow-none btn-lg">
                                    {{landing.text}}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { AdminaddlandingViewer } 