import { User } from '../../src/js/user.module.js?v=2.6.4'   

const WelcomeViewer = {
    name : 'welcome-viewer',
    data() {
        return {
            User: new User,
            profile: null,
            phrase: null,
            phrases: ["Bienvenido de nuevo a tu familia I AM Beauty","Bienvenido de nuevo a tu familia I AM Beauty","¡Es un placer tenerte con nosotros!","Bienvenido de nuevo","¡Estamos felices de verte aquí!","¡Tu presencia aquí nos alegra el día!","¡Gracias por unirte a nuestra familia en línea!","Estamos emocionados de verte de nuevo"],
        }
    },
    methods: {
        getPhrase() {          
            this.phrase = this.phrases[Math.floor(Math.random() * this.phrases.length)];
        },
        getProfileShort() {          
            this.User.getProfileShort({},(response)=>{
                if(response.s == 1)
                {
                    this.profile = response.profile
                }
            })
        },
    },
    mounted() 
    {   
        this.getProfileShort()
        this.getPhrase()
    },
    template : `
        <div v-if="profile" class="card card-body bg-transparent shadow-none text-white py-5">
            <div class="row gx-5 align-items-center justify-content-center">
                <div class="col-12 col-xl-auto animation-fall-down" style="--delay:150ms">
                    <div class="avatar">
                        <img class="bg-white rounded-circle avatar avatar-xl" :src="profile.image" alt="user" title="user" />
                    </div>
                </div>
                <div class="col-12 col-xl">
                    <div class="fs-1 mb-n3 fw-semibold animation-fall-down" style="--delay:250ms">Hola, {{profile.names}} </div>
                    <div v-if="phrase" class="fs-4 animation-fall-down" style="--delay:450ms">{{phrase}}</div>
                </div>
                <div class="col-12 col-xl-auto animation-fall-down" style="--delay:350ms">
                    <div class="d-grid mb-1">
                        <a href="../../apps/store/package" class="btn btn-outline-light mb-0">Comprar</a>
                    </div>
                    <div class="d-grid">
                        <a href="../../apps/store/invoices" class="btn btn-light shadow-none mb-0">Ver mis compras</a>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { WelcomeViewer } 