import { User } from '../../src/js/user.module.js?v=2.6.6'   

const LastordersViewer = {
    name : 'lastorders-viewer',
    data() {
        return {
            User: new User,
            query : null,
            buys : null,
            buysAux : null,
            STATUS : {
                PENDING: 1,
                DELETED: -1,
                VERIFIED: 2,
            }
        }
    },
    watch: {
        query: {
            handler() {
                this.filterData()
            },
            deep: true
        }
    },
    methods: {
        filterData() {    
            this.buys = this.buysAux 
            this.buys = this.buys.filter((buy)=>{
                return buy.invoice_id.toLowerCase().includes(this.query.toLowerCase())
                || buy.amount.toString().includes(this.query)
                || buy.statusText.toLowerCase().includes(this.query.toLowerCase())
            })
        },
        goToViewInvoice(buy_per_user_id) {    
            window.location.href = `../../apps/store/invoices?bpid=${buy_per_user_id}`
        },
        getLastOrders() {    
            this.User.getLastOrders({},(response)=>{
                if(response.s == 1)
                {
                    this.buys = response.buys.map((buy)=>{
                        if(buy.status == this.STATUS.VERIFIED) {
                            buy.statusText = 'Verificado'
                        } else if(buy.status == this.STATUS.PENDING)
                        {
                            buy.statusText = 'Pendiente'
                        } else if(buy.status == this.STATUS.DELETED) {
                            buy.statusText = 'Eliminado'
                        }
                        return buy
                    })

                    this.buysAux = this.buys
                }
            })
        },
    },
    mounted() 
    {   
        this.getLastOrders()
    },
    template : `
        <div class="card animation-fall-down" style="--delay:500ms">
            <div class="card-header px-5">
                <div class="row align-items-center justify-content-center">
                    <div class="col-12 col-xl">
                        <h3>Últimas ordenes</h3>
                    </div>
                    <div class="col-12 col-xl-6">
                        <input v-model="query" type="text" class="form-control" placeholder="buscar alguna compra por ticket id o monto..."/>
                    </div>
                </div>
            </div>

            <ul v-if="buys" class="list-group list-group-flush">
                <li v-for="buy in buys" class="list-group-item p-4">
                    <div class="row justify-content-center align-items-center">
                        <div class="col-12 col-xl">
                            <span class="badge text-secondary p-0">
                                #{{buy.invoice_id}}
                            </span>
                            <div></div>
                        </div>
                        <div class="col-12 col-xl-auto">
                            {{buy.create_date.formatFullDate()}}
                        </div>
                        <div class="col-12 col-xl-auto text-dark fw-sembold">
                            $ {{buy.amount.numberFormat(2)}} MXN
                        </div>
                        <div class="col-12 col-xl-auto">
                            <span v-if="buy.status == STATUS.VERIFIED" class="badge bg-success">
                                Verificado
                            </span>
                            <span v-else-if="buy.status == STATUS.PENDING" class="badge bg-warning">
                                Pendiente 
                            </span>
                            <span v-else-if="buy.status == STATUS.DELETED" class="badge bg-danger">
                                Cancelada
                            </span>
                        </div>
                        <div class="col-12 col-xl-auto">
                            <button @click="goToViewInvoice(buy.buy_per_user_id)" class="btn btn-sm mb-0 px-3 btn-primary">más</button>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    `,
}

export { LastordersViewer } 