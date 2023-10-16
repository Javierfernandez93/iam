import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.5'

const AdminemailViewer = {
    name : 'adminemail-viewer',
    data() {
        return {
            UserSupport: new UserSupport,
            emails: null,
        }
    },
    methods: {
        getEmailLists() {
            this.UserSupport.getEmailLists({}, (response) => {
                if (response.s == 1) {
                    this.emails = response.emails
                }
            })
        },
    },
    mounted() {
        this.getEmailLists()
    },
    template : `
        <div v-if="emails">
            <div class="card">
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Correo</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Fecha de registro</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="email in emails">
                                    <td class="align-middle text-center text-xs">
                                        <span>{{email.suscriber_id}}</span>
                                    </td>
                                    <td class="align-middle text-center text-sm">{{email.email}}</td>
                                    <td class="align-middle text-center text-sm">{{email.create_date.formatFullDate()}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div v-else-if="buys == false" class="alert alert-light fw-semibold text-center">    
            No tenemos compras aún 
        </div>
    `,
}

export { AdminemailViewer } 