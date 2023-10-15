import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.4'

const AdminrulesViewer = {
    name : 'adminrules-viewer',
    data() {
        return {
            UserSupport: new UserSupport,
            user: null,
            COMMON_RULES: Object.freeze([
                {
                    name: 'Drawdown',
                    value: 5,
                    editable: true,
                }
            ])
        }
    },
    methods: {
        deleteRule(rule) {
            rule.name = null
            rule.value = null
        },
        addCommonRule(common_rule) {
            if(!this.findRuleName(common_rule.name))
            {
                this.user.additional_data.push(common_rule)
            }
        },
        findRuleName(name) {
            return this.user.additional_data.find((rule)=>{
                return rule.name.toLowerCase().includes(name.toLowerCase())
            })
        },
        addNewRule() {
            this.user.additional_data.push({
                name: null,
                editable :true,
                value: null,
            })
        },
        getAdminTradingAccount(user_trading_account_id) {
            return new Promise((resolve, reject) => {
                this.UserSupport.getAdminTradingAccount({user_trading_account_id:user_trading_account_id},(response) => {
                    if(response.s == 1)
                    {
                        resolve(response.user)
                    }

                    reject()
                })
            })
        },
        saveAdditionalData() {
            return new Promise((resolve, reject) => {
                this.UserSupport.saveAdditionalData(this.user,(response) => {
                    if(response.s == 1)
                    {
                        resolve(response.user)
                    }

                    reject()
                })
            })
        },
    },
    mounted() {
        if(getParam("utaid"))
        {
            this.getAdminTradingAccount(getParam("utaid")).then((user) => {
                this.user = user
            })
        }
    },
    template : `
        <div v-if="user">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center mb-3">
                        <div class="col">
                            <span class="badge text-secondary p-0">Trader</span>
                            <div class="fs-4 text-primary fw-semibold">
                                {{user.trader}}
                            </div>
                        </div>
                        <div class="col-auto">
                            <button @click="addNewRule" class="btn btn-primary me-2 mb-0 shadow-none">Agregar regla</button>
                            <button @click="saveAdditionalData" class="btn btn-primary mb-0 shadow-none">Actualizar</button>
                        </div>
                    </div>  
                    <div class="row align-items-center d-none">
                        <div class="text-xs text-secondary mb-3">Reglas comunes</div>
                        <div class="col-12">
                            <span v-for="common_rule in COMMON_RULES" class="badge bg-primary cursor-pointer" @click="addCommonRule(common_rule)">
                                {{common_rule.name}}
                            </span>
                        </div>
                    </div>  
                </div>
                
                <ul v-if="user.additional_data" class="list-group list-group-flush">
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col">
                                Balance inicial (USD)
                            </div>
                            <div class="col-auto">
                                <input class="form-control" v-model="user.balance" placeholder="0">
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col">
                                Drawdown (%) 
                            </div>
                            <div class="col-auto">
                                <input class="form-control" v-model="user.drawdown" placeholder="0">
                            </div>
                        </div>
                    </li>
                    <li v-for="(_additional_data,key) in user.additional_data" class="list-group-item">
                        <div class="row">
                            <div class="col">
                                <div v-if="_additional_data.editable">
                                    <input class="form-control" v-model="_additional_data.name" placeholder="Nombre de la regla">
                                </div>
                                <div v-else>
                                    {{_additional_data.name}}
                                </div>
                            </div>
                            <div class="col-auto">
                                <input class="form-control" v-model="_additional_data.value" placeholder="Valor de la regla">
                            </div>
                            <div v-if="_additional_data.editable" class="col-auto">
                                <button @click="deleteRule(_additional_data)" class="btn btn-danger mb-0 shadow-none btn-sm px-3">Delete</button>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    `,
}

export { AdminrulesViewer } 