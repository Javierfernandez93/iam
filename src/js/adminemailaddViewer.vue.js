import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.5'

const AdminemailaddViewer = {
    name : 'adminemailadd-viewer',
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
        saveEmailCampaign: function()
        {
            this.UserSupport.saveEmailCampaign(this.campaign,(response)=>{
                if(response.s == 1)
                {
                    this.$refs.button.innerText = "Guardado con éxito"
                }
            })
        },
        getEmailCampaign: function()
        {
            this.UserSupport.getEmailCampaign(this.campaign,(response)=>{
                if(response.s == 1)
                {
                    resolve(response.campaign)
                }
            })
        },
        initEditor: function()
        {
            var toolbarOptions = [
                ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
                ['blockquote', 'code-block'],

                [{ 'header': 1 }, { 'header': 2 }],               // custom button values
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
                [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
                [{ 'direction': 'rtl' }],                         // text direction

                [{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdown
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                [ 'link', 'image', 'video', 'formula' ],          // add's image support
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
                            class="btn btn-primary" @click="saveEmailCampaign">
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

export { AdminemailaddViewer } 