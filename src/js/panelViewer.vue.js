import { User } from '../../src/js/user.module.js?v=2.6.6'   
import { Api } from '../../src/js/api.module.js'  

const PanelViewer = {
    name : 'panel-viewer',
    data() {
        return {
            User: new User,
            Api: null,
            licence: null,
            proyect : null
        }
    },
    methods: {
        initChart(chartId) {
            const ctx = document.getElementById(chartId).getContext("2d")
            let datasets = []
            
            datasets.push({
                label: ['Ventas'],
                data: [245,123,3504,1230,3501],
                borderColor: '#6f42c1',
                backgroundColor: '#6f42c1',
                pointStyle: "circle",
                pointRadius: 5,
                pointHoverRadius: 10,
                cubicInterpolationMode: "monotone",
                tension: 0.4,
            })

            const data = {
                labels: ['1ero','2ndo','3rdo','4tho','5tho'],
                datasets: datasets,
            }

            const config = {
                type: "line",
                data: data,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: "top",
                        },
                        title: {
                            display: false,
                            text: "Capital invertido por broker 5 días atrás",
                        },
                    },
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            display: false,
                            title: {
                                display: true,
                                text: "Cantidad de visitas",
                            },
                            grid: {
                                display: false
                            }
                        },
                    },
                },
            }

            return new Chart(ctx, config)
        },
        copyClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                $(element).html($(element).data("helper"));
            })
        },
        initCharts() {
            setTimeout(()=>{
                this.proyect.sheets.map((sheet) => {
                    sheet.chart = this.initChart(sheet.sheet_per_proyect_id)
                })
            }, 1000)
        },
        getProyectInfo(keywords) {
            return new Promise((resolve) => {
                let proyect = null

                this.Api.getProyects({apiFilter:{keywords:keywords}},(response)=>{
                    if(response.s == 1)
                    {
                        proyect = response.proyects[0]

                        this.Api.getSheets({proyect_id:proyect.proyect_id,apiFilter:{catalog_sheet_id:1}},(response)=>{
                            if(response.s == 1)
                            {
                                proyect.sheets = response.sheets

                                resolve(proyect)
                            }
                        })
                    }
                })
            })
        },
        init() {
            this.getApiCredentials().then(()=>{
                this.getProyectInfo('DummieTrading').then(proyect => {
                    this.proyect = proyect
                    this.initCharts()
                })
            })

            this.isAviableToAddPayment().then(() => {
                alertAdvise({
                    html: `<div class="row d-flex justify-content-center align-items-center vh-100">
                            <div class="col-12">
                                <h3>📣 Recuerda realizar tu activación mensual de licencia para permanecer activo</h3>
                                <p>Si no te activas en el mes serás eliminado del sistema ☹️</p>
                                <a href="../../apps/store/addPayment" class="btn btn-primary btn-lg">Activar mi licencia</a>
                            </div>
                          </div>`,
                    size: 'modal-fullscreen'
                })
            }).catch(error => {})
        },
        getApiCredentials() {
            return new Promise((resolve) => {
                if(this.Api == null)
                {
                    this.User.getApiCredentials({},(response)=>{
                        if(response.s == 1)
                        {
                            this.Api = new Api(response.api)

                            resolve()
                        }
                    })
                } else {
                    resolve()
                }
            })
        },
        configureService() {
            return new Promise((resolve) => {
                this.getApiCredentials().then(()=>{
                    this.Api.getPackagesList({apiFilter:{name:'DummieTrading'}},(response)=>{

                        if(response.s == 1)
                        {
                            this.Api.importPackage({package_import_id:response.packages[0].package_import_id},(response)=>{
                                if(response.s == 1)
                                {
                                    this.User.configureService({},(response)=>{
                                        if(response.s == 1)
                                        {
                                            alertInfo({
                                                icon:'<i class="bi bi-ui-checks"></i>',
                                                message: 'Hemos añadido el modelo de negocio a tu oficina virtual, ya puedes utilizar las herramientas',
                                                _class:'bg-gradient-success text-white'
                                            })

                                            resolve()
                                        }
                                    })
                                }
                            })
                        }
                    })
                })
            })
        },
        getServiceConfiguration() {
            return new Promise((resolve) => {
                this.User.getServiceConfiguration({},(response)=>{
                    resolve(response.s == 1)
                })
            })
        },
        isAviableToAddPayment() {
            return new Promise((resolve,reject) => {
                this.User.isAviableToAddPayment({}, (response) => {
                    if (response.s == 1) {
                        resolve(true)
                    }

                    reject()
                })
            })
        },
    },
    mounted() 
    {     
        this.getServiceConfiguration().then((configurated)=>{
            this.configurated = configurated

            if(this.configurated)
            {
                this.init()
            } else {
                this.configureService().then(()=>{
                    this.configurated = true

                    this.init()
                })
            }
        })
    },
    template : `
        <div v-if="proyect" class="row">
            <div class="col-12">
                <div class="h3 py-3">Páginas de ventas</div>

                <div class="row">
                    <div v-for="(sheet,index) in proyect.sheets" class="col-12 col-xl-6 px-0 px-xl-3">
                        <div class="card shadow-none overflow-auto border f-zoom-element">
                            <div class="card-header">
                                <div class="text-secondary">Página web</div>
                                <div class="h4 bold"><i class="bi bi-globe2"></i> {{sheet.title}}</div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        
                                        <div class="card shadow-none bg-light">
                                            <div class="card-body">
                                                <div class="row align-items-center">
                                                    <div class="col">
                                                        <div class="text-xs fw-sembold text-secondary">Link a tu página</div>
                                                        <div class="">
                                                            <a class="text-primary fw-sembold text-decoration-underline" target="_blank" :href="sheet.route.formatRoute()">{{sheet.route.formatRoute()}}</a>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <button class="btn shadow-none px-3 fs-5 me-2 mb-0 btn-secondary"><i class="bi bi-share"></i></button>
                                                        <button @click="copyClipboard(sheet.route.formatRoute())" class="btn shadow-none px-3 fs-5 me-2 mb-0 btn-secondary"><i class="bi bi-clipboard-fill"></i></button>
                                                        <a :href="sheet.route.formatRoute()" target="_blank" class="btn shadow-none px-3 fs-5 mb-0 btn-secondary"><i class="bi bi-link-45deg"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row my-3 align-items-center">
                                            <div class="col-12 col-xl-6">
                                                <div class="card shadow-none border">
                                                    <div class="card-body">
                                                        <div class="row align-items-center">
                                                            <div class="col-auto">
                                                                <i class="bi h2 text-secondary bi-eye-fill"></i>
                                                            </div>
                                                            <div class="col text-end">
                                                                <div class="text-secondary text-xs">Visitas totales</div>
                                                                <div class="h1">
                                                                    {{sheet.visits}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-xl-6">
                                                <div class="card bg-gradient-primary">
                                                    <div class="card-body">
                                                        <div class="row align-items-center">
                                                            <div class="col-auto">
                                                                <i class="bi text-white h2 text-secondary bi-cart-fill"></i>
                                                            </div>
                                                            <div class="col text-end">
                                                                <div class="text-secondary text-white text-xs">ventas totales</div>
                                                                <div class="h1 text-white">
                                                                    {{sheet.visits}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-header bg-primary-soft">
                                <div class="text-secondary">Estadísticas</div>
                                <div class="h4 bold"><i class="bi bi-globe2"></i> Cantidad de visitas</div>
                            </div>
                            <div class="row bg-primary-soft">
                                <div class="col-12">
                                    <canvas :id="sheet.sheet_per_proyect_id"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { PanelViewer } 