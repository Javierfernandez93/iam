import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.5'

const AdminticketsViewer = {
    name : 'admintickets-viewer',
    emits : ['maketicket'],
    data() {
        return {
            UserSupport: new UserSupport,
            tickets: null,
            ticketsAux: null,
            query: null,
            STATUS : {
                AWAIT_FOR_SUPPORT : {
                    text: 'Esperando a soporte técnico',
                    cssClass: 'bg-gradient-warning',
                    code: 0
                },
                SUPPORTING : {
                    text: 'Resolviendo',
                    cssClass: 'bg-gradient-primary',
                    code: 1
                },
                FINISHED : {
                    text: 'Finalizado',
                    cssClass: 'bg-gradient-success',
                    code: 2
                },
                DELETED : {
                    text: 'Eliminado',
                    cssClases: 'bg-gradient-danger',
                    code: -1
                },
            },
            SEND_FROM : {
                USER: 0,
                ADMIN: 1
            }
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
            this.tickets = this.ticketsAux
            this.tickets = this.tickets.filter(ticket => ticket.subject.toLowerCase().includes(this.query.toLowerCase()))
        },
        toggleMakeTicket()
        {
            this.$emit('togglemaketicket')
        },
        getAllTickets() {
            return new Promise((resolve,reject) => {
                this.UserSupport.getAllTickets({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.tickets)   
                    }
                    reject()
                })
            })
        },
        toggleReplying(ticket,index)
        {
            ticket.replying = !ticket.replying

            if(ticket.replying && !ticket.editorLoaded)
            {
                this.initEditor(ticket,index)
            }
        },
        appendUserReply(ticket,message,create_date)
        {
            console.log(ticket)
            ticket.items.items.push({
                message: message,
                create_date: create_date,
                send_from: this.SEND_FROM.ADMIN,
                sender : {
                    names: 'tu'
                }
            })
        },
        sendTicketReplyFromAdmin(ticket,target)
        {
            this.UserSupport.sendTicketReplyFromAdmin({message: ticket.reply.message, ticket_per_user_id: ticket.ticket_per_user_id, send_from: this.SEND_FROM.ADMIN}, (response) => {
                if (response.s == 1) {
                    target.innerText = 'Respuesta enviada'

                    this.appendUserReply(ticket,ticket.reply.message, Date.now())

                    ticket.showItems = true
                }
            })
        },
        setTicketAs(ticket,status)
        {
            this.UserSupport.setTicketAs({ticket_per_user_id: ticket.ticket_per_user_id, status: status}, (response) => {
                if (response.s == 1) {
                    ticket.status = status
                }
            })
        },
        initEditor(ticket,index)
        {
            ticket.editor = new Quill(`#${index}`, {
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'], 
                    ]
                },
                theme: 'snow'
            });

            ticket.editor.on('text-change', () => {
                ticket.reply.message = ticket.editor.root.innerHTML
            });

            ticket.initEditor = true
        },
        getChartCode(number)
        {
          return "ABCDEFGHIJKLMNOPQRSTUVWXYZ".charAt(number)
        }
    },
    mounted() {
        this.getAllTickets().then((tickets) => {
            this.tickets = tickets
            this.ticketsAux = tickets
        }).catch(() => this.tickets = false)
    },
    template : `
        <div class="card shadow-none mb-3 overflow-hidden">
            <div class="card-header">
                <input v-model="query" :autofocus="true" type="text" class="form-control" placeholder="Buscar..." />
            </div>
        </div>

        <div v-if="tickets">
            <div v-for="(ticket,index) in tickets" class="card overflow-hidden mb-3">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="avatar">
                                <span class="avatar bg-danger rounded-circle">
                                    {{ ticket.unique_id.getFirstLetter() }}
                                </span>
                            </div>
                        </div>
                        <div class="col fs-5 fw-sembold text-dark">
                            Ticket # {{ticket.unique_id}}
                            <div>
                                {{ticket.user.names}} ID {{ticket.user.user_login_id}} 
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="lead text-xs text-secondary">
                                {{ticket.create_date.formatFullDate()}}
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="dropdown">
                                <a class="btn btn-outline-secondary mb-0 px-3 shadow-none btn-sm dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                    
                                </a>

                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                    <li><button @click="setTicketAs(ticket,STATUS.SUPPORTING.code)" class="dropdown-item">Resolviendo</button></li>
                                    <li><button @click="setTicketAs(ticket,STATUS.FINISHED.code)" class="dropdown-item">Marcar como resuelto</button></li>
                                    <li><button @click="setTicketAs(ticket,STATUS.DELETED.code)" class="dropdown-item">Eliminar</button></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="fs-4 fw-sembold text-dark">
                        {{ticket.subject}}
                    </div>
                    <div class="fs-5"><span v-html="ticket.items.main.message"></span></div>
                </div>

                <div v-if="ticket.showItems" class="card-footer bg-light">
                    <ul class="list-group">
                        <li v-for="item in ticket.items.items" class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div v-if="item.send_from == SEND_FROM.USER" class="avatar">
                                        <span class="avatar rounded-circle bg-dark">
                                            {{item.sender.names.getFirstLetter()}}
                                        </span>
                                    </div>
                                    <div v-else-if="item.send_from == SEND_FROM.ADMIN" class="avatar">
                                        <img :src="item.sender.image" class="avatar rounded-circle" alt="support"/>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="text-dark text-xs">
                                        {{item.sender.names}}
                                    </div>
                                    <div class="fw-sembold text-dark">
                                        <span v-html="item.message"></span>
                                    </div>
                                </div> 
                                <div class="col-auto text-xs">
                                    {{item.create_date.formatFullDate()}}
                                </div> 
                            </div> 
                        </li> 
                    </ul> 
                </div> 

                <div v-show="ticket.replying" class="card-footer">
                    <div class="mb-3">
                        <label>Describe tu respuesta</label>
                        <div :id="getChartCode(index)" style="height:120px"></div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button @click="sendTicketReplyFromAdmin(ticket,$event.target)" class="btn btn-primary shadow-none mb-0">Enviar respuesta</button>
                    </div>
                </div>


                <div class="card-footer">
                    <div class="row align-items-center">
                        <div v-if="ticket.image" class="col-auto">
                            <div class="avatar">
                                <img :src="ticket.image" class="avatar rounded-circle"/>
                            </div>
                        </div>
                        <div v-if="ticket.image" class="col">
                            <div class="mb-n2"><span class="badge p-0 text-secondary">Atendiendo</span></div>
                            <div class="fs-5 text-dark fw-sembold">{{ticket.names}}</div>
                        </div>
                        <div class="col-auto">
                            <span v-if="ticket.status == STATUS.AWAIT_FOR_SUPPORT.code"
                                class="badge"
                                :class="STATUS.AWAIT_FOR_SUPPORT.cssClass">
                                {{STATUS.AWAIT_FOR_SUPPORT.text}}
                            </span>
                            <span v-else-if="ticket.status == STATUS.SUPPORTING.code"
                                class="badge"
                                :class="STATUS.SUPPORTING.cssClass">
                                {{STATUS.SUPPORTING.text}}
                            </span>
                            <span v-else-if="ticket.status == STATUS.FINISHED.code"
                                class="badge"
                                :class="STATUS.FINISHED.cssClass">
                                {{STATUS.FINISHED.text}}
                            </span>
                            <span v-else-if="ticket.status == STATUS.DELETED.code"
                                class="badge"
                                :class="STATUS.DELETED.cssClass">
                                {{STATUS.DELETED.text}}
                            </span>
                        </div>
                        <div class="col-auto">
                            <div>
                                <span class="cursor-pointer me-2" @click="ticket.showItems = !ticket.showItems" class="badge border border-primary text-primary">
                                    <i class="bi bi-chat-right-text text-xs me-2"></i>
                                    <span v-if="ticket.items.items">
                                        {{ticket.items.items.length}}
                                    </span>
                                    <span v-else>
                                        0
                                    </span>
                                </span>

                                <button @click="toggleReplying(ticket,getChartCode(index))" class="btn btn-dark shadow-none mb-0 btn-sm"
                                    v-text="ticket.replying ? 'Cerrar respuesta' : 'Responder'"></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="card">
            <div class="card-body text-center">
                <div class="fs-4 fw-sembold">¿Necesitas ayuda?</div>
                <div class="text-secondary">Crea un ticket y te responderemos en 24 horas</div>
            </div>
        </div>
    `,
}

export { AdminticketsViewer } 