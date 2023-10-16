import { User } from '../../src/js/user.module.js?v=2.6.6'   

const VarsViewer = {
    name : 'vars-viewer',
    props: ['showOrderMaker'],
    emits: ['sendOrder'],
    data() {
        return {
            User : new User,
            variables : null
        }
    },
    methods: {
        getAllUserVars()
        {
            this.User.getAllUserVars({},(response)=>{
                if(response.s == 1)
                {
                    console.log(this.variables)
                    this.variables = response.variables
                }
            })
        },
        saveVariable(variable)
        {
            variable.busy = true
            
            this.User.saveVariable({variable:variable},(response)=>{
                variable.busy = false

                if(response.s == 1)
                {

                }
            })
        },
        toggleModal()
        {
            $(this.$refs.offcanvasRight).offcanvas('show')
        },
        sendOrder() {
            $(this.$refs.modal).offcanvas('show')
        }
    },
    mounted() {
        this.getAllUserVars()
    },
    template : `
        <div class="offcanvas offcanvas-end blur shadow-blur overflow-scroll" tabindex="-1" ref="offcanvasRight" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
            <div>
                <div class="offcanvas-header">
                    <div id="offcanvasRightLabel">
                        <div class="h4">
                            Variables
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm px-3 shadow-none btn-danger" data-bs-dismiss="offcanvas" aria-label="Close"><i class="bi fs-5 bi-x"></i> </button>
                </div>

                <div class="offcanvas-body">
                    <div class="card bg-white lead">
                        <div class="card-body">
                            <div class="alert alert-info text-center text-white mb-3">
                                <strong>Aviso</strong>
                                <div class="text-xs">
                                    Aquí puedes configurar las variables para que las tomemos por defecto en las acciones que realices con DummieTrading. Puedes configurar el lotage que quieres arriesgar en las operaciones.
                                </div>
                            </div>

                            <div v-if="variables">
                                <div class="text-xs mb-3">Lista de variables</div>
                                <ul class="list-group list-group-flush">
                                    <li v-for="variable in variables" class="list-group-item">
                                        <div class="row align-items-center">
                                            <div class="col-12 col-xl">
                                                <div class="form-floating">
                                                    <input type="text" v-model="variable.value.value" class="form-control" id="floatingInput" :placeholder="variable.name">
                                                    <label for="floatingInput">{{variable.name}}</label>
                                                </div>
                                            </div>
                                            <div class="col-12 col-xl-auto">
                                                <button :disabled="variable.busy" @click="saveVariable(variable)" class="btn btn-primary mb-0">
                                                    <span v-if="!variable.busy">Guardar</span>
                                                    <span v-else>...</span>
                                                </button>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { VarsViewer } 