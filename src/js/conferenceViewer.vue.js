import { User } from '../../src/js/user.module.js?v=2.6.4'   

const ConferenceViewer = {
    name : 'conference-viewer',
    data() {
        return {
            User : new User,
            timezoneConfigurated: null,
            conferences: null
        }
    },
    methods: {
        openLink(conference) {
            conference.loading = true
            window.location.href = conference.link
        },
        getConferences() {
            return new Promise((resolve,reject) => {
                this.User.getConferences({},(response)=>{
                    if(response.s == 1)
                    {
                        resolve(response.conferences,response.timezoneConfigurated)
                    }

                    reject()
                })
            })
        },
    },
    mounted() 
    {   
        this.getConferences().then((conferences,timezoneConfigurated)=>{
            this.conferences = conferences
            this.timezoneConfigurated = timezoneConfigurated
        })
    },
    template : `
        <div v-if="conferences">
            <div class="card overflow-hidden">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <div class="h4">Conferencias</div>
                        </div>
                    </div>
                </div>
                <div>
                    <div v-if="timezoneConfigurated" class="conference-overlay d-flex justify-content-center align-items-center start-0 top-0">
                        <div class="col-10 z-index-3 fw-semibold h4">
                            Por favor, configura tu zona horaria <a class="text-decoration-underline fw-sembold" href="../../apps/backoffice/profile?e=profile">aquí</a>, así podrás ver los horarios de nuestras conferencias en tu uso horario.
                        </div>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item" v-for="(day,index) in conferences">
                            <div class="text-dark fw-semibold">
                                <span class="badge bg-gradient-primary">{{day.day}}</span>
                            </div>

                            <div v-if="day.conferences">
                                <ul class="list-group list-group-flush border-0">
                                    <li v-for="conference in day.conferences" class="list-group-item f-zoom-element py-3 position-relative">
                                        <div v-if="!conference.loading">
                                            <div class="row cursor-pointer" @click="openLink(conference)">
                                                <div>
                                                    <div><span class="text-dark fw-sembold">{{conference.title}}</span></div>
                                                    <div class="my-2">
                                                        <span class="badge text-xxs bg-success me-2">{{conference.catalog_conference_title}}</span>
                                                        <span class="badge text-xxs bg-primary me-2"><i class="bi bi-clock me-1"></i> {{conference.time_formatted }}</span>
                                                        <span class="badge text-xxs bg-secondary"> {{conference.timezone }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-capitalize border border-primary text-primary badge text-xs">Por {{conference.name}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-else class="d-flex justify-content-center align-items-center">
                                            <div class="spinner-grow" style="width: 1.5rem; height: 1.5rem;" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    `,
}

export { ConferenceViewer } 