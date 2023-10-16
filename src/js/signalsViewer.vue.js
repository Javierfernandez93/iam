import { User } from '../../src/js/user.module.js?v=2.6.5'   

const SignalsViewer = {
    name : 'signals-viewer',
    emit : ['openCanvas'],
    data() {
        return {
            User : new User,
            channels : null,
        }
    },
    methods: {
        getChannelsPerUser() {
            return new Promise((resolve, reject) => {
                this.User.getChannelsPerUser({},(response)=>{
                    if(response.s == 1)
                    {
                        resolve(response.channels)  
                    }

                    reject()
                })
            })
        }
    },
    mounted() {
        this.getChannelsPerUser().then((channels)=>{
            this.channels = channels
        }).catch((err)=>{
            this.channels = false
        })
    },
    template : `
        <div v-if="channels">
            <div class="card blur shadow-blur shadow-none mb-3">
                <div class="card-header bg-transparent">
                    <div class="row">
                        <div class="row justify-content-center align-items-center">
                            <div class="col-12 col-xl fs-4 fw-semibold text-primary">
                                Lista de Señales
                            </div>
                            <div class="col-12 col-xl-auto">
                                <a href="../../apps/signals/tradingView" class="btn mb-0 btn-outline-primary">Abrir Trading View</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <input type="search" v-model="query" class="form-control" placeholder="Buscar...">
                </div>
            </div>
            <div v-for="channel in channels" class="card shadow-blur blur card-body mb-3">
                <div class="row align-items-center">
                    <div class="col-12 col-xl">
                        <div>
                            <span class="badge bg-primary">{{channel.followers}} seguidores</span>
                        </div>
                        <span class="fs-5 fw-semibold text-primary">
                            {{channel.name}}
                        </span>
                    </div>
                    <div class="col-12 col-xl-auto">
                        <div class="d-grid">
                            <button @click="$emit('openCanvas',channel)" class="btn d-none btn-primary mb-0">Enviar señal grupo</button>
                            <button @click="$emit('openCanvas',channel)" class="btn btn-primary mb-0">Enviar señal</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else-if="channels == false">
            sin canales
        </div>
    `,
}

export { SignalsViewer } 