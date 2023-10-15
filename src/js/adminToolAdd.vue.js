import { UserSupport } from '../../src/js/userSupport.module.js?t=5'

Vue.createApp({
    components : {
    },
    data() {
        return {
            UserSupport : null,
            toolComplete : false,
            catalog_tools: null,
            tool : {
                title: null,
                route: null,
                catalog_tool_id: 1,
                description: null,
            },
        }
    },
    watch : {
        query : 
        {
            handler() {
                this.filterData()
            },
            deep : true
        },
        tool : 
        {
            handler() {
                this.toolComplete = this.tool.title !== null && this.tool.route !== null
            },
            deep : true
        },
    },
    methods: {
        saveTool: function()
        {
            this.UserSupport.saveTool(this.tool,(response)=>{
                if(response.s == 1)
                {
                    this.$refs.button.innerText = "Guardado con éxito"
                }
            })
        },
        openFileManager: function () 
        {
            this.$refs.file.click()
        },
        uploadToolFile: function () 
        {
            return new Promise((resolve) => {
                let files = $(this.$refs.file).prop('files');
                var form_data = new FormData();
            
                form_data.append("file", files[0]);
            
                this.UserSupport.uploadToolFile(form_data,$(".progress-chat").find(".progress-bar"),(response)=>{
                    if(response.s == 1)
                    {
                        this.tool.route = response.target_path
                    }
                    resolve()
                })
            })
        },
        getCatalogNotices: function()
        {
            this.UserSupport.getCatalogNotices({},(response)=>{
                if(response.s == 1)
                {
                    this.catalog_notices = response.catalog_notices
                }
            })
        },
        initEditor: function()
        {
            this.editor = new Quill('#editor', {
                theme: 'snow'
            });

            this.editor.on('text-change', () => {
                this.tool.description = this.editor.root.innerHTML
            });
        },
        getCatalogTools: function()
        {
            return new Promise((resolve) => {
                this.UserSupport.getCatalogTools({},(response)=>{
                    if(response.s == 1)
                    {
                        this.catalog_tools = response.catalog_tools
                    }

                    resolve()
                })
            })
        }
    },
    mounted() 
    {
        this.UserSupport = new UserSupport

        this.getCatalogTools().then(()=>{
            this.initEditor()
        });
    },
}).mount('#app')