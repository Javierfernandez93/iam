import { User } from '../../src/js/user.module.js?v=2.6.5'   

const NoticewatchViewer = {
    name : 'noticewatch-viewer',
    data() {
        return {
            User : new User,
            notice : null
        }
    },
    methods: {
        getNotice(notice_id) {
            return new Promise((resolve, reject) =>{
                this.User.getNotice({notice_id:notice_id},(response)=>{
                    if(response.s == 1)
                    {
                        resolve(response.notice)
                    }

                    reject()
                })
            })
        }
    },
    mounted() 
    {   
        if(getParam("nid"))
        {
            this.getNotice(getParam("nid")).then((notice) => {
                this.notice = notice
            }).catch(() => this.notice = false)
        }
    },
    template : `
        <div v-if="notice" class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <a class="btn btn-sm px-3 btn-primary shadow-none mb-0" href="../../apps/backoffice"><i class="bi text-white fs-4 bi-arrow-left-short"></i></a>
                    </div>
                    <div class="col">
                        <span class="badge p-0 text-secondary">Subida hace {{notice.create_date.timeSince()}}</span>
                        <h3>{{notice.title}}</h3>
                    </div>
                </div>
            </div>
            <div class="card-body">


                <span class="lead" v-html="notice.description"></span>
            </div>
        </div>
        <div v-else-if="notice == false" class="alert alert-light text-center">
            <div>
                <strong>Aviso</strong>
            </div>
            No encontramos noticias con la información proporcionada

            <div class="mt-3">
                <a href="../../apps/backoffice/" class="btn btn-primary btn-sm px-3 shadow-none mb-0">Volver</a>
            </div>
        </div>
    `,
}

export { NoticewatchViewer } 