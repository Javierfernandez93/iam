import { User } from '../../src/js/user.module.js?v=2.6.6'   

const TestViewer = {
    name : 'test-viewer',
    data() {
        return {
            User : new User,
            busy : false,
            message : null,
            SENDER: {
                USER : 1,
                BOT : 2,
            },
            replies : [
            ],
        }
    },
    methods: {
        repeatMessage(message) {
            this.message = message 

            this.telegramDispatcher()
        },
        telegramDispatcher() {
            this.busy = true

            this.replies.unshift({
                sender : this.SENDER.USER,
                message : this.message,
            })

                    
            let tempMessage = this.message
            this.message = null

            this.User.telegramDispatcher({message:tempMessage},(response)=>{
                this.busy = false

                if(response.s == 1)
                {   
                    let message = null

                    if(response.messageOriginal.isJsonString())
                    {
                        const messageJson = JSON.parse(response.messageOriginal)
                        
                        if(messageJson.videoWeb)
                        {
                            message = messageJson.videoWeb.getVideoFrame()
                        }
                    } else if(response.message.isJsonString()) {
                        const messageJson = JSON.parse(response.message)

                        message = messageJson.result.text
                    } else {
                        message = response.message
                    }
                    
                    this.replies.unshift({
                        sender : this.SENDER.BOT,
                        message : message,
                    })
                } 
            })
        },
    },
    mounted() {
    
    },
    template : `
        <div class="row justify-content-center animation-fall-right" style="--delay:500ms;">
            <div class="col-12 col-xl-8">
                <div class="alert alert-info text-white">
                    <strong>Importante</strong>
                    <div>
                        Este command se encuentra en una fase experimential. A cualquier comando puedes escribir "Cancelar" para abortar
                    </div>
                </div>

                <div v-if="replies" class="card blur shadow-blur mb-3 messages overflow-y-scroll">
                    <ul class="list-group bg-transparent list-group-flush">
                        <li v-for="reply in replies" :class="reply.sender == SENDER.BOT ? 'bg-dark text-white' : 'bg-transparent'" class="list-group-item border-0 py-3">
                            <div v-if="reply.sender == SENDER.USER">
                                <div class="row align-items-center">
                                    <div class="col-12 col-xl-auto mb-3 mb-xl-0">
                                        <div class="avatar avatar-sm me-2 bg-dark">
                                            TU
                                        </div>
                                    </div>
                                    <div class="col-12 col-xl">
                                        <span class="cursor-pointer" @click="repeatMessage(reply.message)" v-html="reply.message"></span>
                                    </div>
                                </div>
                            </div>
                            <div v-else-if="reply.sender == SENDER.BOT">
                                <div class="row align-items-center">
                                    <div class="col-12 col-xl-auto mb-3 mb-xl-0">
                                        <div class="avatar avatar-sm me-2 bg-primary">
                                            DT
                                        </div>
                                    </div>
                                    <div class="col-12 col-xl">
                                        <span class="fw-sembold" v-html="reply.message"></span>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="card card-body">
                    <div class="row align-items-center">
                        <div class="col-12 col-xl mb-3 mb-xl-0">
                            <input @keypress.exact.enter="telegramDispatcher" v-model="message" type="text" class="form-control form-control-lg" placeholder="Send a message..."/>
                        </div>
                        <div class="col-12 col-xl-auto">
                            <div class="d-grid">
                                <button :class="message ? 'btn-primary' : ''" :disabled="busy" @click="telegramDispatcher" class="btn btn-lg px-4 rounded-1 mb-0 shadow-none">
                                    <span v-if="busy">
                                        ...
                                    </span>
                                    <span v-else>
                                        <i class="bi bi-send"></i>
                                    </span>
                                </button>
                            </div>
                        </div>
                        <div v-if="busy" class="col-xl-auto">
                            <div class="d-grid justify-content-center">
                                <span class="spinner-grow spinner-grow-sm" aria-hidden="true"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { TestViewer } 