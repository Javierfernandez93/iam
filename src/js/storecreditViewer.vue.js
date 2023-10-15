
import { User } from '../../src/js/user.module.js?v=2.6.4'   

const StorecreditViewer = {
    name : 'storecredit-viewer',
    props : ['cart','hasitems'],
    emits: ['nextstep'],
    data() {
        return {
            User: new User,
            product: null
        }
    },
    watch : {
        product : {
            handler() {
                this.cart.hasItems = this.product.quantity > 0
            },
            deep: true
        }
    },
    methods: {
        addProduct() {
            this.User.addProduct(this.product, (response) => {
                if (response.s == 1) {
                    this.cart.product_id = response.product_id
                    
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
        getProduct() {
            this.User.getProduct({}, (response) => {
                if (response.s == 1) {
                    this.product = response.product
                }
            })
        },
    },
    mounted() {
        this.getProduct()
    },
    template : `
        <div v-if="product" class="row justify-content-center">
            <div class="col-12 col-xl-4">
                <div class="card shadow-none border">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="text-dark">{{product.title}} - $ {{product.amount.numberFormat(2)}} c/u</div>
                            <input :autofocus="true" type="number" v-model="product.quantity" class="form-control fs-3 text-center" placeholder="Cantidad aquí" />
                        </div>

                        <div v-if="product.quantity" class="text-center mb-3">
                            <div class="text-dark">Sub total</div>
                            <div class="fs-3 fw-sembold text-dark">USD $ {{ (product.amount * product.quantity).numberFormat(2) }}</div>
                        </div>
                    </div>
                    <div class="card-footer d-grid">
                        <button @click="addProduct" :disabled="!product.quantity" class="btn btn-primary shadow-none mb-0 btn-lg">Añadir créditos</button>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { StorecreditViewer } 