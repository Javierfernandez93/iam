import { User } from '../../src/js/user.module.js?v=2.6.6'   

const DummyViewer = {
    name : 'dummy-viewer',
    data() {
        return {
            User : new User,
            user: null,
            interval: null,
            step: 1,
            STEPS: {
                FIRST : {
                    title: 'Conectáte Dummie Trading',
                    code: 1,
                },
            },
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
            return new Promise((resolve,reject)=>{
                this.User.getUserTelegram({},(response)=>{
                    if(response.s == 1)
                    {
                        resolve(response.user)
                    }

                    reject()
                })
            })
        },
        createUserTelegramCredentials() {
            return new Promise((resolve)=>{
                this.User.createUserTelegramCredentials({},()=>{
                    resolve()
                })
            })
        },
        openBotConfiguration(token) {
            window.open(`https://t.me/DummieTrading?start=${token}`)
        },
        sendDummieTradingTest(target) {
            this.User.sendDummieTradingTest(this.user,()=>{
                target.innerText = 'Enviado'
            })
        },
        isUserConnectedWithDummyTrading() {
            this.User.isUserConnectedWithDummyTrading(this.user,(response)=>{
                if(response.s == 1)
                {
                    this.user.connected = true
                    clearInterval(this.interval)
                }
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
        reconnectDummieTrading() {
            this.user.connected = false
            this.startWatching()
        },
        startWatching() {
            this.interval = setInterval(() =>{
                this.isUserConnectedWithDummyTrading()
            },4000)
        }
    },
    mounted() 
    {   
        this.step = this.STEPS.FIRST

        this.createUserTelegramCredentials().then(()=>{
            this.getUserTelegram().then((user)=>{
                this.user = user

                if(!this.user.connected)
                {
                    this.startWatching()
                }
            }).catch(()=>{
                this.user = false
            })
        })
    },
    template : `
        <div v-if="user">
            <div v-if="user.connected" class="card blur shadow-blur card-body text-primary">
                <div class="row align-items-center fs-3 fw-semibold">
                    <div class="col">
                        Conectado a Dummie Trading 
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="col-auto">
                        <div class="d-grid">
                            <button @click="sendDummieTradingTest($event.target)" class="btn btn-primary">Enviar mensaje de prueba</button>
                        </div>
                        <div class="d-grid">
                            <button @click="reconnectDummieTrading" class="btn btn-primary mb-0">Volver a conectar</button>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="p-5">
                <div class="row justify-content-center">
                    <div class="col-12 col-xl-5">
                        <div class="card bg-primary">
                            <div class="card-body">
                                <div class="text-center text-white fw-semibold text-dark py-3 fs-4">{{step.title}}</div>

                                <div class="card">
                                    <div class="card-body">
                                        <img :src="qr.getQrCode()" class="w-100" title="bot" alt="bot"/>

                                        <div class="text-center fw-semibold fs-4 text-primary">
                                            {{userName}}
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="d-grid">
                                            <button @click="openBotConfiguration(user.token_key)" class="btn btn-success shadow-none mb-0 btn-lg" target="_blank">
                                                <div>
                                                    ¡Conectar
                                                </div>
                                                <div>
                                                    a DummieTrading!
                                                </div>
                                            </button>
                                        </div>
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

export { DummyViewer } 