import { User } from '../../src/js/user.module.js?v=2.6.6'   

const PackageviewViewer = {
    name : 'packageview-viewer',
    data() {
        return {
            User: new User,
            packageInfo : null,
            image : null,
            logged : false,
        }
    },
    methods: {
        viewImage(image) {
            this.image = image
        },
        getPackageInfo(package_id) {
            this.User.getPackageInfo({package_id:package_id}, (response) => {
                if (response.s == 1) {
                    this.logged = response.logged
                    this.packageInfo = response.packageInfo
                    this.packageInfo.quantity = 1
                    this.image = this.packageInfo.products[0].images[0]
                }
            })
        },
        buyPackage(packageInfo) {
            if(!this.logged)
            {
                window.location.href = `../../apps/login`
            } else {
                window.location.href = `../../apps/store/package?pid=${packageInfo.package_id}&quantity=${packageInfo.quantity}`
            }
        },
    },
    mounted() {
        if(getParam("pid"))
        {
            this.getPackageInfo(getParam("pid"))
        }
    },
    template : `
        <div v-if="packageInfo" class="row gx-5 justify-content-center position-relative z-index-1 py-5 animation-fall-down" style="--delay:500ms">
            <div class="col-12 col-xl-6">
                <div class="row">
                    <div class="col-2">
                        <div v-for="product in packageInfo.products">
                            <div v-for="image in product.images" class="mb-3">
                                <img :src="image.path" class="img-fluid img-thumbnail cursor-pointer z-zoom-element" @click="viewImage(image)"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-10">
                        <img :src="image.path" class="img-fluid img-thumbnail"/>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-6">
                <h3 class="text-dark text-light">
                    {{packageInfo.title}}
                </h3>
                <h4 class="text-dark text-semibold">
                    $ {{packageInfo.amount.numberFormat(2)}} MXN 
                </h4>

                <div class="row mb-3">
                    <div class="col-12 col-xl-4">
                        <div class="input-group">
                            <button @click="packageInfo.quantity = parseInt(packageInfo.quantity-1)" class="btn btn-outline-secondary mb-0" type="button" id="button-addon1">-</button>
                            <input v-model="packageInfo.quantity" type="number" class="form-control text-center rounded-0" placeholder="" aria-label="" aria-describedby="button-addon1">
                            <button @click="packageInfo.quantity = parseInt(packageInfo.quantity+1)" class="btn btn-outline-secondary mb-0" type="button" id="button-addon1">+</button>
                        </div>
                    </div>
                </div>

                <div class="border border-bottom border-light"></div>

                <div class="my-3">
                    <button @click="buyPackage(packageInfo)" class="btn btn-primary btn-lg mb-0 shadow-none px-4">Adquiere ahora</button>
                </div>
                
                <div class="my-3">
                    <div class="fw-sembold text-dark pb-3">Contenido</div>
                    <div class="">{{packageInfo.description}}</div>
                </div>
            </div>
        </div>
    `,
}

export { PackageviewViewer } 