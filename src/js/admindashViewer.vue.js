import { UserSupport } from '../../src/js/userSupport.module.js?t=1.1.4'   

const AdmindashViewer = {
    name : 'admindash-viewer',
    data() {
        return {
            UserSupport: new UserSupport,
            systemVars : null,
            systemVarsAux : null,
            query : null,
        }
    },
    methods: {
        getSystemVars() {
            this.UserSupport.getSystemVars({},(response)=>{
                if(response.s == 1)
                {
                    this.systemVars = response.systemVars
                }
            })
        },
        updateSystemVar() {
            this.UserSupport.updateSystemVar({},(response)=>{
                if(response.s == 1)
                {
                    this.systemVars = response.systemVars
                }
            })
        },
        saveSystemVars() {
            this.UserSupport.saveSystemVars({},(response)=>{
                if(response.s == 1)
                {
                    this.systemVars = response.systemVars
                }
            })
        },
        saveSystemVar(systemVar) {
            systemVar.busy = true
            this.UserSupport.saveSystemVar({systemVar:systemVar},(response)=>{
                systemVar.busy = false
                
                if(response.s == 1)
                {

                }
            })
        },
    },
    mounted() 
    {   
        this.getSystemVars()
    },
    template : `
        <div class="row justify-content-center">  
            <div class="col-12">  
                <div class="alert alert-light text-center">  
                    <strong>Información de tu sitio</strong>
                    <div>Añade la información de tu sitio</div>
                </div>
            </div>
        </div>
        <div v-if="systemVars" class="card card-body">
            <ul class="list-group list-group-flush">
                <li v-for="systemVar in systemVars" class="list-group-item border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1">{{systemVar.description}}</span>
                                <input v-model="systemVar.val" @keypress.exact.enter="saveSystemVar(systemVar)" :class="systemVar.val ? 'is-valid' : 'is-invalid'" type="text" class="form-control px-3" :placeholder="systemVar.description" :aria-label="systemVar.description" aria-describedby="basic-addon1">
                            </div>
                        </div>
                        <div class="col-auto">
                            <button :disabled="!systemVar.busy" @click="saveSystemVar(systemVar)" class="btn btn-primary" v-text="systemVar.busy ? '...' : 'Actualizar'">
                            </button>
                        </div>
                        <div v-if="systemVar.busy" class="col-auto">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    `,
}

export { AdmindashViewer } 