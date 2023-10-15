import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.4'

const AdminpackagesViewer = {
    name : 'adminpackages-viewer',
    data() {
        return {
            UserSupport: new UserSupport,
            packages: null,
            packagesAux: null,
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
            this.packages = this.packagesAux
            this.packages = this.packages.filter((_package) => {
                return _package.title.toLowerCase().includes(this.query.toLowerCase()) 
                || _package.amount.toString().includes(this.query.toLowerCase()) 
            })
        },
        setPackageStatus(_package,status) {
            this.UserSupport.setPackageStatus({package_id:_package.package_id,status:status}, (response) => {
                if (response.s == 1) {
                    _package.status = response.status
                }
            })
        },
        getAdminPackages() {
            return new Promise((resolve,reject) => {
                this.UserSupport.getAdminPackages({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.packages)
                    }

                    reject()
                })
            })
        },
    },
    mounted() {
        this.getAdminPackages().then((packages)=>{
            this.packages = packages
            this.packagesAux = packages
        }).catch(()=>{
            // this.packages = false
        })
    },
    template : `
        <div class="card mb-3">
            <div class="input-group input-group-lg input-group-merge">
                <input
                    v-model="query"
                    :autofocus="true"
                    @keydown.enter.exact.prevent="search"
                    type="text" class="form-control border-0 shadow-lg" placeholder="Buscar paquete..."/>
            </div>
        </div>

        <div v-if="packages">
            <div v-for="_package in packages" class="card card-body">
                <div class="row align-items-center">
                    <div class="col-12 col-xl">

                        <span v-if="_package.status == 1" class="badge bg-success">Activo</span>
                        <span v-if="_package.status == 0" class="badge bg-secondary">Inactivo</span>
                        <span v-if="_package.status == -1" class="badge bg-danger">Eliminado</span>

                        <div class="h3">{{_package.title}}</div>
                        <div class="h3 fw-light">$ {{_package.amount.numberFormat(2)}} MXN</div>
                    </div>
                    <div v-if="_package.products" class="col-12 col-xl">
                        <div>Productos</div>
                        <div v-for="product in _package.products" class="lead fw-bold text-dark">
                            <span class="badge me-2 bg-primary">{{product.quantity}}</span> {{product.product.title}}
                            <div class="">
                                $ {{product.product.amount.numberFormat(2)}} MXN
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-auto">
                        <div class="dropdown">
                            <button type="button" class="btn btn-outline-primary mb-0 px-3 btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">

                            </button>
                            <ul class="dropdown-menu shadow">
                                <li v-if="_package.status == 1"><button class="dropdown-item" @click="setPackageStatus(_package,0)">Inactivar</button></li>
                                <li v-if="_package.status == 0"><button class="dropdown-item" @click="setPackageStatus(_package,1)">Activar</button></li>
                                <li><button class="dropdown-item" @click="setPackageStatus(_package,-1)">Eliminar</button></li>
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

export { AdminpackagesViewer } 