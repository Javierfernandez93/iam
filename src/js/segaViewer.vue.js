import { User } from './user.module.js?t=4'

const SegaViewer = {
    name : 'sega-viewer',
    data() {
        return {
            User: new User,
            ewallet: null,
        }
    },
    methods: {
        sendForWhatsApp(title,link) {   
            const text = `*${title}* \n👉 Da click aquí ${link} `         
            
            window.open(text.getWhatsappLink())
        },
        copyPublicKey(public_key,event) {            
            navigator.clipboard.writeText(public_key).then(() => {
                event.target.innerText = 'copiado'
            });
        },
    },
    mounted() 
    {      
    },
    template : `
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="avatar avatar-md">
                            <span class="avatar avatar-md bg-gradient-primary">App</span>
                        </div>
                    </div>
                    <div class="col">
                        <h4>App para descomprimir rar</h4>
                        <p>Esta app sirve para descomprimir archivos .rar (rooms/archivos de juegos)</p>
                        <span class="badge bg-secondary">Peso apróximado 5.6mb</span>
                    </div>
                    <div class="col-auto">
                        <div class="d-grid"> 
                            <a href="https://zuum.link/AppRar" download class="btn btn-primary shadow-none">Descargar</a>
                        </div>
                        <div class="d-grid"> 
                            <button @click="sendForWhatsApp('Descarga la app para descomprimir los Juegos','https://zuum.link/AppRar')" class="btn btn-success mb-0 shadow-none">Envíar por WhatsApp</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="avatar avatar-md">
                            <span class="avatar avatar-md bg-gradient-primary">App</span>
                        </div>
                    </div>
                    <div class="col">
                        <h4>App para jugar</h4>
                        <p>Esta app sirve para jugar tus juegos exclusivos de super nintendo</p>
                        <span class="badge bg-secondary">Peso apróximado 1.3mb</span>
                    </div>
                    <div class="col-auto">
                        <div class="d-grid"> 
                            <a href="https://zuum.link/AppSnes" download class="btn btn-primary shadow-none">Descargar</a>
                        </div>
                        <div class="d-grid"> 
                            <button @click="sendForWhatsApp('Descarga la app para correr los Juegos','https://zuum.link/AppSnes')" class="btn btn-success mb-0 shadow-none">Envíar por WhatsApp</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="avatar avatar-md">
                            <span class="avatar avatar-md bg-gradient-success">File</span>
                        </div>
                    </div>
                    <div class="col">
                        <h4>Descargar juegos</h4>
                        <p>Lista de 782 juegos de Sega MegaDrive</p>
                        <span class="badge bg-secondary">Peso apróximado 524mb</span>
                    </div>
                    <div class="col-auto">
                        <div class="d-grid"> 
                            <a href="https://zuum.link/GamesSegaMegaDrive" download class="btn btn-primary shadow-none">Descargar</a>
                        </div>
                        <div class="d-grid"> 
                            <button @click="sendForWhatsApp('Descarga los Juegos','https://zuum.link/GamesSegaMegaDrive')" class="btn btn-success mb-0 shadow-none">Envíar por WhatsApp</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="avatar avatar-md">
                            <span class="avatar avatar-md bg-gradient-success">File</span>
                        </div>
                    </div>
                    <div class="col">
                        <h4>Descargar juegos</h4>
                        <p>Lista de 326 juegos de Sega MasterSystem</p>
                        <span class="badge bg-secondary">Peso apróximado 45mb</span>
                    </div>
                    <div class="col-auto">
                        <div class="d-grid"> 
                            <a href="https://zuum.link/GamesSegaMasterSystem" download class="btn btn-primary shadow-none">Descargar</a>
                        </div>
                        <div class="d-grid"> 
                            <button @click="sendForWhatsApp('Descarga los Juegos','https://zuum.link/GamesSegaMasterSystem')" class="btn btn-success mb-0 shadow-none">Envíar por WhatsApp</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="avatar avatar-md">
                            <span class="avatar avatar-md bg-gradient-danger">Video</span>
                        </div>
                    </div>
                    <div class="col">
                        <h4>Tutorial como instalar el paquete de 700 Juegos</h4>
                        <p>De la Consola Nes de DummieTrading</p>
                    </div>
                    <div class="col-auto">
                        <div class="d-grid"> 
                            <a href=../../apps/academy/lesson?cid=5" class="btn btn-primary shadow-none">Ver</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { SegaViewer } 