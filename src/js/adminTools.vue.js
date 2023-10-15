import { UserSupport } from '../../src/js/userSupport.module.js?t=5'

Vue.createApp({
    components : {
    },
    data() {
        return {
            UserSupport : null,
            tools : {},
            toolsAux : {},
            query : null,
            columns: { // 0 DESC , 1 ASC 
                tool_id : {
                    name: 'tool_id',
                    desc: true,
                },
                title : {
                    name: 'title',
                    desc: false,
                    alphabetically: true,
                },
                tool : {
                    name: 'tool',
                    desc: false,
                    alphabetically: true,
                },
                create_date : {
                    name: 'create_date',
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
    },
    methods: {
        sortData: function (column) {
            this.tools.sort((a,b) => {
                const _a = column.desc ? a : b
                const _b = column.desc ? b : a

                if(column.alphabetically)
                {
                    return _a[column.name].localeCompare(_b[column.name])
                } else {
                    return _a[column.name] - _b[column.name]
                }
            });

            column.desc = !column.desc
        },
        goToEdit : function(tool_id) {
            window.location.href = `../../apps/admin-tools/edit?tid=${tool_id}`
        },
        publishTool : function(tool_id) {
            this.UserSupport.publishTool({tool_id:tool_id},(response)=>{
                if(response.s == 1)
                {
                    this.getAdminTools()
                }
            })
        },
        unpublishTool : function(tool_id) {
            this.UserSupport.unpublishTool({tool_id:tool_id},(response)=>{
                if(response.s == 1)
                {
                    this.getAdminTools()
                }
            })
        },
        deleteTool : function(tool_id) {
            this.UserSupport.deleteTool({tool_id:tool_id},(response)=>{
                if(response.s == 1)
                {
                    this.getAdminTools()
                }
            })
        },
        filterData : function() {
            this.tools = this.toolsAux
            
            this.tools = this.toolsAux.filter((tool)=>{
                return tool.title.toLowerCase().includes(this.query.toLowerCase()) ||tool.create_date.formatDate().toLowerCase().includes(this.query.toLowerCase())
            })
        },
        getAdminTools : function() {
            this.UserSupport.getAdminTools({},(response)=>{
                if(response.s == 1)
                {
                    this.toolsAux = response.tools
                    this.tools = this.toolsAux
                }
            })
        },
    },
    mounted() 
    {
        this.UserSupport = new UserSupport
        this.getAdminTools()
    },
}).mount('#app')