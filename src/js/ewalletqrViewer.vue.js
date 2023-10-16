
import { User } from '../../src/js/user.module.js?v=2.6.6'   

const EwalletqrViewer = {
    name : 'ewalletqr-viewer',
    props: ['ewallet'],
    data() {
        return {
            User: new User,
        }
    },
    methods: {
        goToTransaction: function(hash) {            
            window.location.href = `../../apps/blockchain/transaction?txn=${hash}`
        },
        sendEwalletFunds: function() {                        
            this.User.sendEwalletFunds({recipientAdress:this.ewallet.recipientAdress,amountToSend:this.ewallet.amountToSend,message:this.ewallet.message},(response)=>{                
                if(response.s == 1)
                {
                    this.$emit('getewallet')

                    $(this.$refs.offcanvasRight).offcanvas('hide')
                }
            })
        },
        getEwalletQr: function() {     
            this.openOffCanvas()
        },
        openOffCanvas: function() {     
            $(this.$refs.offcanvasRight).offcanvas('show')
        },
    },
    updated() {
    },
    mounted() 
    {       
    },
    template : `
        <div class="offcanvas offcanvas-end" tabindex="-1" ref="offcanvasRight" id="offcanvasRight" aria-labelledby="offcanvasWithBackdropLabel">
            <div>
                <div class="offcanvas-header">
                    <h5 id="offcanvasRightLabel">
                        <div>
                            <t>Recibir USD</t>
                        </div>
                    </h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div v-if="ewallet.link" class="offcanvas-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="alert alert-light">
                                <strong>Aviso</strong>
                                Para recibir dinero pide que escaneen el código QR a continuación o bien comparte tu dirección de ewallet
                            </div>
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
            </div>
        </div>
    `,
}

export { EwalletqrViewer } 