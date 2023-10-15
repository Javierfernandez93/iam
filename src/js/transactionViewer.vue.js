import { User } from './user.module.js?t=4'

const TransactionViewer = {
    name : 'transaction-viewer',
    data() {
        return {
            User: new User,
            transaction: null,
        }
    },
    methods: {
        goToViewAddress(address) {            
            window.location.href = `../../apps/blockchain/viewAddress?address=${address}`
        },
        getTransactionInfo(hash) {          
            this.User.getTransactionInfo({hash:hash},(response)=>{
                if(response.s == 1)
                {
                    this.transaction = response.transaction
                }   
            })
        },
    },
    mounted() 
    {       
        if(getParam('txn'))
        {
            this.getTransactionInfo(getParam('txn'))
        }
    },
    template : `
        <div v-if="transaction" class="row">
            <div class="col-12 mb-3">
                <div class="card overflow-hidden">
                    <div class="card-header bg-white">
                        Detalles
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-2 text-secondary">
                                    Fees
                                </div>
                                <div class="col-8 fw-semibold">
                                    {{transaction.fees.numberFormat(6)}} USD
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-2 text-secondary">
                                    Hash
                                </div>
                                <div class="col fw-semibold">
                                    {{transaction.hash}}
                                </div>
                                <div class="col-auto fw-semibold">
                                    {{transaction.unix_date.formatFullDate()}}
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="row">
                                        <div class="col text-truncate">
                                            <u class="text-primary cursor-pointer" @click="goToViewAddress(output.input.address)">{{transaction.input.address}}</u>
                                        </div>
                                        <div class="col-auto">
                                            {{transaction.input.amount.numberFormat(6)}} USD 
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto text-center fs-5">
                                    <span class="badge bg-soft-primary">
                                        <i class="bi bi-arrow-90deg-right text-primary"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div v-for="output in transaction.output" class="row">
                                        <div class="col text-truncate">
                                            <u class="text-primary cursor-pointer" @click="goToViewAddress(output.address)">{{output.address}}</u>
                                        </div>
                                        <div class="col-auto text-end">
                                            {{output.amount.numberFormat(6)}} USD 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-12">
                <div class="card overflow-hidden">
                    <div class="card-header bg-white">
                        Detalles
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-2 text-secondary">
                                    Hash
                                </div>
                                <div class="col-8 fw-semibold">
                                    {{transaction.hash}}
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-2 text-secondary">
                                    Estatus
                                </div>
                                <div class="col-8 fw-semibold">
                                    Confirmada
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-2 text-secondary">
                                    Tiempo 
                                </div>
                                <div class="col-8 fw-semibold">
                                    {{transaction.unix_date.formatFullDate()}}
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-2 text-secondary">
                                    Tamaño 
                                </div>
                                <div class="col-8 fw-semibold">
                                    {{transaction.size.numberFormat(0)}} bytes
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-2 text-secondary">
                                    Peso
                                </div>
                                <div class="col-8 fw-semibold">
                                    {{transaction.weight.numberFormat(0)}}
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-2 text-secondary">
                                    Total input
                                </div>
                                <div class="col-8 fw-semibold">
                                    {{transaction.totalInput.numberFormat(6)}} USD
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-2 text-secondary">
                                    Total output
                                </div>
                                <div class="col-8 fw-semibold">
                                    {{transaction.totalOutput.numberFormat(6)}} USD
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-2 text-secondary">
                                    Fees
                                </div>
                                <div class="col-8 fw-semibold">
                                    {{transaction.fees.numberFormat(6)}} USD
                                </div>
                            </div>
                        </li>
                        <li v-if="transaction.data" class="list-group-item">
                            <div class="row">
                                <div class="col-2 text-secondary">
                                    Data
                                </div>
                                <div class="col-8 fw-semibold">
                                    <span v-for="(data, index) in transaction.data" 
                                        class="badge bg-soft-primary text-primary mx-1">
                                        {{index.replace('@opt', '')}} : {{data}} 
                                    </span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    `,
}

export { TransactionViewer } 