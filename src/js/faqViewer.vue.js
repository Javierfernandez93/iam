import { User } from '../../src/js/user.module.js?v=2.6.4'   

const FaqViewer = {
    name : 'faq-viewer',
    emits : ['toogleviewfullfaq'],
    data() {
        return {
            User: new User,
            faqs: null,
            faqsAux: null,
            query: null
        }
    },
    watch: {
        query:
        {
            handler() {
                this.filterData()
            },
            deep: true
        }
    },
    methods: {
        filterData() {
            this.faqs = this.faqsAux
            this.faqs = this.faqs.filter(faq =>  faq.title.toLowerCase().includes(this.query.toLowerCase()) )
        },
        setViewFullFaq(state) 
        {
            this.$emit('toogleviewfullfaq',state)
        },
        toggleMakeTicket(faq) 
        {
            faq.viewFaq = false
            this.$emit('toogleviewfullfaq',false)
            this.$emit('togglemaketicket')
        },
        showFaqId(faq_id) {
            this.setViewFullFaq(true)

            this.faqs.map((faq) => {
                if(faq.faq_id == faq_id) 
                {
                    faq.viewFaq = true
                }
            })
        },
        getAllFaqs() {
            return new Promise((resolve,reject) => {
                this.User.getAllFaqs({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.faqs)   
                    }

                    reject()
                })
            })
        },
    },
    mounted() {
        
        this.getAllFaqs().then((faqs) => {
            this.faqs = faqs
            this.faqsAux = faqs
            
            if(getParam('fid'))
            {
                this.showFaqId(getParam('fid'))
            }
        }).catch(() => this.faqs = false)
    },
    template : `
        <div class="card bg-transparent shadow-none">
            <div class="card-header bg-transparent">
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="fs-4 fw-sembold text-dark">Busca una pregunta</div>
                        <div class="text-secondary">Ingresa tu pregunta o escribe una palabra</div>
                    </div>
                </div>

                <div class="input-group input-group-lg input-group-merge position-relative">
                    <input
                        v-model="query"
                        :autofocus="true"
                        type="text" class="form-control border-0 position-relative z-index-0 shadow-lg" placeholder="Buscar faq..."/>

                    <i class="bi bi-search position-absolute z-index-2 pt-2 end-5"></i>
                </div>
            </div>
        </div>
        <div v-if="faqs" class="card">
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li v-for="faq in faqs" class="list-group-item f-zoom-element-sm">
                        <div class="row cursor-pointer" @click="setViewFullFaq(true); faq.viewFaq = !faq.viewFaq">
                            <div class="col-12">
                                <div class="fs-5 text-dark fw-sembold">{{faq.title}}</div>
                            </div>
                        </div>
                        <div v-if="faq.viewFaq">
                            <div class="row">
                                <div class="col-12">
                                    <div class="text-secondary lead">
                                        <span v-html="faq.description"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="container text-center bg-light p-3 rounded-2">
                                <div class="row">
                                    <div class="mb-3">Si aún tienes dudas no dudes en contactarnos en nuestros diferentes canales</div>
                                    <div class="col-12 col-xl">
                                        <a class="btn shadow-none" href="https://zuum.link/gOUdV5"><i class="bi fs-1 bi-whatsapp text-success"></i></a>
                                    </div>
                                    <div class="col-12 col-xl">
                                        <a class="btn shadow-none" href="https://t.me/zuumbusiness"><i class="bi fs-1 text-danger bi-telegram"></i></a>
                                    </div>
                                    <div class="col-12 col-xl">
                                        <button class="btn shadow-none" @click="toggleMakeTicket(faq)"><i class="bi fs-1 text-success bi-chat-left-fill"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    `,
}

export { FaqViewer } 