import { User } from '../../src/js/user.module.js?v=2.6.6'   

const TestprogressViewer = {
    name : 'testprogress-viewer',
    data() {
        return {
            User : new User,
            progress : null,
            INTERVAL_TIMER : 15000,
            status : 0,
            interval : null,
            STATUS : {
                0 : {
                    text: 'Esperando',
                    _class: 'bg-secondary',
                },
                1 : {
                    text: 'En progresso',
                    _class: 'bg-success',
                },
                2 : {
                    text: 'Finalizado',
                    _class: 'bg-primary',
                },
                3 : {
                    text: 'Expirado',
                    _class: 'bg-danger',
                }
            }
        }
    },
    methods: {
        assignStatus() {
            this.progress.tests.map((test) => {
                test.statusInfo = this.STATUS[test.status]

                return test
            })
        },
        getTestProgress() {
            this.User.getTestProgress({},(response)=>{
                if(response.s == 1)
                {
                    this.status = response.status
                    this.progress = response.progress
                    this.assignStatus()
                }
            })
        },
    },
    mounted() {
        this.getTestProgress()
        
        this.interval = setInterval(()=>{
            this.getTestProgress()
        },this.INTERVAL_TIMER)
    },
    template : `
        <div v-if="progress && status > 0" class="card">
            <div class="card-body">
                <h3>Progreso general {{progress.progress}}%</h3>

                <div class="progress">
                    <div class="progress-bar" :style="{width: progress.progress + '%'}" role="progressbar" :aria-valuenow="progress.progress" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div v-for="(test,index) in progress.tests" class="col">
                        <div class="card border shadow-none">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-12 h4 col-xl">
                                        Prueba # {{index+1}} 
                                    </div>
                                    <div class="col-12 col-xl-auto">
                                        <span class="badge" :class="test.statusInfo._class">{{test.statusInfo.text}}</span>
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

export { TestprogressViewer } 