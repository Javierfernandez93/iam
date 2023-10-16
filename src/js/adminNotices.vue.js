import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.6'

Vue.createApp({
    components : {
    },
    data() {
        return {
            UserSupport : null,
            notices : {},
            query : null,
            noticesAux : {},
            columns: { // 0 DESC , 1 ASC 
                notice_id : {
                    name: 'notice_id',
                    desc: true,
                },
                title : {
                    name: 'title',
                    desc: false,
                    alphabetically: true,
                },
                notice : {
                    name: 'notice',
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
            this.notices.sort((a,b) => {
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
        goToEdit : function(notice_id) {
            window.location.href = `../../apps/admin-news/edit?nid=${notice_id}`
        },
        publishNotice : function(notice_id) {
            this.UserSupport.publishNotice({notice_id:notice_id},(response)=>{
                if(response.s == 1)
                {
                    this.getNotices()
                }
            })
        },
        unpublishNotice : function(notice_id) {
            this.UserSupport.unpublishNotice({notice_id:notice_id},(response)=>{
                if(response.s == 1)
                {
                    this.getNotices()
                }
            })
        },
        deleteNotice : function(notice_id) {
            this.UserSupport.deleteNotice({notice_id:notice_id},(response)=>{
                if(response.s == 1)
                {
                    this.getNotices()
                }
            })
        },
        filterData : function() {
            this.notices = this.noticesAux
            
            this.notices = this.noticesAux.filter((notice)=>{
                return notice.title.toLowerCase().includes(this.query.toLowerCase()) || notice.create_date.formatDate().toLowerCase().includes(this.query.toLowerCase())
            })
        },
        getNotices : function() {
            this.UserSupport.getNotices({},(response)=>{
                if(response.s == 1)
                {
                    this.noticesAux = response.notices
                    this.notices = this.noticesAux
                }
            })
        },
    },
    mounted() 
    {
        this.UserSupport = new UserSupport
        this.getNotices()
    },
}).mount('#app')