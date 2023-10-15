import { User } from '../../src/js/user.module.js?v=2.5.6'   
import { Player } from '../../src/js/player.module.js?v=2.5.6'   
import { io } from "../../src/js/socket.io.js?v=2.5.6";

const GameViewer = {
    name : 'game-viewer',
    data() {
        return {
            User : new User,
            Player : new Player,
            socket : io('http://localhost:3000/'),
            busy : false,
            players : false,
            room : null,
            profile: null,
            state: null,
            interval: null,
            ACTIONS: {
                DOWN : 0,
                UP : 1,
            },
            STATES: {
                WAITING : 0,
                PLAYING : 1,
                FINISHED : 2
            }
        }
    },
    methods: {
        startCounter() {
            this.interval = setInterval(()=>{
                let timeleft = new Date(this.room.expirationDate).getTime() - new Date().getTime();

                const msPerSecond = 1000;
                const msPerMinute = msPerSecond * 60;
                const msPerHour = msPerMinute * 60;
                const msPerDay = msPerHour * 24;

                const days = Math.floor(timeleft / msPerDay);
                const hours = Math.floor((timeleft % (1000 * 60 * 60 * 24)) / msPerHour);
                const minutes = Math.floor((timeleft % (1000 * 60 * 60)) / msPerMinute);
                const seconds = Math.floor((timeleft % (1000 * 60)) / msPerSecond);
                const totalSeconds =  seconds + (minutes * 60) + (hours * 60 * 60) + (days * 24 * 60 * 60)

                this.room.timeLeft = {
                    days : days,
                    hours : hours,
                    minutes : minutes,
                    seconds : seconds,
                    totalSeconds : totalSeconds
                }

                if(totalSeconds <= 0) 
                {
                    clearInterval(this.interval)

                    this.interval = null
                }
            },1000)
        },
        getDiff(time) {
            let difference = new Date(time).getTime() - new Date().getTime();

            return Math.floor((difference % (1000 * 60)) / 1000);
        },
        setAction(ACTION) {
            this.Player.ACTION = ACTION;

            this.socket.emit('setAction',{
                id : this.room.id,
                playerAction : {
                    action : this.Player.ACTION,
                    id : this.Player.getId()
                }
            })
        },
        searchRoom() {
            this.busy = true 

            this.socket.emit('searchRoom', this.Player.getId())
        },
        async getProfileShort() {
            return new Promise((resolve, reject) => {
                this.User.getProfileShort({},(response)=>{
                    if(response.s == 1)
                    {
                        resolve(response.profile)
                    }
                })
            })
        },
    },
    async mounted() {
        this.profile = await this.getProfileShort()

        this.Player.setUserName(this.profile.names)
        this.Player.setPlayerId(this.profile.company_id)

        this.socket.emit('setPlayerAsConnected',{
            userName: this.Player.getUserName(),
            playerId: this.Player.getPlayerId()
        })

        this.socket.on('updatePlayersInRoom',(room)=>{
            this.room = room
            console.log("updatingPlayersInRoom",room)
        })

        this.socket.on('updatePlayers',(data)=>{
            this.players = data.players
        })
        
        this.socket.on('updatePlayer',(player)=>{
            this.Player.setId(player.id)
        })
        
        this.socket.on('gameStarted',(room)=>{
            this.room.STATE = room.STATE
            this.room.expirationDate = room.expirationDate
            
            this.startCounter()
        })
        
        this.state = this.STATES.WAITING
    },
    template : `
        <div v-if="busy">
            Buscando partida...
        </div>
        
        <div class="row">
            <div class="col-12 col-xl-8">
                {{Player}}
                <button @click="searchRoom" class="btn btn-neutral">Conectar</button>
            </div>
        </div>

        <div v-if="room">
            {{room.STATE}}
            
            <div v-if="room.STATE == STATES.PLAYING" class="row">
                <div class="col-12 col-xl">
                    <div class="d-grid">
                        <button @click="setAction(ACTIONS.UP)" class="btn btn-lg btn-success">Subir</button>
                    </div>
                </div>
                <div class="col-12 col-xl">
                    <div class="d-grid">
                        <button @click="setAction(ACTIONS.DOWN)" class="btn btn-lg btn-danger">Bajar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-xl">
                <div v-if="room">
                    <h2>room {{room.id}}</h2>
                    <h4>state {{room.STATE}}</h4>
                    
                    <div v-if="room.timeLeft">
                        Decide antes de {{room.timeLeft.totalSeconds}} segundos
                    </div>

                    <ul class="list-group">
                        <li v-for="player in room.players" class="list-group-item">
                            {{player}}
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-12 col-xl">
                <div v-if="players" class="card">
                    <div class="card-header">
                        <h2>
                            {{players.length}} Jugadores
                        </h2>
                    </div>
                    <div>
                        <ul class="list-group">
                            <li v-for="player in players" class="list-group-item">
                                {{player.id}}
                                <div>
                                    {{player.playerId}}
                                </div>
                                <div>
                                    {{player.userName}}
                                </div>
                                
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { GameViewer } 