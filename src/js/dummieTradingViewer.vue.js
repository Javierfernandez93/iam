import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.5'

const DummietradingViewer = {
    name : 'dummietrading-viewer',
    data() {
        return {
            UserSupport: new UserSupport,
            apis: null,
            apisAux: null,
            query: null
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
            this.faqs = this.faqsAux
            this.faqs = this.faqs.filter(faq =>  faq.title.toLowerCase().includes(this.query.toLowerCase()) )
        },
        sendMessageToTelegramChannels(api) {
            const alert = alertCtrl.create({
                title: `Enviar broadcast`,
                subTitle: `Ingresa`,
                size: 'modal-fullscreen',
                inputs: [
                    {
                        type: 'text',
                        name: 'message',
                        id: 'message',
                        placeholder: 'message',
                    },
                ],
                buttons: [
                    { 
                        text: 'Enviar',
                        handler: data => {
                            for(let channel of api.channels) 
                            {
                                this.UserSupport.sendMessageToTelegramChannel({message:data.message,telegram_api_id:api.telegram_api_id,telegram_channel_id:channel.telegram_channel_id}, (response) => {
                                    if (response.s == 1) {
                                        
                                    }
                                })
                            }     
                            
                            alertInfo({
                                icon:'<i class="bi bi-ui-checks"></i>',
                                message: 'Mensaje enviado',
                                _class:'bg-gradient-success text-white'
                            })
                        }              
                    },
                    {
                        text: 'Cancelar',
                        role: 'cancel', 
                        handler: data => {
                        }
                    },  
                ]
            });
          
            alertCtrl.present(alert.modal)
        },
        sendMessageToTelegramChannel(telegram_api_id,telegram_channel_id) {
            const alert = alertCtrl.create({
                title: `Enviar mensaje a channel`,
                subTitle: `Ingresa`,
                size: 'modal-fullscreen',
                inputs: [
                    {
                        type: 'text',
                        name: 'message',
                        id: 'message',
                        placeholder: 'message',
                    },
                ],
                buttons: [
                    { 
                        text: 'Enviar',
                        handler: data => {
                            console.log(data)
                            
                            this.UserSupport.sendMessageToTelegramChannel({message:data.message,telegram_api_id:telegram_api_id,telegram_channel_id:telegram_channel_id}, (response) => {
                                if (response.s == 1) {
                                    alertInfo({
                                        icon:'<i class="bi bi-ui-checks"></i>',
                                        message: 'Mensaje enviado',
                                        _class:'bg-gradient-success text-white'
                                    })
                                }
                            })
                        }              
                    },
                    {
                        text: 'Cancelar',
                        role: 'cancel', 
                        handler: data => {
                        }
                    },  
                ]
            });
          
            alertCtrl.present(alert.modal)
        },
        getAllTelegramApis() {
            return new Promise((resolve, reject) => {
                this.UserSupport.getAllTelegramApis({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.apis)
                    }

                    reject()
                })
            })
        },
        configureTelegramHook(api) {
            this.UserSupport.configureTelegramHook({telegram_api_id:api.telegram_api_id}, (response) => {
                if (response.s == 1) {
                    alertInfo({
                        icon:'<i class="bi bi-ui-checks"></i>',
                        message: 'Hook configurado',
                        _class:'bg-gradient-success text-white'
                    })
                }
            })
        },
    },
    mounted() {
        this.getAllTelegramApis().then((apis)=>{
            this.apis = apis
            this.apisAux = apis
        }).catch((err) => {
            this.apis = false
        })
    },
    template : `
        <div v-if="apis">
            <div v-for="api in apis" class="card mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-12 col-xl">
                            <span class="text-xs">Bot</span>
                            <div>
                                {{api.user_name}}
                            </div>
                        </div>
                        <div v-if="api.channels" class="col-12 col-xl-auto">
                            <ul class="list-group list-group-flush">
                                <li v-for="channel in api.channels" class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-xl">
                                            {{channel.name}}
                                        </div>
                                        <div class="col-12 col-xl-auto">
                                            <button @click="sendMessageToTelegramChannel(api.telegram_api_id,channel.telegram_channel_id)" class="btn btn-primary mb-0 shadow-none">Send</button>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="col-12 col-xl-auto">
                            <div class="d-grid">
                                <button @click="addChannel" class="btn btn-outline-primary btn-sm px-3 mb-2">Añadir canal</button>
                            </div>
                            <div class="d-grid">
                                <button @click="sendMessageToTelegramChannels(api)" class="btn btn-outline-primary btn-sm px-3">Enviar broadcast a canales</button>
                            </div>
                            <div class="d-grid">
                                <button @click="configureTelegramHook(api)" class="btn btn-outline-primary btn-sm px-3 mb-0">configurar hook</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { DummietradingViewer } 