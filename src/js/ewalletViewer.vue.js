import { User } from '../../src/js/user.module.js?v=2.6.4'   

const EwalletViewer = {
    name : 'ewallet-viewer',
    props: ['ewallet'],
    emits: ['openatm','getewalletqr','openwithdraw','openaddfunds'],
    data() {
        return {
            User: new User,
            lastAddress: null,
            lastWithdraws: null,
            lastTransactions: null,
            viewUSD: true,
            viewKeyPair: false,
            TRANSACTION_TYPES: {
                OUTPUT : 'output',
                INPUT : 'input',
            },
            STATUS: {
                WAITING_FOR_DEPOSIT : 1,
                DEPOSITED : 2
            }
        }
    },
    methods: {
        goToViewTransaction(hash) {            
            window.location.href = `../../apps/blockchain/viewTransaction?txn=${hash}`
        },
        openInNewTab(link) {            
            window.open(link)
        },
        openAtm() {            
            this.$emit('openatm')
        },
        openWithdraw() {            
            this.$emit('openwithdraw')
        },
        openAddFunds() {    
            this.$emit('openaddfunds')
        },
        getLastTransactionsWallet() {   
            this.User.getLastTransactionsWallet({},(response)=>{
                if(response.s == 1)
                {
                    this.lastTransactions = response.lastTransactions
                }
            })
        },
        getLastWithdraws() {   
            this.User.getLastWithdraws({},(response)=>{
                if(response.s == 1)
                {
                    this.lastWithdraws = response.lastWithdraws
                } else {
                    this.lastWithdraws = false
                }
            })
        },
        goToConfigureWithdrawMethods(hash) {            
            window.location.href = `../../apps/ewallet/withdrawMethods`
        },
        goToViewPublicKey(public_key) {            
            window.location.href = `../../apps/blockchain/viewAddress?address=${public_key}`
        },
        copyPublicKey(public_key,event) {            
            navigator.clipboard.writeText(public_key).then(() => {
                event.target.innerText = 'Copiado'
            });
        },
        copyPrivateKey(key_pair,event) {            
            navigator.clipboard.writeText(key_pair).then(() => {
                event.target.innerText = 'Copiado'
            });
        },
        getEwalletQr() {    
            this.$emit('getewalletqr')
        },
        getLastAddress() {         
            this.User.getLastAddress({},(response)=>{
                
                if(response.s == 1)
                {
                    this.lastAddress = response.lastAddress
                }
            })
        },
    },
    updated() {
    },
    mounted() 
    {       
        this.getLastTransactionsWallet()
        this.getLastAddress()
        this.getLastWithdraws()
    },
    template : `
        <div v-if="ewallet" class="row">
            <div class="col-xl-5 mb-xl-0 mb-4">
                <div class="card bg-transparent shadow-xl">
                    <div
                        class="overflow-hidden position-relative border-radius-xl"
                        style="background-image: url('../../src/img/curved-images/curved14.jpg')"
                        >
                        <span class="mask bg-gradient-dark"></span>
                        <div class="card-body position-relative z-index-1 p-3">
                            <div class="row">
                                <div class="col">
                                    <i class="fas fa-wifi text-white p-2" aria-hidden="true"></i>
                                </div>
                                <div class="col-auto">
                                    <img src="../../src/img/logo-white.svg" style="width:107px">
                                </div>
                            </div>
                            
                            <div class="mt-4 mb-5">
                                <h5 class="text-white text-uppercase pb-2">Balance</h5>
                                <div class="row">
                                    <div class="col">
                                        <h5 class="text-white text-uppercase pb-2">$ {{ewallet.amount.numberFormat(2)}} USD </h5>
                                    </div>
                                    <div class="col-auto">
                                        <button 
                                            @click="getEwalletQr"
                                            class="btn btn-success btn-xs px-3 me-1" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Recibir" aria-label="Enviar"><i class="bi bi-arrow-90deg-down"></i></button>
                                        <button
                                            @click="openAtm"
                                            class="btn btn-danger btn-xs px-3 me-1" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Enviar" aria-label="Recibir"><i class="bi bi-arrow-90deg-up"></i></button>

                                        <button
                                            @click="openWithdraw"
                                            class="btn btn-primary btn-xs px-3 me-1" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Retirar" aria-label="Retirar"><i class="bi bi-currency-exchange"></i></button>

                                        <button
                                            @click="openAddFunds"
                                            class="btn btn-light btn-xs px-3" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Fondear" aria-label="Fondear"><i class="bi bi-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex">
                                <div class="d-flex">
                                    <div class="me-4">
                                        <p class="text-white text-sm opacity-8 mb-0">Card Holder</p>
                                        <h6 class="text-white mb-0">{{ewallet.holder}}</h6>
                                    </div>
                                    <div>
                                        <p class="text-white text-sm opacity-8 mb-0">Expires</p>
                                        <h6 class="text-white mb-0">11/29</h6>
                                    </div>
                                </div>
                                <div class="ms-auto w-20 d-flex align-items-end justify-content-end">
                                    <img class="w-60 mt-2" src="../../src/img/logos/mastercard.png" alt="logo" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-7">
                <div class="alert text-white alert-info">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-white">Cómo retirar dinero DummieTrading</div>
                        </div>
                        <div class="col-auto">
                            <a href="https://zuum.link/FondosDummieTrading" target="_blank" class="btn mb-0 shadow-none btn-primary">Ver video</a>
                        </div>
                    </div>
                </div>
                <div class="card mb-3 border-radius-xl overflow-hidden">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-9">
                                <div>
                                    <span class="badge text-secondary p-0">Dirección</span>
                                </div>
                                <div>
                                    <span class="fw-semibold">
                                        {{ewallet.public_key}}
                                    </span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <button 
                                    @click="copyPublicKey(ewallet.public_key,$event)"
                                    class="btn btn-light shadow-none btn-xs px-3 mb-0" data-bs-toggle="tooltip" data-bs-placement="top" title="copiar"><i class="bi bi-clipboard"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-danger border-0 text-white alert-dismissible fade show" role="alert">
                    <strong>Aviso</strong> 
                    La dirección o llave pública preeviamente señalada es única y exclusivamente para uso interno en DummieTrading. No envíes ni recibas crypto fuera de DummieTrading.</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <div class="alert alert-success border-0 text-white alert-dismissible fade show" role="alert">
                    <strong>Aviso</strong> 
                    <div class="row align-items-center">
                        <div class="col">
                            Ya puedes retirar dinero, configura tus métodos de retiro de dinero.
                        </div>
                        <div class="col-auto">
                            <button @click="goToConfigureWithdrawMethods" class="btn ms-1 px-3 mb-0 btn-outline-light">Configurar métodos</button>
                        </div>
                    </div>

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
            <div class="col-xl-4 mt-3">
                <div class="card overflow-hidden border-radius-xl">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <div class="fw-semibold"><i class="bi bi-clock"></i> Transacciones recientes</div>
                            </div>
                            <div class="col-auto d-none">
                                <button type="button" class="btn btn-link m-0 p-0">Ver todas</button>
                            </div>
                        </div>
                    </div>
                    <div v-if="lastTransactions">
                        <ul class="list-group list-group-flush">
                            <li v-for="lastTransaction in lastTransactions" class="list-group-item bg-primary-soft list-group-item-action f-zoom-element-sm">
                                <div class="row align-items-center">
                                    <div class="col-9">
                                        <div>
                                            <span class="badge p-0 text-secondary">Hash</span>
                                        </div>
                                        <div 
                                            @click="goToViewTransaction(lastTransaction.hash)" class="cursor-pointer text-truncate">
                                            <u>
                                                {{lastTransaction.hash}}
                                            </u>
                                        </div>
                                    </div>
                                    <div class="col-3 text-end text-truncate">
                                        <div>
                                            <span v-if="lastTransaction.transactionData.type == TRANSACTION_TYPES.INPUT" class="badge border border-success text-success text-xxs">
                                                Recibido
                                            </span>
                                            <span v-else-if="lastTransaction.transactionData.type == TRANSACTION_TYPES.OUTPUT" class="badge border border-danger text-danger text-xxs">
                                                Enviado
                                            </span>
                                        </div>
                                        <span v-if="lastTransaction.transactionData.address" class="fw-semibold text-dark text-xs">
                                            {{lastTransaction.transactionData.address.amount.numberFormat(6)}} USD
                                        </span>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 mt-3">
                <div class="card overflow-hidden border-radius-xl">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <div class="fw-semibold"><i class="bi bi-clock"></i> Direcciones recientes</div>
                            </div>
                            <div class="col-auto d-none">
                                <button type="button" class="btn btn-link m-0 p-0">Ver todas</button>
                            </div>
                        </div>
                    </div>
                    <div v-if="lastAddress">
                        <ul class="list-group list-group-flush">
                            <li v-for="lastAddres in lastAddress" class="list-group-item bg-primary-soft list-group-item-action f-zoom-element-sm">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <div>
                                            <span class="badge p-0 text-secondary">Dirección</span>
                                        </div>
                                        <div 
                                            @click="goToViewPublicKey(lastAddres.to_public_key)" class="cursor-pointer text-truncate">
                                            <u>
                                                {{lastAddres.to_public_key}}
                                            </u>
                                        </div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div>
                                            <span v-if="lastAddres.to_public_key == ewallet.public_key" class="badge border border-success text-success text-xxs">
                                                Recibido
                                            </span>
                                            <span v-else class="badge border border-danger text-danger text-xxs">
                                                Enviado
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 mt-3">
                <div class="card overflow-hidden border-radius-xl bg-soft-secondary">
                    <div class="card-body">
                        <div>
                            <div class="fw-semibold"><i class="bi bi-clock"></i> Últimos retiros</div>
                        </div>
                    </div>
                    <div v-if="lastAddress">
                        <ul class="list-group list-group-flush">
                            <li v-for="lastWithdraw in lastWithdraws" class="list-group-item list-group-item-action f-zoom-element-sm bg-transparent">
                                <div class="row align-items-center">    
                                    <div class="col text-truncate">
                                        <div>
                                            <span class="badge text-secondary p-0">Wallet</span>
                                            <div class="fw-semibold text-decoration-underline text-truncate text-xs text-dark">{{lastWithdraw.wallet}}</div>
                                        </div>
                                        <div>
                                            <span class="badge text-secondary p-0">Método</span>
                                            <div class="fw-semibold text-decoration-underline text-truncate text-xs text-dark">{{lastWithdraw.method}} {{lastWithdraw.currency}}</div>
                                        </div>

                                        <div v-if="lastWithdraw.status == STATUS.DEPOSITED" @click="openInNewTab(lastWithdraw.result_data)">
                                            <span class="badge text-secondary p-0">Ver en blockchain</span>
                                            <div class="text-decoration-underline cursor-pointer text-primary text-xs">{{lastWithdraw.result_data}}</div>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div>
                                            <span v-if="lastWithdraw.status == STATUS.WAITING_FOR_DEPOSIT" class="badge border text-warning text-xxs border-warning">
                                                Pendiente
                                            </span>
                                            <span v-else-if="lastWithdraw.status == STATUS.DEPOSITED" class="badge border text-success text-xxs border-success">
                                                Depósitado
                                            </span>
                                        </div>
                                        <span class="fw-semibold text-dark text-xs">
                                            {{lastWithdraw.amount.numberFormat(6)}} USD
                                        </span>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { EwalletViewer } 