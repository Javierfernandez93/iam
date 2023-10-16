import { User } from '../../src/js/user.module.js?v=2.6.5'   
import { Api } from '../../src/js/api.module.js'  

const CounterViewer = {
    name : 'counter-viewer',
    data() {
        return {
            User: new User,
            reamingTime: null
        }
    },
    methods: {
        getReamingTime() {
            return new Promise((resolve,reject) => {
                this.User.getReamingTime({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.reamingTime)
                    }

                    reject()
                })
            })
        },
    },
    mounted() 
    {     
        this.getReamingTime().then((reamingTime)=>{
            this.reamingTime = reamingTime
        })  
    },
    template : `
        <div v-if="reamingTime">
            <div class="row justify-content-center">
                <div class="col-12 col-xl-7">
                    <div class="row justify-content-center text-center mb-3 text-white">
                        <div class="col-6 col-xl-3">
                            <div class="card bg-gradient-danger">
                                <div class="card-body">
                                    <div>Meses</div>
                                    <div><h3 class="text-white">{{reamingTime.months}}</h3></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-xl-3">
                            <div class="card bg-gradient-danger">
                                <div class="card-body">
                                    <div>Días</div>
                                    <div><h3 class="text-white">{{reamingTime.days}}</h3></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-xl-3">
                            <div class="card bg-gradient-danger">
                                <div class="card-body">
                                    <div>Horas</div>
                                    <div><h3 class="text-white">{{reamingTime.hours}}</h3></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-xl-3">
                            <div class="card bg-gradient-danger">
                                <div class="card-body">
                                    <div>Minutos</div>
                                    <div><h3 class="text-white">{{reamingTime.minutes}}</h3></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-light text-dark text-center">
                        <strong>
                        Aviso
                        </strong>
                        Si no te activas antes de este tiempo tu cuenta será bloqueada y tus comisiones y equipo pasarán a la empresa.
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { CounterViewer } 