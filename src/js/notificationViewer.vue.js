import { User } from '../../src/js/user.module.js?v=2.6.6'   

const NotificationViewer = {
    name: 'notification-viewer',
    data() {
        return {
            User : new User,
            notifications : null,
        }
    },
    methods: {
        getNotifications() {
            return new Promise((resolve, reject) => {
                this.User.getNotifications({},(response)=>{
                    if(response.s == 1)
                    {
                        resolve(response.notifications)
                    }

                    reject()
                })
            })
        },
    },
    mounted() 
    {
        this.getNotifications().then((notifications) => {
            this.notifications = notifications
        }).catch(() => this.notifications = false )
    },
    template : `
        <div class="card mt-3 overflow-hidden">
            <div class="card-header h4">
                Notificaciones
            </div>
            <div v-if="notifications">
                <ul class="list-group list-group-flush">
                    <li v-for="notification in notifications" class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="avatar avatar-xs">
                                    <span class="avatar avatar-xs rounded-circle bg-primary" v-html="notification.extra"></span>
                                </div>
                            </div>
                            <div class="col">
                                <div>
                                    <span class="badge text-secondary p-0">
                                        Hace {{notification.create_date.timeSince()}}
                                    </span>
                                </div>
                                {{notification.message}}
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div v-else-if="notifications == false" class="card-body text-center">
                Aún no tienes notificaciones. 
                <div class="text-xs">Vuelve más tarde</div>
            </div>
        </div>
    `
}

export { NotificationViewer }