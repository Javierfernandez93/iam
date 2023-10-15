import { User } from '../../src/js/user.module.js?t=1.1.4'   

const BrokersViewer = {
    name : 'brokers-viewer',
    data() {
        return {
            User: new User,
            brokers: null,
            brokersAux: null,
            query: null,
        }
    },
    watch: {
        query : {
            handler() {
                this.filterData()
            },
            deep: true
        }
    },
    methods: {
        filterData() {
            this.brokers = this.brokersAux
            this.brokers = this.brokers.filter((broker)=>{
                return broker.broker.toLowerCase().includes(this.query.toLowerCase()) || broker.description.toLowerCase().includes(this.query.toLowerCase())
            })
        },
        getBrokers() {
            this.User.getBrokers({},(response)=>{
                if(response.s == 1)
                {
                    this.brokers = response.brokers
                    this.brokersAux = response.brokers
                }
            })
        },
    },
    mounted() 
    {   
        this.getBrokers()
    },
    template : `
        <div v-if="brokers" class="container">
            <div class="row align-items-center animation-fall-down" style="--delay:500ms">
                <div class="col-12 col-xl">
                    <h2 class="mb-3">Brokers y exchanges recomendados</h2>
                    <div class="mb-3">
                        Un broker de forex (también conocido como corredor de divisas o agente de divisas) es una entidad financiera o una empresa que facilita el comercio de divisas en el mercado de divisas (forex).
                    </div>
                    <div class="mb-3">
                    Una "exchange" o "plataforma de intercambio" se refiere a un sitio web o plataforma en línea donde los usuarios pueden comprar, vender o intercambiar diversos activos digitales, como criptomonedas, tokens y otros tipos de activos digitales
                    </div>
                    <div class="">Te brindamos las mejores opciones para que puedas regístrarte y comenzar a operar en Forex y en exchanges.</div>
                </div>
                <div class="col-12 col-xl-auto">
                    <lottie-player src="../../src/files/json/lottie.json" background="transparent"  speed="1"  mode=“normal” loop autoplay style="width: 400px; height: 400px;"></lottie-player>
                </div>
            </div>
            <div class="d-flex justify-content-center my-3 animation-fall-down" style="--delay:700ms">
                <span class="my-5 border-top border-secondary w-50 d-flex"></span>
            </div>
            <div v-if="brokers" class="row animation-fall-down" style="--delay:900ms">
                <div class="row justify-content-center">
                    <div class="col-12 col-xl-4">
                        <input v-model="query" type="text" class="form-control form-control-lg" placeholder="Buscar broker"/>
                    </div>
                </div>

                <div v-for="broker in brokers" class="col-12 col-md-4 col-xl-4 mt-5">
                    <div class="card overflow-hidden">
                        <span class="mask bg-primary-dark"></span>
                        <div class="position-relative z-index-1">
                            <div class="card-body">
                                <h3 class="text-white">
                                    {{broker.broker}}
                                </h3>

                                <div v-if="broker.market" class="mb-3">
                                    <span v-for="market in broker.market" class="badge me-2 text-primary bg-light">
                                        {{market}}
                                    </span>
                                </div>
                                <div class="text-white mb-3">
                                    {{broker.description}}
                                </div>
                            </div>
                            <div class="card-footer pb-0">
                                <div class="d-grid">
                                    <a :href="broker.signup_url" target="_blank" class="btn btn-dark">Regístrarme</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { BrokersViewer } 