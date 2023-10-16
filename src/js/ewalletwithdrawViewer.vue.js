import { User } from '../../src/js/user.module.js?v=2.6.6'   

const EwalletwithdrawViewer = {
    name : 'ewalletwithdraw-viewer',
    emits: ['getewallet'],
    props: ['ewallet'],
    data() {
        return {
            User: new User,
            withdrawMethods : null,
            withdraw: {
                fee: null,
                amount: null,
                withdraw_method_per_user_id: null
            },
            FEE_WITHDRAW_TRANSACTION: 0,
            error: null,
            MIN_AMOUNT_TO_WITHDRAW: 20,
            ERRORS: {
                NOT_WITHDRAWS_METHODS : {
                    code: 1,
                    text: 'No tienes métodos de retiro, por favor configura uno'
                },
                NOT_ENOUGH_FUNDS : {
                    code: 2,
                    text: 'No tienes fondos suficientes'
                },
                NOT_WITHDRAWS_METHOD_CHOICED : {
                    code: 3,
                    text: 'Elige un método de retiro'
                },
                NOT_MINIMUM_AMOUNT : {
                    code: 4,
                    text: `El monto mínimo de retiro es de`
                },
            }
        }
    },
    watch : {
        withdraw: {
            handler() {
                this.error = null

                this.withdraw.fee = (this.withdraw.amount * this.FEE_WITHDRAW_TRANSACTION) / 100

                const tempAmount = parseFloat(this.withdraw.amount) + parseFloat(this.withdraw.fee)

                if(this.ewallet.amount >= tempAmount)
                {
                    if(this.withdraw.amount >= this.MIN_AMOUNT_TO_WITHDRAW)
                    {
                        if(this.withdraw.withdraw_method_per_user_id != null)
                        {
                            if(this.withdrawMethods != false)
                            {

                            } else {
                                this.error = this.ERRORS.NOT_WITHDRAWS_METHODS
                            }
                        } else {
                            this.error = this.ERRORS.NOT_WITHDRAWS_METHOD_CHOICED
                        }
                    } else {
                        this.error = this.ERRORS.NOT_MINIMUM_AMOUNT
                    }
                } else {
                    this.error = this.ERRORS.NOT_ENOUGH_FUNDS
                }
            },
            deep: true
        }
    },
    methods: {
        goToTransaction: function(hash) {            
            window.location.href = `../../apps/blockchain/transaction?txn=${hash}`
        },
        goToConfigureWithdrawMethods: function(hash) {            
            window.location.href = `../../apps/ewallet/withdrawmethods`
        },
        sendEwalletFunds: function() {            
            this.User.sendEwalletFunds(this.withdraw,(response)=>{
                if(response.s == 1)
                {
                    this.$emit('getewallet')

                    $(this.$refs.offcanvasRight).offcanvas('hide')
                }
            })
        },
        openWithdraw: function() {     
            this.openOffCanvas()
        },
        openOffCanvas: function() {     
            $(this.$refs.offcanvasRight).offcanvas('show')
        },
        filterWithdrawMethods: function (withdrawMethods) {
            return new Promise((resolve, reject) => { 
                withdrawMethods = withdrawMethods.filter((withdrawMethod)=>{
                    return withdrawMethod['wallet'] != false
                })

                if(withdrawMethods.length > 0)
                {
                    resolve(withdrawMethods)
                } else {
                    reject()   
                }
            })
        },
        getWithdrawsMethods: function () {
            return new Promise((resolve, reject) => {
                this.User.getWithdrawsMethods({  }, (response) => {
                    if (response.s == 1) {
                        resolve(response)
                    }

                    reject()
                })
            })
        },
        getTransactionWithdrawFee: function() {            
            this.User.getTransactionWithdrawFee({},(response)=>{
                
                if(response.s == 1)
                {
                    this.FEE_WITHDRAW_TRANSACTION = response.fee_withdraw
                }
            })
        },
        withdrawFunds: function () {
            this.User.withdrawFunds(this.withdraw, (response) => {
                if (response.s == 1) {
                    this.$emit('getewallet')

                    $(this.$refs.offcanvasRight).offcanvas('hide')
                }

            })
        },
    },
    mounted() 
    {       
        this.getTransactionWithdrawFee()

        this.getWithdrawsMethods().then((response)=>{
            this.withdraw.fee = response.fee

            this.filterWithdrawMethods(response.withdrawMethods).then((withdrawMethodsFiltered)=>{
                this.withdrawMethods = withdrawMethodsFiltered
            }).catch(() => { 
                this.withdrawMethods = false
                this.error = this.ERRORS.NOT_WITHDRAWS_METHODS
            })
        }).catch(() => { 
            
        })
    },
    template : `
        <div class="offcanvas offcanvas-end" tabindex="-1" ref="offcanvasRight" id="offcanvasRight" aria-labelledby="offcanvasWithBackdropLabel">
            <div>
                <div class="offcanvas-header">
                    <h5 id="offcanvasRightLabel">
                        <div>
                            <t>Retirar USD</t>
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
                                    v-model="withdraw.amount"
                                    :class="error != ERRORS.NOT_ENOUGH_FUNDS && withdraw.amount > 0 ? 'is-valid' : ''"
                                    type="number" class="form-control" id="address" placeholder="Cantidad a retirar">
                                <label for="address">Cantidad a retirar</label>
                            </div>

                            <div v-if="withdrawMethods" class="form-floating mb-3">
                                <select class="form-select" v-model="withdraw.withdraw_method_per_user_id" aria-label="Selecciona tu método de retiro">
                                    <option v-for="withdrawMethod in withdrawMethods" v-bind:value="withdrawMethod.withdraw_method_per_user_id">
                                        {{ withdrawMethod.method }} - {{ withdrawMethod.wallet }} ({{ withdrawMethod.catalog_currency.currency }})
                                    </option>
                                </select>
                                <label for="floatingSelect">Selecciona tu método de retiro</label>
                            </div>

                            <div class="form-floating mb-3 text-center">
                                <span class="badge text-secondary p-0">Cantidad + (fee $ {{FEE_WITHDRAW_TRANSACTION.numberFormat(2)}}%) </span>
                                <div class="fw-semibold text-dark">
                                    $ {{(parseFloat(withdraw.amount)+parseFloat(withdraw.fee)).numberFormat(2)}} USD
                                </div>
                            </div>

                            <div v-if="error" class="alert alert-danger text-white">
                                <strong>Aviso</strong> - {{error.text}}

                                <span v-if="error == ERRORS.NOT_MINIMUM_AMOUNT">$ {{MIN_AMOUNT_TO_WITHDRAW.numberFormat(2)}}</span>

                                <div v-if="error == ERRORS.NOT_WITHDRAWS_METHODS" class="mt-3">
                                    <button class="btn btn-light mb-0" @click="goToConfigureWithdrawMethods">Ir a configurar método</button>
                                </div>
                            </div>
                            <div v-else class="alert alert-success text-white">
                                <div v-if="withdraw.fee > 0">
                                    <strong>Aviso</strong> Fee de la transacción $ {{withdraw.fee.numberFormat(2)}} USDT
                                </div>
                            </div>

                            <button 
                                :disabled="error != null"
                                @click="withdrawFunds"
                                class="btn btn-primary shadow-none waves-effect waves-light">
                                Retirar fondos
                            </button>
                        </div>    
                    </div>    
                </div>
            </div>
        </div>
    `,
}

export { EwalletwithdrawViewer } 