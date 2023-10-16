import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.5'

const AdmintelegramaddViewer = {
    name : 'admintelegramadd-viewer',
    data() {
        return {
            UserSupport: new UserSupport,
            campaign: {
                title: null,
                content: null
            },
            campaignComplete: false
        }
    },
    watch : {
        campaign: {
            handler() {
                this.campaignComplete = this.campaign.title != null
            },
            deep: true,
        }
    },
    methods: {
        saveTelegramCampaign()
        {
            this.UserSupport.saveTelegramCampaign(this.campaign,(response)=>{
                if(response.s == 1)
                {
                    this.$refs.button.innerText = "Guardado con éxito"
                }
            })
        },
        getTelegramCampaign()
        {
            this.UserSupport.getTelegramCampaign(this.campaign,(response)=>{
                if(response.s == 1)
                {
                    resolve(response.campaign)
                }
            })
        },
        initEditor()
        {
            this.editor = new Quill('#editor', {
                modules: {
                    toolbar: {}
                },
                theme: 'snow'
            });

            this.editor.on('text-change', () => {
                this.campaign.content = this.editor.root.innerHTML
            });
        },
    },
    mounted() {
        this.initEditor()
    },
    template : `
        <div class="row mb-3">
            <div class="col-12 col-xl-8">
                <div class="card">
                    <div class="card-body">
                        <label>Título</label>
                        <input 
                            :autofocus="true"
                            :class="campaign.title ? 'is-valid' : ''"
                            @keydown.enter.exact.prevent="$refs.description.focus()"
                            v-model="campaign.title"
                            ref="title"
                            type="text" class="form-control" placeholder="Título">
                        
                        <div class="mb-3">
                            <label>Descripción</label>
                            
                            <div id="editor"></div>
                        </div>

                        <button 
                            :disabled="!campaignComplete"
                            ref="button"
                            class="btn btn-primary" @click="saveTelegramCampaign">
                            Guardar
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="card">
                    <div class="card-header">
                        Previsualización
                    </div>
                    <div class="card-body">
                        <div class="fw-semibold">{{campaign.title}}</div>
                        <span v-html="campaign.content"></span>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { AdmintelegramaddViewer } 