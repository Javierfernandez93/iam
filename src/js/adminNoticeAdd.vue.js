import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.4'

Vue.createApp({
    components : {
    },
    data() {
        return {
            UserSupport : null,
            noticeComplete : false,
            catalog_priorities : {},
            catalog_notices : {},
            notice : {
                title: null,
                description: null,
                catalog_notice_id: 2,
                catalog_priority_id: 1,
                limit_dates: false,
            },
            noticesAux : {},
            columns: { // 0 DESC , 1 ASC 
                user_support_id : {
                    name: 'name',
                    desc: false
                },
                title : {
                    name: 'capital',
                    desc: true,
                },
                notice : {
                    name: 'portfolio',
                    desc: false,
                    alphabetically: true,
                },
                create_date : {
                    name: 'gain',
                    desc: false,
                },
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
        notice : 
        {
            handler() {
                this.noticeComplete = this.notice.title && this.notice.description
            },
            deep : true
        },
    },
    methods: {
        saveNotice: function()
        {
            this.UserSupport.saveNotice(this.notice,(response)=>{
                if(response.s == 1)
                {
                    this.$refs.button.innerText = "Guardado con éxito"
                }
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
                this.notice.description = this.editor.root.innerHTML
            });
        },
        getCatalogPriorities: function()
        {
            return new Promise((resolve) => {
                this.UserSupport.getCatalogPriorities({},(response)=>{
                    if(response.s == 1)
                    {
                        this.catalog_priorities = response.catalog_priorities
                    }

                    resolve()
                })
            })
        }
    },
    mounted() 
    {
        this.UserSupport = new UserSupport

        this.getCatalogPriorities().then(()=>{
            this.getCatalogNotices()
            this.initEditor()
        });
    },
}).mount('#app')