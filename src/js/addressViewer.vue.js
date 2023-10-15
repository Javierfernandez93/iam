import { User } from './user.module.js?t=4'

const AddressViewer = {
    name : 'address-viewer',
    data() {
        return {
            User: new User,
            ewallet: null,
        }
    },
    methods: {
        copyPublicKey(public_key,event) {            
            navigator.clipboard.writeText(public_key).then(() => {
                event.target.innerText = 'copiado'
            });
        },
        goToTransaction(hash) {            
            window.location.href = `../../apps/blockchain/transaction?txn=${hash}`
        },
        getEwalletAddressInfo(public_key) {       
            this.User.getEwalletAddressInfo({public_key:public_key},(response)=>{
                if(response.s == 1)
                {
                    this.ewallet = response.ewallet
                }   
            })
        },
    },
    updated() {
    },
    mounted() 
    {       
        if(getParam('address'))
        {
            this.getEwalletAddressInfo(getParam('address'))
        }
    },
    template : `
        <div v-if="ewallet" class="row">
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-11 text-center">
                                <div><img :src="ewallet.link.validateProtocol().getQrCode()" class="w-100"/></div>
                                
                                <div class="text-gradient text-primary">
                                    {{ewallet.public_key}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-8">
                <div class="card overflow-hidden">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-2 text-secondary">
                                    Dirección
                                </div>
                                <div class="col-10 fw-semibold">
                                    {{ewallet.public_key}} <button @click="copyPublicKey(ewallet.public_key,$event)" class="btn btn-light btn-xs" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="copiar" aria-label="copiar"><i class="bi bi-clipboard"></i></button></span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-2 text-secondary">
                                    Formato
                                </div>
                                <div class="col-10 fw-semibold">
                                    <span class="badge bg-soft-success text-success text-uppercase">{{ewallet.format}}</span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-2 text-secondary">
                                    Transacciones
                                </div>
                                <div class="col-10 fw-semibold">
                                    {{ewallet.transactions}}
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-2 text-secondary">
                                    Total enviado
                                </div>
                                <div class="col-10 fw-semibold">
                                    {{ewallet.totalSent.numberFormat(6)}} USD
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-2 text-secondary">
                                    Total recibido
                                </div>
                                <div class="col-10 fw-semibold">
                                    {{ewallet.totalReceived.numberFormat(6)}} USD
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-2 text-secondary">
                                    Balance final
                                </div>
                                <div class="col-10 fw-semibold">
                                    {{ewallet.amount.numberFormat(6)}} USD
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    `,
}

export { AddressViewer } 