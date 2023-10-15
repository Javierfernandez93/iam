import { User } from '../../src/js/user.module.js?v=2.6.4'   

const POP_UP = 1
const NEW = 2

const NoticeViewer = {
    name : 'notice-viewer',
    props : [],
    emits : [],
    data() {
        return {
            User : new User,
            notices : [],
            showPop : true,
            pops : []
        }
    },
    watch : {
        pops: {
            handler() {
                this.showPop = true
            },
            deep: true
        },
    },
    methods: {
        nextPop(index) {
            const max = this.pops.length - 1
            
            this.pops[index].view = false
            
            if(index < max)
            {
                this.pops[index+1].view = true
            } else {
                $(this.$refs.viewerModal).modal('hide')
            }
        },
        setFirstPopAsView() {
            this.pops[0].view = true
        },
        goToNotice(notice) {
            window.location.href = `../../apps/backoffice/notice?nid=${notice.notice_id}`
        },
        getNoticesList() {
            this.User.getNoticesList({},(response)=>{
                if(response.s == 1)
                {
                    response.notices.map((notice)=>{
                        if(notice.catalog_notice_id == NEW)
                        {
                            this.notices.push(notice)
                        } else if(notice.catalog_notice_id == POP_UP) {
                            this.pops.push(
                                Object.assign(notice, {aviable:false})
                            )
                        }
                    })
                    
                    if(this.pops.length > 0) 
                    {
                        this.setFirstPopAsView()
                        
                        setTimeout(()=>{
                            $(this.$refs.viewerModal).modal('show')
                        },200)
                    }
                }
            })
        }
    },
    mounted() 
    {   
        this.getNoticesList()
    },
    template : `
        <div
            v-if="notices"
            class="card overflow-hidden border-radius-xl">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <div class="h4">Noticias</div>
                    </div>
                </div>
            </div>
            <ul class="list-group list-group-flush">
                <li    
                    v-for="notice in notices" 
                    @click="goToNotice(notice)"
                    class="list-group-item list-group-item-zoom py-3 cursor-pointer">
                    <div class="row align-items-center">
                        <div><span class="badge text-secondary p-0">Hace {{notice.create_date.timeSince()}}</span></div>
                        <div class="col-12 col-xl-4">
                            <img :src="notice.image" class="img-fluid rounded" :title="notice.title"/>
                        </div>
                        <div class="col-12 col-xl-8">
                            <div class="mb-2">
                                <span class="fw-semibold text-dark fs-6">{{notice.preview}} </span>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <!-- Modal -->
        <div class="modal fade" ref="viewerModal" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div
                v-for="(pop, index) in pops" 
                class="modal-dialog modal-dialog-centered"
                :class="[(pop.view ? '' : 'd-none'), (pop.modal_class ? pop.modal_class : '')]">
                <div class="modal-content border-radius shadow-xl border-0">
                    <div class="modal-header border-0">
                        <div class="row align-items-center w-100">
                            <div class="col h4 text-center fw-sembold">
                                {{pop.title}}
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn-close bg-dark" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div v-html="pop.description"></div>
                    </div>
                    <div class="modal-footer border-0" v-if="pop.button_action">
                        <button class="btn btn-primary" @click="nextPop(index)">
                            <span v-if="index < pops.length-1">
                                Siguiente
                            </span>
                            <span v-else>
                                Cerrar
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { NoticeViewer } 