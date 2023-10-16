import { User } from '../../src/js/user.module.js?v=2.6.6'   

const StoreitemsViewer = {
    name : 'storeitems-viewer',
    props : ['cart','hasitems'],
    emits: ['nextstep'],
    data() {
        return {
            User: new User,
            packages : null,
            PACKAGE_TYPE :{
                MEMBERSHIP : 1,
            },
            product : {
                product_id: 1,
                quantity: 0,
                added: false,
            }
        }
    },
    watch : {
        'cart.vars' : {
            handler(newv,oldv) {
                this.sanitize()
                this.product.quantity = this.getTotal()
            },
            deep: true
        },
        'product.added' : {
            handler() {
                this.cart.hasItems = this.product.added
            },
            deep: true
        }
    },
    methods: {
        addProduct() {
            this.User.addProduct(this.product, (response) => {
                if (response.s == 1) {
                    this.product.added = true
                }
            })
        },
        sanitize() {
            this.cart.vars.map((_var) => {
                if(_var.default_value) 
                {
                    if(_var.default_value < _var.min_value) 
                    {
                        _var.default_value = _var.min_value
                    } else if(_var.default_value >= _var.max_value) {
                        _var.default_value = _var.max_value
                    }
                }
            })
        },
        getTotal() {
            let quantity = 149

            this.cart.vars.map((_var) => {
                if(_var.name == 'trading_day') {
                    quantity *= 1 + _var.default_value/26
                } else if(_var.name == 'trading_min_day') {
                    quantity *= 1 + _var.default_value/27
                } else if(_var.name == 'drawdown_by_day') {
                    quantity *= 1 + _var.default_value/56
                } else if(_var.name == 'drawdown_total') {
                    quantity *= 1 + _var.default_value/57
                }
            })

            return Math.ceil(quantity)
        },
        addPackage(item) {
            let quantity = item.quantity != undefined ? item.quantity : 1

            this.User.addPackage({package_id:item.package_id,quantity:quantity}, (response) => {
                if (response.s == 1) {
                    this.cart.package_id = response.package_id
                    
                    item.selected = true

                    setTimeout(()=>{
                        this.$emit('nextstep')
                    },500)
                }
            })
        },
        deleteItem(item)
        {
            this.User.deleteItem({id:item.package_id}, (response) => {
                if (response.s == 1) {
                    item.selected = false
                }
            })
        },
        getVarsConfiguration() {
            this.User.getVarsConfiguration({}, (response) => {
                if (response.s == 1) {
                    this.cart.vars = response.vars
                }
            })
        },
        getPackages(catalog_package_type_id) {
            this.User.getPackages({catalog_package_type_id:catalog_package_type_id}, (response) => {
                if (response.s == 1) {
                    this.packages = response.packages.map((_package)=>{
                        _package.quantity = 1
                        return _package
                    })
                }
            })
        },
    },
    mounted() {
        const package_type = getLastUrlPart()
        
        if(['package'].includes(package_type)) {
            this.getPackages(this.PACKAGE_TYPE.MEMBERSHIP)
        }

        if(getParam("pid")) 
        {
            this.addPackage({
                package_id:getParam("pid"),
                quantity:getParam("quantity")
            })
        }
    },
    template : `
        <ul class="nav nav-pills mb-5" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button @click="getPackages(PACKAGE_TYPE.MEMBERSHIP)" class="nav-link" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Paquetes IAM</button>
            </li>
        </ul>
        
        <div if="packages" class="row justify-content-center">
            <div v-for="(package,index) in packages" class="col-12 col-xl-4 col-md-4">
                <div class="card position-relative animation-fall-down overflow-hidden mb-5" style="--delay:200ms;background-position:center;background-size:cover;" :style="{'background-image':'url('+package.image+')'}">
                    <span class="mask" class="bg-gradient-dark" :alt="package.title"></span>
                    
                    <div class="position-relative z-index-1">
                        <div class="card-body">
                            <div class="h2 text-white">{{package.title}}</div>
                            
                            <div class="my-5 text-center">
                                <div class="text-md text-light mb-n0">Valor</div>
                                <div class="h2 fw-sembold text-white">$ {{package.amount.numberFormat(2)}}</div>
                            </div>
                            
                            <div class="input-group">
                                <button @click="package.quantity = parseInt(package.quantity-1)" class="btn btn-outline-secondary mb-0" type="button" id="button-addon1">-</button>
                                <input v-model="package.quantity" type="number" class="form-control text-center rounded-0" placeholder="" aria-label="" aria-describedby="button-addon1">
                                <button @click="package.quantity = parseInt(package.quantity+1)" class="btn btn-outline-secondary mb-0" type="button" id="button-addon1">+</button>
                            </div>
                        </div>
                        <div class="card-footer d-grid">
                            <button @click="addPackage(package)" class="btn btn-primary btn-lg mb-0 shadow-none">Elegir paquete</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { StoreitemsViewer } 