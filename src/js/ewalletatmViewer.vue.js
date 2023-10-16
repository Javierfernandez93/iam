import { User } from '../../src/js/user.module.js?v=2.6.5'   

const EwalletatmViewer = {
    name : 'ewallet-viewer',
    emits: ['getewallet'],
    props: ['ewallet'],
    data() {
        return {
            User: new User,
            isEwalletFilled: false,
            sending: false,
            transactions: null,
            error: null,
            FEE_INTERNAL_TRANSACTION: 0,
            errors: {
                INVALID_ADDRESS_LENGHT : {
                    code: 1,
                    text: 'La dirección de destino no cumple con la cantidad de caracteres necesarios'
                },
                ADDRESS_NOT_EXIST : {
                    code: 2,
                    text: 'La dirección de destino no existe'
                },
                NOT_ENOUGH_FUNDS : {
                    code: 3,
                    text: 'No tienes fondos suficientes'
                },
                SAME_ADDRESS : {
                    code: 4,
                    text: 'La dirección de destino y la llave pública son las mismas'
                },
                NOT_MIN_AMOUNT : {
                    code: 5,
                    text: 'Debes de mandar al menos $5 usd'
                }
            }
        }
    },
    watch : {
        ewallet: {
            handler() {
                this.error = null
                
                this.ewallet.fee = (this.ewallet.amountToSend * this.FEE_INTERNAL_TRANSACTION) / 100

                const amountTemp = parseFloat(this.ewallet.amountToSend) + parseFloat(this.ewallet.fee)

                if(this.ewallet.recipientAdress.length == this.ewallet.addressLenght)
                {
                    if(this.ewallet.public_key != this.ewallet.recipientAdress)
                    {
                        if(this.ewallet.amount >= amountTemp)
                        {

                        } else {
                            this.error = this.errors.NOT_ENOUGH_FUNDS
                        }
                    } else {
                        this.error = this.errors.SAME_ADDRESS
                    }
                } else {
                    this.error = this.errors.INVALID_ADDRESS_LENGHT
                }
            },
            deep: true
        }
    },
    methods: {
        goToTransaction: function(hash) {            
            window.location.href = `../../apps/blockchain/transaction?txn=${hash}`
        },
        sendEwalletFunds: function() {    
            if(!this.sending)
            {
                this.sending = true    

                this.User.sendEwalletFunds({recipientAdress:this.ewallet.recipientAdress,amountToSend:this.ewallet.amountToSend,message:this.ewallet.message},(response)=>{
                    this.sending = false        
                    
                    if(response.s == 1)
                    {
                        this.$emit('getewallet')
    
                        $(this.$refs.offcanvasRight).offcanvas('hide')
                    } else if(response.r == "NOT_AMOUNT_TO_SEND") {
                        alertMessage('Ingresa una cantidad válida')
                    }
                })
            }
        },
        getTransactionFee: function() {            
            this.User.getTransactionFee({},(response)=>{
                
                if(response.s == 1)
                {
                    this.FEE_INTERNAL_TRANSACTION = response.fee_transaction
                }
            })
        },
        openAtm: function() {     
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
        this.getTransactionFee()
    },
    template : `
        <div class="offcanvas offcanvas-end" tabindex="-1" ref="offcanvasRight" id="offcanvasRight" aria-labelledby="offcanvasWithBackdropLabel">
            <div>
                <div class="offcanvas-header">
                    <h5 id="offcanvasRightLabel">
                        <div>
                            <t>Enviar USD</t>
                        </div>
                    </h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div v-if="ewallet" class="offcanvas-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="badge bg-gradient-warning fs-5 shadow-lg">
                                        <i class="bi bi-currency-exchange text-white"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div>
                                        <span class="badge text-secondary p-0">Balance</span>
                                    </div>
                                    <div class="fs-5 fw-semibold text-dark">
                                        $ {{ewallet.amount.numberFormat(2)}} USD
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-body">
                            <div class="form-floating mb-3">
                                <input 
                                    v-model="ewallet.recipientAdress"
                                    :class="error != errors.INVALID_ADDRESS_LENGHT || error != errors.SAME_ADDRESS && ewallet.recipientAdress != null ? 'is-valid' : ''"
                                    type="text" class="form-control" id="address" placeholder="Dirección destino">
                                <label for="address">Dirección destino</label>
                            </div>
                            
                            <div class="form-floating mb-3">
                                <input 
                                    v-model="ewallet.amountToSend"
                                    :class="error != errors.NOT_ENOUGH_FUNDS && ewallet.amountToSend > 0 ? 'is-valid' : ''"
                                    type="number" class="form-control" id="address" placeholder="Cantidad a enviar">
                                <label for="address">Cantidad</label>
                            </div>
                            
                            <div class="form-floating mb-3 text-center">
                                <span class="badge text-secondary p-0">Cantidad + (fee $ {{FEE_INTERNAL_TRANSACTION.numberFormat(2)}}%) </span>
                                <div class="fw-semibold text-dark">
                                    $ {{(parseFloat(ewallet.amountToSend)+parseFloat(ewallet.fee)).numberFormat(2)}} USD
                                </div>
                            </div>

                            <div class="form-floating mb-3">
                                <input 
                                    v-model="ewallet.message"
                                    @keydown.enter.exact.prevent="sendEwalletFunds"
                                    type="text" class="form-control" id="message" placeholder="Mensaje adicional">
                                <label for="message">Mensaje adicional</label>
                            </div>

                            <div v-if="error" class="alert alert-danger text-white">
                                <strong>Aviso</strong> ERROR {{error.code}} - {{error.text}}
                            </div>
                            <div v-if="error" class="alert alert-success text-white">
                                <strong>Aviso</strong> El fee por transacción interna es de {{FEE_INTERNAL_TRANSACTION.numberFormat(2)}} %
                            </div>

                            <button 
                                :disabled="error != null || sending"
                                @click="sendEwalletFunds"
                                class="btn btn-primary waves-effect waves-light">
                                <span v-if="sending">Enviando...</span>
                                <span v-else>Enviar</span>
                                
                            </button>
                        </div>    
                    </div>    
                </div>
            </div>
        </div>
    `,
}

export { EwalletatmViewer } 