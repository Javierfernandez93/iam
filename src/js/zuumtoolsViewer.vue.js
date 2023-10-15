import { User } from '../../src/js/user.module.js?v=2.6.4'   

const ZuumtoolsViewer = {
    name : 'zuumtools-viewer',
    props : [],
    emits : [],
    data() {
        return {
            User: new User,
            query: null,
            toolsAux: null,
            tools: null
        }
    },
    watch : {
        query: {
            handler() {
                this.filterData()
            },
            deep: true,
        }
    },
    methods: {
        filterData() {
            this.tools = this.toolsAux 
            
            this.tools = this.tools.filter(tool => tool.name.toLowerCase().includes(this.query.toLowerCase()) )
        },
        goToSingupToo(link) {
            window.location.href = `../../apps/backoffice/zuumSignup?link=${link}` 
        },
        getZuumTools() {
            return new Promise((resolve) => {
                this.User.getZuumTools({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.tools)   
                    }
                })
            })
        },
    },
    mounted() {
        this.getZuumTools().then((tools) => {
            this.tools = tools
            this.toolsAux = tools
        }).catch(error => this.tools = false )
    },
    template : `
        <div class="row">
            <div class="col-12">
                <div class="card mb-3">
                    <div class="input-group input-group-lg input-group-merge">
                        <input
                            v-model="query"
                            :autofocus="true"
                            type="text" class="form-control border-0 shadow-lg" placeholder="Buscar tool..."/>
                    </div>
                </div>

                <div v-if="tools" class="row">
                    <div v-for="tool in tools" class="col-12 col-xl-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="avatar avatar-xl">
                                            <img :src="tool.icon" class="avatar rounded avatar-xl">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="fs-4 fw-sembold text-dark">{{tool.name}}</div>
                                        <div>{{tool.description}}</div>
                                    </div>
                                    <div class="col-auto">
                                        <button @click="goToSingupToo(tool.link)" class="btn btn-outline-success btn-lg px-3 mb-0"><i class="bi bi-arrow-right"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { ZuumtoolsViewer } 