import { User } from '../../src/js/user.module.js?v=2.6.4'   

const AddticketViewer = {
    name : 'addticket-viewer',
    emits : ['maketicket'],
    data() {
        return {
            User: new User,
            ticket: {
                subject: null,
                message: null
            },
            ticketComplete: false
        }
    },
    watch : {
        ticket: {
            handler() {
                this.ticketComplete = this.ticket.subject && this.ticket.message
            },
            deep: true,
        }
    },
    methods: {
        toggleMakeTicket()
        {
            this.$emit('togglemaketicket') 
        },
        initEditor()
        {
            var toolbarOptions = [
                ['bold', 'italic', 'underline', 'strike'], 
            ];

            this.editor = new Quill('#editor', {
                modules: {
                    toolbar: toolbarOptions
                },
                theme: 'snow'
            });

            this.editor.on('text-change', () => {
                this.ticket.message = this.editor.root.innerHTML
            });
        },
        addTicket(target) {
            this.User.addTicket(this.ticket, (response) => {
                if (response.s == 1) {
                    target.innerText = 'Ticket creado. Redirgiendo en 3 segundos...'
                    
                    setTimeout(() => {
                        this.$emit('togglemaketicket') 
                    }, 3000);
                }
            })
        },
    },
    mounted() {
        this.initEditor()
    },
    template : `
        <div class="card bg-transparent shadow-none">
            <div class="card-header bg-transparent">
                <div class="row">
                    <div class="col">
                        <button @click="toggleMakeTicket" class="btn btn-dark shadow-none mb-0"><i class="bi bi-arrow-left-short fs-5"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-none">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <label>Asunto</label>
                        <input 
                            :autofocus="true"
                            :class="ticket.subject ? 'is-valid' : ''"
                            @keydown.enter.exact.prevent="$refs.description.focus()"
                            v-model="ticket.subject"
                            ref="subject"
                            type="text" class="form-control mb-3" placeholder="Asunto">
                    </div>
                    <div class="col-auto">
                    
                    </div>
                </div>
                <div class="mb-3">
                    <label>Describe tu problema</label>
                    <div id="editor" style="height:200px"></div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <button 
                    :disabled="!ticketComplete"
                    ref="button"
                    class="btn btn-dark mb-0 shadow-none" @click="addTicket($event.target)">
                    Crear ticket
                </button>
            </div>
        </div>        
    `,
}

export { AddticketViewer } 