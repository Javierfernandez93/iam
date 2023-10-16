import { User } from '../../src/js/user.module.js?v=2.6.5'   

const WidgettelegramViewer = {
    name : 'widgettelegram-viewer',
    data() {
        return {
            User: new User,
            user: null,
            qr: 'https://t.me/DummieTrading',
            userName: '@DummieTrading',
        }
    },
    methods: {
        copyToken(token,target) {            
            navigator.clipboard.writeText(`token=${token}`).then(() => {
                target.innerText = 'copiado'
            });
        },
        getUserTelegram() {
            this.User.getUserTelegram({},(response)=>{
                if(response.s == 1)
                {
                    this.user = response.user
                }
            })
        },
        createUserTelegramCredentials() {
            return new Promise((resolve)=>{
                this.User.createUserTelegramCredentials({},()=>{
                    resolve()
                })
            })
        },
        getQrCode() {
            const alert = alertCtrl.create({
                bgColor: `bg-light`,
                size : 'modal-fullscreen',
                html: ` 
                    <div class="row vh-100 align-items-center justify-content-center">
                        <div class="col-12 col-xl-4">
                            <div class="card"> 
                                <div class="card-header fs-5 text-center fw-sembold"> 
                                    Únete al grupo de telegram de DummieTrading
                                    <div class="fs-4 fw-sembold text-primary">${this.userName}</div>
                                </div>
                                <div class="card-body"> 
                                    <img src="${this.qr.getQrCode()}" class="w-100" alt="qr" title="qr"/>
                                </div>
                            </div>
                        </div>
                    </div>
                `,
            });

            alertCtrl.present(alert.modal);
        },
    },
    mounted() 
    {   
       this.createUserTelegramCredentials().then(()=>{
           this.getUserTelegram()
       })
    },
    template : `
        <div v-if="user">
            <div v-if="!user.chat_id" class="row mb-3">
                <div class="col-12">
                    <div class="alert alert-info text-white">
                        <div class="row align-items-center">
                            <div class="col-12 col-xl fs-5">
                                <span class="badge bg-light text-primary">1</span> Únete al bot de telegram y recibe tu bitácora en tiempo real
                                <div class="mt-3">
                                    <div>
                                        <span class="badge bg-light text-primary">2</span>
                                        Después de unirte al bot envía este mensaje al Telegram para terminar la configuración:
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-auto fw-sembold">
                                            token={{user.token_key}}
                                        </div>
                                        <div class="col-auto">
                                            <button @click="copyToken(user.token_key,$event.target)" class="btn btn-light px-3 btn-sm mb-0 shadow-none">Copiar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-xl-auto">
                                <a class="btn btn-outline-light mb-0 shadow-none me-2" :href="qr" target="_blank">Únete ahora</a>
                                <button class="btn btn-outline-light mb-0 shadow-none" @click="getQrCode(qr)">Ver qr</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="alert alert-success text-center text-white fs-5">    
                Ya está configurado tu Telegram. Recibirás información directo en tu tu chat
            </div>
        </div>
    `,
}

export { WidgettelegramViewer } 