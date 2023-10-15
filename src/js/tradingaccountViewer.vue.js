import { User } from '../../src/js/user.module.js?v=2.6.4'   

const TradingaccountViewer = {
    name : 'tradingaccount-viewer',
    data() {
        return {
            User : new User,
            profile: null,
            user: null
        }
    },
    methods: {
        copy(text,target) {
            navigator.clipboard.writeText(text).then(() => {
                target.innerText = 'Copiado'
            })
        },
        open(link) {
            window.location.href = link
        },
        openLink(conference) {
            conference.loading = true

            window.location.href = conference.link
        },
        getTradingAccount() {
            return new Promise((resolve,reject) => {
                this.User.getTradingAccount({},(response)=>{
                    if(response.s == 1)
                    {
                        resolve(response.user)
                    }

                    reject()
                })
            })
        },
        getStartedInfo(catalog_campaign_id) 
        {
            let extraHtml = ''
            let html = null

            this.User.isActive({},async (response)=>{
                if(!response.active)
                {
                    // extraHtml = await 'https://vimeo.com/860700143/3f89cc45ff'.getVimeoFrame()   
                    extraHtml = await 'https://vimeo.com/861774368/5c47dfbeb2'.getVimeoFrame()   
                }
                
                if(response.trial)
                {
                    extraHtml = await 'https://vimeo.com/861882298'.getVimeoFrame()   
                }

                if([0,2,3,5].includes(catalog_campaign_id))
                {
                    html = `<div class="card-body">
                        <div class="text-dark text-center mb-3">Comienza a operar con <strong>DummieTrading</strong> con 4 sencillos pasos</div>

                        <div class="overflow-scroll px-3" style="height:500px;">
                            <div class="card blur over-card-blur shadow-blur mb-3 animation-fall-down" style="--delay:500ms">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-xl-4">
                                            <img src="../../src/img/video-player.png" alt="video-player" title="video-player" class="img-fluid rounded"/>
                                        </div>
                                        <div class="col-12 col-xl-8">
                                            <div class="h4">Abre tu cuenta en el Broker</div>
                                            <div class="">Puedes abrir la cuenta en el broker que quieras o ver la lista de brokers recomendados</div>

                                            <div class="mt-3">
                                                <button onclick="openVideo('../../src/files/video/conectar-telegram.mp4')" class="btn btn-primary shadow-none btn-sm px-3">Ver video</button>
                                                <a href="../../apps/brokers/" target="_blank" class="btn btn-primary shadow-none btn-sm px-3">Ver Brokers</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card blur over-card-blur shadow-blur mb-3 animation-fall-down" style="--delay:700ms">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-xl-4">
                                            <img src="../../src/img/video-player.png" alt="video-player" title="video-player" class="img-fluid rounded"/>
                                        </div>
                                        <div class="col-12 col-xl-8">
                                            <div class="h4">Conecta tu Broker o exchange</div>
                                            <div class="">Ahora es necesario conectar tu broker o exchange a DummieTrading</div>

                                            <div class="mt-3">
                                                <button onclick="openVideo('../../src/files/video/conectar-metatrader.mp4')" class="btn btn-primary shadow-none btn-sm px-3">Ver video</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card blur over-card-blur shadow-blur mb-3 animation-fall-down" style="--delay:800ms">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-xl-4">
                                            <img src="../../src/img/video-player.png" alt="video-player" title="video-player" class="img-fluid rounded"/>
                                        </div>
                                        <div class="col-12 col-xl-8">
                                            <div class="h4">Fondea tu Wallet</div>
                                            <div class="">Fondea el 10% del valor de tu cuenta de trading directo a tu Billetera DummieTrading</div>

                                            <div class="mt-3">
                                                <button onclick="openVideo('../../src/files/video/fondear-wallet.mp4')" class="btn btn-primary shadow-none btn-sm px-3">Ver video</button>
                                                <a href="../../apps/ewallet/" target="_blank" class="btn btn-primary shadow-none btn-sm px-3">Ir a billetera electrónica</a>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card blur over-card-blur shadow-blur mb-3 animation-fall-down" style="--delay:1000ms">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-xl-4">
                                            <img src="../../src/img/video-player.png" alt="video-player" title="video-player" class="img-fluid rounded"/>
                                        </div>
                                        <div class="col-12 col-xl-8">
                                            <div class="h4">Suscribete</div>
                                            <div class="">El último paso es que te suscribas a una de las operativas</div>

                                            <div class="mt-3">
                                                <button onclick="openVideoLoom('https://www.loom.com/embed/63dff9e85a3845ae8441922118480e5e?sid=5a2326ae-832f-41bc-aae0-f1f9f368720b')" class="btn btn-primary shadow-none btn-sm px-3">Ver video</button>

                                                <a href="../../apps/signals/pammy" target="_blank" class="btn btn-primary shadow-none btn-sm px-3">Ir a billetera electrónica</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`
                } else if([4].includes(catalog_campaign_id)) {
                    html = `<div class="card-body">
                        <div class="text-dark text-center mb-3">Comienza a operar con <strong>DummieTrading</strong> sigue los siguientes pasos</div>

                        <div class="overflow-scroll px-3" style="height:500px;">
                            ${extraHtml}
                        
                            <div class="d-none">
                                <div class="text-dark text-center mb-3">Una vez actives tu trial en <strong>DummieTrading</strong> opera con 4 sencillos pasos</div>

                                <div class="card blur over-card-blur shadow-blur mb-3 animation-fall-down" style="--delay:500ms">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-12 col-xl-4">
                                                <img src="../../src/img/video-player.png" alt="video-player" title="video-player" class="img-fluid rounded"/>
                                            </div>
                                            <div class="col-12 col-xl-8">
                                                <div class="h4">Abre tu cuenta en el Broker</div>
                                                <div class="">Puedes abrir la cuenta en el broker que quieras o ver la lista de brokers recomendados</div>

                                                <div class="mt-3">
                                                    <button onclick="openVideo('../../src/files/video/conectar-telegram.mp4')" class="btn btn-primary shadow-none btn-sm px-3">Ver video</button>
                                                    <a href="../../apps/brokers/" target="_blank" class="btn btn-primary shadow-none btn-sm px-3">Ver Brokers</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card blur over-card-blur shadow-blur mb-3 animation-fall-down" style="--delay:700ms">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-12 col-xl-4">
                                                <img src="../../src/img/video-player.png" alt="video-player" title="video-player" class="img-fluid rounded"/>
                                            </div>
                                            <div class="col-12 col-xl-8">
                                                <div class="h4">Conecta tu Broker o exchange</div>
                                                <div class="">Ahora es necesario conectar tu broker o exchange a DummieTrading</div>

                                                <div class="mt-3">
                                                    <button onclick="openVideo('../../src/files/video/conectar-metatrader.mp4')" class="btn btn-primary shadow-none btn-sm px-3">Ver video</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card blur over-card-blur shadow-blur mb-3 animation-fall-down" style="--delay:800ms">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-12 col-xl-4">
                                                <img src="../../src/img/video-player.png" alt="video-player" title="video-player" class="img-fluid rounded"/>
                                            </div>
                                            <div class="col-12 col-xl-8">
                                                <div class="h4">Fondea tu Wallet</div>
                                                <div class="">Fondea el 10% del valor de tu cuenta de trading directo a tu Billetera DummieTrading</div>

                                                <div class="mt-3">
                                                    <button onclick="openVideo('../../src/files/video/fondear-wallet.mp4')" class="btn btn-primary shadow-none btn-sm px-3">Ver video</button>
                                                    <a href="../../apps/ewallet/" target="_blank" class="btn btn-primary shadow-none btn-sm px-3">Ir a billetera electrónica</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card blur over-card-blur shadow-blur mb-3 animation-fall-down" style="--delay:1000ms">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-12 col-xl-4">
                                                <img src="../../src/img/video-player.png" alt="video-player" title="video-player" class="img-fluid rounded"/>
                                            </div>
                                            <div class="col-12 col-xl-8">
                                                <div class="h4">Suscribete</div>
                                                <div class="">El último paso es que te suscribas a una de las operativas</div>

                                                <div class="mt-3">
                                                    <button onclick="openVideoLoom('https://www.loom.com/embed/63dff9e85a3845ae8441922118480e5e?sid=5a2326ae-832f-41bc-aae0-f1f9f368720b')" class="btn btn-primary shadow-none btn-sm px-3">Ver video</button>

                                                    <a href="../../apps/signals/pammy" target="_blank" class="btn btn-primary shadow-none btn-sm px-3">Ir a billetera electrónica</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`
                } else if([1].includes(catalog_campaign_id)) {
                    html = `<div class="card-body">
                        <div class="text-dark text-center mb-3">Comienza a operar con <strong>DummieTrading</strong> sigue los siguientes pasos</div>

                        <div class="overflow-scroll px-3" style="height:500px;">
                            <div class="card blur over-card-blur shadow-blur mb-3 animation-fall-down" style="--delay:500ms">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-xl-4">
                                            <img src="../../src/img/video-player.png" alt="video-player" title="video-player" class="img-fluid rounded"/>
                                        </div>
                                        <div class="col-12 col-xl-8">
                                            <div class="h4">Abre tu cuenta en el Broker</div>
                                            <div class="">Puedes abrir la cuenta en el broker que quieras o ver la lista de brokers recomendados</div>

                                            <div class="mt-3">
                                                <button onclick="openVideo('../../src/files/video/conectar-telegram.mp4')" class="btn btn-primary shadow-none btn-sm px-3">Ver video</button>
                                                <a href="../../apps/brokers/" target="_blank" class="btn btn-primary shadow-none btn-sm px-3">Ver Brokers</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card blur over-card-blur shadow-blur mb-3 animation-fall-down" style="--delay:700ms">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-xl-4">
                                            <img src="../../src/img/video-player.png" alt="video-player" title="video-player" class="img-fluid rounded"/>
                                        </div>
                                        <div class="col-12 col-xl-8">
                                            <div class="h4">Conecta tu Broker o exchange</div>
                                            <div class="">Ahora es necesario conectar tu broker o exchange a DummieTrading</div>

                                            <div class="mt-3">
                                                <button onclick="openVideo('../../src/files/video/conectar-metatrader.mp4')" class="btn btn-primary shadow-none btn-sm px-3">Ver video</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center card blur over-card-blur shadow-blur mb-3 animation-fall-down" style="--delay:800ms">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-12">
                                            <div class="h4">Activa tu membresía</div>
                                            <div class="">Puedes comprar 1 mes o aprovechar las ofertas por 6 y 12 meses</div>

                                            <div class="mt-3">
                                                <a href="../../apps/store/package" target="_blank" class="btn btn-primary shadow-none btn-sm px-3">Ir a productos</a>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center card blur over-card-blur shadow-blur mb-3 animation-fall-down" style="--delay:1000ms">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-12">
                                            <div class="h4">Suscribete a las señales</div>
                                            <div class="">Puedes suscribirte a operativas de señales </div>

                                            <div class="mt-3">
                                                <a href="../../apps/signals/list" target="_blank" class="btn btn-primary shadow-none btn-sm px-3">Ir proveedor de operativa</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row text-center">
                                <div class="col-12 col-xl">
                                    <div class="card blur over-card-blur shadow-blur mb-3 animation-fall-down" style="--delay:1000ms">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-12">
                                                    <div class="h4">Conecta con meta trader</div>

                                                    <div class="mt-3">
                                                        <a href="../../apps/connect/metatrader" target="_blank" class="btn btn-primary shadow-none btn-sm px-3">Conectar con MetaTrader</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-xl d-none">
                                    <div class="card blur over-card-blur shadow-blur mb-3 animation-fall-down" style="--delay:1000ms">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-12">
                                                    <div class="h4">Conecta con binance</div>

                                                    <div class="mt-3">
                                                        <a href="../../apps/connect/binance" target="_blank" class="btn btn-primary shadow-none btn-sm px-3">Conectar con Binance</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`
                }

                let alert = alertCtrl.create({
                    title: `<h3 class="">Comienza con DummieTrading</h3>`,
                    bgColor: "blur shadow-blur",
                    size: "modal-lg",
                    html: html,
                })

                alertCtrl.present(alert.modal);
            })
        },
        getProfileShort() {
            return new Promise((resolve,reject) => {
                this.User.getProfileShort({},(response)=>{
                    if(response.s == 1)
                    {
                        resolve(response.profile)
                    }

                    reject()
                })
            })
        },
        getUserCampaign()
        {
            return new Promise((resolve,reject) => {
                this.User.getUserCampaign({},(response)=>{
                    if(response.s == 1)
                    {
                        resolve(response.catalog_campaign_id)
                    }

                    reject()
                })
            })
        },
    },
    mounted() 
    {   
        setTimeout(()=>{
            this.getUserCampaign().then((catalog_campaign_id)=>{
                this.getStartedInfo(catalog_campaign_id)
            })
        },500)

        window.openVideo = function(video) {
            $(".modal").addClass("hide").removeClass("show");
            // let embed = `<div style="position: relative; padding-bottom: 46.77083333333333%; height: 0;"><iframe src="${video}" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></iframe></div>`
            let embed = `<video src="${video}" class="w-100" controls poster="../../src/img/video-player.png">
                    <p>Su navegador no soporta vídeos HTML5.</p>
                </video>`

            let alert = alertCtrl.create({
                title: "Video",
                size: "modal-xl",
                html: embed,
            })

            alertCtrl.present(alert.modal);
        },

        window.openVideoLoom = function(video) {
            $(".modal").addClass("hide").removeClass("show");
            let embed = `<div style="position: relative; padding-bottom: 46.77083333333333%; height: 0;"><iframe src="${video}" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></iframe></div>`

            let alert = alertCtrl.create({
                title: "Video",
                size: "modal-xl",
                html: embed,
            })

            alertCtrl.present(alert.modal);
        },

        this.getProfileShort().then((profile)=>{
            this.profile = profile
        })
        this.getTradingAccount().then((user)=>{
            this.user = user
        }).catch(()=> this.user = false)
    },
    template : `
        <div v-if="user" class="card overflow-hidden mt-3 animation-fall-down" style="--delay:800ms">
            <div class="card-header fs-4 fw-semibold text-primary">
                <div class="row align-items-center">
                    <div class="col">
                        Cuenta trading
                    </div>
                    <div class="col-auto">
                        <a class="btn btn-primary mb-0 shadow-none btn-sm px-3" href="../../apps/full/">Ver más</a>
                    </div>
                </div>
            </div>
            <div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-12 col-xl">
                                <div><span class="badge p-0 text-secondary">Login</span></div>
                                {{user.login}}
                            </div>
                            <div class="col-12 col-xl-auto">
                                <button @click="copy(user.url,$event.target)" class="btn btn-success mb-0 shadow-none btn-sm px-3">Copiar</button>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-12 col-xl">
                                <div><span class="badge p-0 text-secondary">Password</span></div>
                                {{user.password}}
                            </div>
                            <div class="col-12 col-xl-auto">
                                <button @click="copy(user.url,$event.target)" class="btn btn-success mb-0 shadow-none btn-sm px-3">Copiar</button>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-12 col-xl">
                                <div><span class="badge p-0 text-secondary">Trader</span></div>
                                {{user.trader}}
                            </div>
                            <div class="col-12 col-xl-auto">
                                <button @click="copy(user.url,$event.target)" class="btn btn-success mb-0 shadow-none btn-sm px-3">Copiar</button>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-12 col-xl">
                                <div><span class="badge p-0 text-secondary">Plataforma</span></div>
                                Meta Trader 5
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div v-else-if="user == false" class="card mt-3 animation-fall-down" style="--delay:800ms">
            <div v-if="profile" class="overflow-hidden position-relative border-radius-lg bg-cover h-100" style="background-image: url('../../assets/img/ivancik.jpg');">
                <span class="mask bg-gradient-dark"></span>
                <div class="card-body position-relative z-index-1 h-100 p-3">
                    <h6 class="text-white font-weight-bolder mb-3">¡Hola {{profile.names}}!</h6>
                    <p class="text-white mb-3">Si no tienes un paquete activo en DummieTrading, comienza hoy. Configura tu paquete.</p>
                    <a class="btn btn-round btn-outline-white mb-0" href="../../apps/store/package">
                        Configura tu paquete
                        <i class="fas fa-arrow-right text-sm ms-1" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
    `,
}

export { TradingaccountViewer } 