import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.5'

const AdminproductsViewer = {
    name : 'adminproducts-viewer',
    data() {
        return {
            UserSupport: new UserSupport,
            products: null,
            productsAux: null,
            query: null
        }
    },
    watch : {
        query : {
            handler() {
                this.filterData()
            },
            deep: true
        }
    },
    methods: {
        filterData() {
            this.products = this.productsAux
            this.products = this.products.filter((product) => {
                return product.title.toLowerCase().includes(this.query.toLowerCase()) 
                || product.amount.toString().includes(this.query.toLowerCase()) 
            })
        },
        saveProductStock(product) {
            this.UserSupport.saveProductStock({product_id:product.product_id,stock:product.stock}, (response) => {
                if (response.s == 1) {
                    product.editingStock = false
                }
            })
        },
        setProductStatus(product,status) {
            this.UserSupport.setProductStatus({product_id:product.product_id,status:status}, (response) => {
                if (response.s == 1) {
                    product.status = response.status
                }
            })
        },
        getAdminProducts() {
            return new Promise((resolve,reject) => {
                this.UserSupport.getAdminProducts({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.products)
                    }

                    reject()
                })
            })
        },
    },
    mounted() {
        this.getAdminProducts().then((products)=>{
            this.products = products
            this.productsAux = products
        }).catch(()=>{
            // this.products = false
        })
    },
    template : `
        <div class="card mb-3">
            <div class="input-group input-group-lg input-group-merge">
                <input
                    v-model="query"
                    :autofocus="true"
                    @keydown.enter.exact.prevent="search"
                    type="text" class="form-control border-0 shadow-lg" placeholder="Buscar producto..."/>
            </div>
        </div>

        <div v-if="products">
            <div v-for="product in products" class="card card-body">
                <div class="row align-items-center">
                    <div class="col-12 col-xl-auto">
                        <div class="avatar">
                            <img :src="product.image" class="avatar" alt="Product" alt="Product"/>
                        </div>
                    </div>
                    <div class="col-12 col-xl">
                        <span v-if="product.status == 1" class="badge bg-success">Activo</span>
                        <span v-if="product.status == 0" class="badge bg-secondary">Inactivo</span>
                        <span v-if="product.status == -1" class="badge bg-danger">Eliminado</span>
                        
                        <div class="lead">{{product.title}} $ {{product.amount.numberFormat(2)}} MXN</div>
                    </div>
                    <div class="col-12 col-xl-auto">
                        <div v-if="!product.editingStock" @click="product.editingStock = true" class="lead">{{product.stock}} u.</div>
                        <div v-else class="lead">
                            <input v-model="product.stock" @keypress.exact.enter="saveProductStock(product)" :class="product.stock ? 'is-valid' : 'is-invalid'" type="number" class="form-control"/>
                        </div>
                    </div>
                    <div class="col-12 col-xl-auto">
                        <div class="dropdown">
                            <button type="button" class="btn btn-outline-primary mb-0 px-3 btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">

                            </button>
                            <ul class="dropdown-menu shadow">
                                <li v-if="product.status == 1"><button class="dropdown-item" @click="setProductStatus(product,0)">Inactivar</button></li>
                                <li v-if="product.status == 0"><button class="dropdown-item" @click="setProductStatus(product,1)">Activar</button></li>
                                <li><button class="dropdown-item" @click="setProductStatus(product,-1)">Eliminar</button></li>
                                <li><button class="dropdown-item" @click="editProduct(product)">Editar</button></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else-if="packages == false" class="alert alert-light fw-semibold text-center">    
            No tenemos paquetes aún 
        </div>
    `,
}

export { AdminproductsViewer } 