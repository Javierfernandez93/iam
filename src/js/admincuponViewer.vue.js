import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.5'

const AdmincuponViewer = {
    name : 'admincupon-viewer',
    data() {
        return {
            UserSupport: new UserSupport,
            cupons: null,
        }
    },
    methods: {
        createCupons() {
            let alert = alertCtrl.create({
                title: "Crea cupones",
                subTitle: `Ingresa la información que se te pide`,
                inputs: [
                    {
                        type: 'text',
                        id: 'code',
                        placeholder: 'Cupon',
                        name: 'code',
                    },
                    {
                        type: 'number',
                        id: 'amount',
                        placeholder: 'Cantidad',
                        name: 'amount',
                    },
                    {
                        type: 'number',
                        id: 'discount',
                        placeholder: 'Porcentaje de descuento',
                        name: 'discount',
                    }
                ],
                buttons: [
                    {
                        text: "Sí, crear",
                        class: 'btn-success',
                        role: "cancel",
                        handler: (data) => {
                            this.UserSupport.createCupons(data,(response)=>{
                                this.getCupons()
                            })
                        },
                    },
                    {
                        text: "Cancelar",
                        role: "cancel",
                        handler: (data) => {
                        },
                    },
                ],
            })

            alertCtrl.present(alert.modal);
        },
        getCupons() {
            this.UserSupport.getCupons({}, (response) => {
                if (response.s == 1) {
                    this.cupons = response.cupons
                }
            })
        },
    },
    mounted() {
        this.getCupons()
    },
    template : `
        <div v-if="cupons">
            <div class="card">
                <div class="card-header">
                    <div class="row justify-content-center align-items-center">
                        <div class="col-12 col-xl h3">
                            Lista de cupones
                        </div>
                        <div class="col-12 col-xl-auto">
                            <button @click="createCupons" class="btn btn-primary mb-0">Generar cupones</button>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cupon</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Descuento</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Fecha de registro</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Estatus</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="cupon in cupons">
                                    <td class="align-middle text-center text-xs">
                                        <span>{{cupon.cupon_id}}</span>
                                    </td>
                                    <td class="align-middle text-center text-sm">{{cupon.code}}</td>
                                    <td class="align-middle text-center text-sm">{{cupon.discount.numberFormat(2)}} %</td>
                                    <td class="align-middle text-center text-sm">{{cupon.create_date.formatFullDate()}}</td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="badge bg-primary">Disponible</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div v-else-if="buys == false" class="alert alert-light fw-semibold text-center">    
            No tenemos compras aún 
        </div>
    `,
}

export { AdmincuponViewer } 