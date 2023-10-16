import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.5'

const AdminemailstatsViewer = {
    name : 'adminemailstats-viewer',
    props : [],
    emits : [],
    data() {
        return {
            UserSupport: new UserSupport,
            emails: [],
        }
    },
    methods: {
        getAllEmailCampaign: function(campaign_email_id)
        {
            return new Promise((resolve) => {
                this.UserSupport.getAllEmailCampaign({campaign_email_id:campaign_email_id},(response)=>{
                    if(response.s == 1)
                    {
                        resolve(response.emails)
                    }
                })
            })
        },
    },
    mounted() {
        if(getParam('cmid'))
        {
            this.getAllEmailCampaign(getParam('cmid')).then( emails => this.emails = emails )
        }
    },
    template : `
        <div class="card border-radius-2xl mb-4">
            <div class="card-header pb-0">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <i class="bi bi-pie-chart-fill text-gradient text-primary fs-3"></i>
                    </div>
                    <div class="col fw-semibold text-dark">
                        <div><span class="badge bg-secondary text-xxs">Total {{Object.keys(emails).length}}</span></div>
                        <div class="fs-5">Mensajes</div>
                    </div>
                </div>
            </div>
            <div class="card-header">
                <input v-model="query" :autofocus="true" type="text" class="form-control" placeholder="Buscar..." />
            </div>
            <div
                v-if="Object.keys(emails).length > 0" 
                class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr class="align-items-center">
                                <th class="text-center">    
                                    <u class="text-sm ms-2">ID</u>
                                </th>
                                <th>    
                                    <u class="text-sm ms-2">Correo</u>
                                </th>
                                <th h class="text-center">    
                                    <u class="text-sm ms-2">Usuario</u>
                                </th>
                                <th>    
                                    <u class="text-sm ms-2">Enviado</u>
                                </th>
                                <th class="text-center text-uppercase text-xxs font-weight-bolder opacity-7">Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="email in emails">
                                <td class="align-middle text-center text-sm">
                                    <p class="font-weight-bold mb-0">{{email.email_per_campaign_id}}</p>
                                </td>
                                <td>
                                    {{email.email}}
                                </td>
                                <td h class="text-center">
                                    <span v-if="email.user_login_id">
                                        <span class="badge bg-success">Sí</span>
                                    </span>
                                    <span v-else>
                                        <span class="badge bg-secondary">Invitado</span>
                                    </span>
                                </td>
                                <td>
                                    {{email.create_date.formatFullDate()}}
                                </td>
                                <td class="align-middle text-center text-sm">
                                    
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div v-else
                class="card-body">
                <div class="alert alert-secondary text-white text-center">
                    <div>No tenemos usuarios aún</div>
                </div>
            </div>
        </div>
    `,
}

export { AdminemailstatsViewer } 