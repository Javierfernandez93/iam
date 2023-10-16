import { User } from '../../src/js/user.module.js?v=2.6.6'   

const ConnectViewer = {
    name : 'connect-viewer',
    props: ['feedback'],
    emits: ['nextStep'],
    data() {
        return {
            User : new User,
            user: null,
            busy: false,
            isBlocked: false,
            interval: null,
            step: 1,
            STEPS: {
                FIRST : {
                    title: 'Conectáte Dummie Trading',
                    code: 1,
                },
            },
            qr: 'https://t.me/Autocapitaltradingbot',
            userName: '@Autocapitaltradingbot',
        }
    },
    methods: {
        copyToken(text,target) {   
            navigator.clipboard.writeText(text).then(() => {
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
        setUserAsDisconnected() {
            this.User.setUserAsDisconnected({},()=>{
                
            })
        },
        getConfigurationUrl(token) {
            return `https://t.me/Autocapitaltradingbot?start=${token}`
        },
        getUrl() {
            return `https://t.me/Autocapitaltradingbot`
        },
        openBot() {
            window.open(this.getUrl())
        },
        openBotConfiguration(token) {
            window.open(this.getConfigurationUrl(token))
        },
        sendDummieTradingTest(target) {
            this.busy = true
            this.User.sendDummieTradingTest(this.user,()=>{
                this.busy = false
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

            this.setUserAsDisconnected()

            this.startWatching()
        },
        startWatching() {
            this.interval = setInterval(() =>{
                this.isUserConnectedWithDummyTrading()
            },2000)
        }
    },
    mounted() {
        this.startWatching()
        
        this.getUserTelegram().then((user) => {
            this.isBlocked = isIOS() || isSafari()

            this.user = user
        }).catch(() => this.user = false)
    },
    template : `
        <div v-if="user">
            <div v-if="user.connected" class="">
                <div class="row align-items-center fs-3 fw-semibold justify-content-center">
                    <div class="col-12 col-xl-4">
                        <div class="card bg-gradient-primary text-white text-center over-card-blur animation-fall-right" style="--delay:500ms">
                            <div class="card-body">
                                <div class="row justify-content-center">
                                    <div class="col-10 col-md-5 col-xl-5">
                                        <img src="../../src/img/logo.svg" class="w-100 mb-3" alt="logo" title="logo"/>
                                    </div>
                                </div>
                                <div class="text-white h4"><i class="bi bi-check-circle"></i> Conectado a telegram con Dummie Trading</div> 
                            </div>
                            <div class="card-footer">
                                <div class="d-grid">
                                    <button :disabled="busy" @click="sendDummieTradingTest($event.target)" class="btn btn-outline-light">
                                        <span v-if="busy">
                                            ...
                                        </span>
                                        <span v-else>
                                            Enviar mensaje de prueba
                                        </span>
                                    </button>
                                </div>
                                <div class="d-grid">
                                    <button @click="reconnectDummieTrading" class="btn btn-outline-light mb-0">Volver a conectar</button>
                                </div>
                                <div v-if="feedback" class="d-grid">
                                    <button @click="$emit('nextStep')" class="btn btn-light mt-3 mb-0">Seguir señales</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="feedback" class="d-flex justify-content-center mt-3">
                        <button @click="$emit('nextStep')" class="btn btn-primary mb-0 shadow-none btn-lg">
                            Siguiente paso
                        </button>
                    </div>
                </div>
            </div>
            <div v-else class="py-5">
                <div class="row align-items-center animation-fall-down" style="--delay:500ms">
                    <div class="col-12 col-xl-8">
                        
                        <h2 class="mb-3">Conéctate a DummieTrading</h2>
                        <div class="mb-3">
                            El siguiente paso es conectar <strong>DummieTrading</strong> con <strong>Telegram</strong> para poder operar desde tu <strong>Telegram</strong> a cualquier hora y sin límitaciones
                        </div>
                    </div>
                    <div class="col-12 col-xl-4 order-1">
                        <lottie-player src="../../src/files/json/telegram.json" background="transparent"  speed="1"  mode=“normal” loop autoplay style="width: 250px; height: 250px;"></lottie-player>
                    </div>
                </div>

                <div class="row my-3 justify-content-center align-items-top">
                    <div v-show="!isBlocked" class="col-12 col-xl-6 mb-3 mb-xl-0 animation-fall-right" style="--delay:800ms">
                        <div class="card card-body over-card-blur shadow-blur blur">
                            <div class="text-center">
                                <h3>Conéctate automático</h3>
                                
                                <div class="d-flex justify-content-center">
                                    <span class="border border-bottom w-25 d-inline my-3"></span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-grid">
                                    <button @click="openBotConfiguration(user.token_key)" class="btn btn-primary mb-1 btn-lg" >Conéctate abriendo este enlace</button> 
                                </div>
                                <div class="d-grid">
                                    <button @click="copyToken(getConfigurationUrl(user.token_key),$event.target)" class="btn btn-primary shadow-none btn-lg">o copia el enlace aquí</button>
                                </div>
                            </div>

                            <div>
                                <div class="text-dark">
                                    <strong>Aviso</strong> 
                                    <div>También puedes escanear el siguiente código QR con tu cámara para conectarte</div>
                                </div>
                            
                                <img :src="getConfigurationUrl(user.token_key).getQrCode()" class="w-100" title="bot" alt="bot"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-6 animation-fall-right" style="--delay:1100ms">
                        <div class="card card-body over-card-blur shadow-blur blur">
                            <div class="text-center">
                                <h3>Conéctate semi automático</h3>
                                
                                <div class="d-flex justify-content-center">
                                    <span class="border border-bottom w-25 d-inline my-3"></span>
                                </div>
                            </div>

                            <div class="mb-3 text-center">
                                <span class="badge bg-success">Paso 1</span>
                                <div>Abre DummieTrading en tu teléfono</div>
                            
                                <div class="d-grid">
                                    <a href="https://t.me/Autocapitaltradingbot" target="_blank" class="btn btn-primary btn-lg mb-0">Abrir DummieTrading</a>
                                </div>
                            </div>
                            <div class="mb-3 text-center">
                                <span class="badge bg-success">Paso 2</span>
                                <div>Copia el texto dando click en el siguiente botón y pégalo en DummieTrading</div>
                            
                                <div class="d-grid">
                                    <button @click="copyToken(user.token_key.getCommandCopy(),$event.target)" class="btn btn-primary btn-lg mb-0">Copiar texto</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { ConnectViewer } 