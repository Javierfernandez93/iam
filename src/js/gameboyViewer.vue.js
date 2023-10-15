import { User } from './user.module.js?t=4'

const GameboyViewer = {
    name : 'gameboy-viewer',
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
                        <h4>App para jugar</h4>
                        <p>Esta app sirve para jugar tus juegos</p>
                        <span class="badge bg-secondary">Peso apróximado 6.7mb</span>
                    </div>
                    <div class="col-auto">
                        <div class="d-grid"> 
                            <a href="https://zuum.link/EmulatorGames" download class="btn btn-primary shadow-none">Descargar</a>
                        </div>
                        <div class="d-grid"> 
                            <button @click="sendForWhatsApp('Descarga la app para correr los Juegos','https://zuum.link/EmulatorGames')" class="btn btn-success mb-0 shadow-none">Envíar por WhatsApp</button>
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
                        <h4>App para mover archivos</h4>
                        <p>Esta app sirve para mover archivos</p>
                        <span class="badge bg-secondary">Peso apróximado 7.5mb</span>
                    </div>
                    <div class="col-auto">
                        <div class="d-grid"> 
                            <a href="https://zuum.link/AppFileManager" download class="btn btn-primary shadow-none">Descargar</a>
                        </div>
                        <div class="d-grid"> 
                            <button @click="sendForWhatsApp('Descarga la app para mover archivos','https://zuum.link/AppFileManager')" class="btn btn-success mb-0 shadow-none">Envíar por WhatsApp</button>
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
                        <p>Lista de 547 juegos de GameBoy</p>
                        <span class="badge bg-secondary">Peso apróximado 66mb</span>
                    </div>
                    <div class="col-auto">
                        <div class="d-grid"> 
                            <a href="https://zuum.link/GamesGameboy" download class="btn btn-primary shadow-none">Descargar</a>
                        </div>
                        <div class="d-grid"> 
                            <button @click="sendForWhatsApp('Descarga los Juegos de GameBoy','https://zuum.link/GamesGameboy')" class="btn btn-success mb-0 shadow-none">Envíar por WhatsApp</button>
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
                        <h4>Organizador de juegos</h4>
                        <p>Si tienes más de un emulador este archivo te ayudará a organizar los juegos</p>
                        <span class="badge bg-secondary">Peso apróximado 2mb</span>
                    </div>
                    <div class="col-auto">
                        <div class="d-grid"> 
                            <a href="https://zuum.link/GameFiles" download class="btn btn-primary shadow-none">Descargar</a>
                        </div>
                        <div class="d-grid"> 
                            <button @click="sendForWhatsApp('Organiza tus Juegos con este archivo','https://zuum.link/GameFiles')" class="btn btn-success mb-0 shadow-none">Envíar por WhatsApp</button>
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
                        <h4>Tutorial como jugar estos juegos</h4>
                        <p>De la Consola N64 de DummieTrading</p>
                    </div>
                    <div class="col-auto">
                        <div class="d-grid"> 
                            <a href="../../apps/academy/lesson?cid=7" class="btn btn-primary shadow-none">Ver</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { GameboyViewer } 