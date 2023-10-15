import { UserSupport } from '../../src/js/userSupport.module.js?t=5'

Vue.createApp({
    components : {
    },
    data() {
        return {
            editor: null,
            UserSupport : null,
            toolComplete : false,
            catalog_tools : {},
            tool : {
                tool_id: null,
                title: null,
                descriptionOriginal: null,
                description: null,
                catalog_tool_id: 1,
                route: null
            }
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
                this.toolComplete = this.tool.title && this.tool.description
            },
            deep : true
        },
    },
    methods: {
        updateTool: function()
        {
            this.UserSupport.updateTool(this.tool,(response)=>{
                if(response.s == 1)
                {
                    this.$refs.button.innerText = "Actualizado con éxito"
                }
            })
        },
        getTool: function(tool_id)
        {
            return new Promise((resolve)=>{
                this.UserSupport.getTool({tool_id:tool_id},(response)=>{
                    if(response.s == 1)
                    {
                        this.tool = response.tool
                    }

                    resolve(this.tool)
                })
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
                this.tool.description = this.editor.root.innerHTML
            });
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
            this.getTool(getParam("tid")).then(()=>{
                this.initEditor()
            })
        })
    },
}).mount('#app')