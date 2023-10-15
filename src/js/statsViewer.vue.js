import { UserSupport } from './userSupport.module.js'   

const StatsViewer = {
    name : 'stats-viewer',
    props : [],
    emits : [],
    data() {
        return {
            UserSupport : null,
            performancesAux: {},
            performances: {},
            query: null,
            columns : {
                create_date : {
                    name: 'create_date',
                    desc: false,
                },
                performance : {
                    name: 'performance',
                    desc: false,
                },
            }
        }
    },
    watch : {
        query : 
        {
            handler() {
                this.filterData()
            },
            deep : true
        }
    },
    methods: {
        filterData : function() {
            this.performances = this.performancesAux
            
            this.performances = this.performancesAux.filter((performance)=>{
                return performance.create_date.formatDate().toLowerCase().includes(this.query.toLowerCase()) || performance.extrapol.toString().includes(this.query.toLowerCase()) || performance.average.toString().includes(this.query.toLowerCase()) || performance.performance.toString().includes(this.query.toLowerCase())
            })
        },
        sortData: function (column) {
            this.performances.sort((a,b) => {
                const _a = column.desc ? a : b
                const _b = column.desc ? b : a

                if(column.alphabetically)
                {
                    return _a[column.name].localeCompare(_b[column.name])
                } else {
                    return _a[column.name] - _b[column.name]
                }
            });

            column.desc = !column.desc
        },
        getAverange : function(array) {
            return array.reduce((a,b) => a+b, 0) / array.length
        },
        getData : function(performances) {
            let temp = []
            performances.map((performance)=>{
                temp.push(performance.performance)
                const average = this.getAverange(temp)

                Object.assign(performance, {
                    average: average,
                    extrapol: average*20,
                })
            })

            return performances
        },
        getBrokersData : function() {
            this.UserSupport.getBrokersData({},(response)=>{
                if(response.s == 1)
                {
                    this.performancesAux = this.getData(response.performances)
                    this.performances = this.performancesAux
                }
            })
        }
    },
    updated() {
    },
    mounted() 
    {   
        this.UserSupport = new UserSupport
        this.getBrokersData()
    },
    template : `
        <div class="card mb-3">
            <div class="card-header pb-0">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <i class="bi bi-pie-chart-fill"></i>
                    </div>
                    <div class="col fw-semibold text-dark">
                        <div class="small">Estadísticas</div>
                    </div>
                    <div class="col-auto text-end">
                        <div><span class="badge bg-secondary">Total de estadísticas {{Object.keys(performances).length}}</span></div>
                    </div>
                </div>
            </div>
            <div class="card-header pb-0">
                <div class="row">
                    <div class="col">
                        <input 
                            :autofocus="true"
                            v-model="query"
                            type="text" class="form-control" placeholder="Buscar por fecha, rendimiento, promedio o extrapol..."/>
                    </div>
                    <div class="col-auto">
                        <button 
                            @click="getBrokersData"
                            class="btn btn-primary"><i class="bi bi-arrow-clockwise"></i></button>
                    </div>
                </div>
            </div>
            <div 
                v-if="Object.keys(performances).length > 0"
                class="card-body px-0 pt-0 pb-2">
                <div class="xtable-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th @click="sortData(columns.create_date)" class="text-center c-pointer text-uppercase text-xxs text-secondary font-weight-bolder opacity-7">
                                    <span v-if="columns.create_date.desc">
                                        <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                    </span>    
                                    <span v-else>    
                                        <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                    </span>    
                                    <u class="text-sm">Fecha</u>
                                </th>
                                <th @click="sortData(columns.performance)" class="text-center c-pointer text-uppercase text-xxs text-secondary font-weight-bolder opacity-7">
                                    <span v-if="columns.performance.desc">
                                        <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                    </span>    
                                    <span v-else>    
                                        <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                    </span>    
                                    <u class="text-sm">Rendimiento</u>
                                </th>
                                <th @click="sortData(columns.performance)" class="text-center c-pointer text-uppercase text-xxs text-secondary font-weight-bolder opacity-7">
                                    <span v-if="columns.performance.desc">
                                        <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                    </span>    
                                    <span v-else>    
                                        <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                    </span>    
                                    <u class="text-sm">Promedio</u>
                                </th>
                                <th @click="sortData(columns.performance)" class="text-center c-pointer text-uppercase text-xxs text-secondary font-weight-bolder opacity-7">
                                    <span v-if="columns.performance.desc">
                                        <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                    </span>    
                                    <span v-else>    
                                        <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                    </span>    
                                    <u class="text-sm">Extrapol</u>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="performance in performances"
                                class="text-center">
                                <td class="align-middle text-center text-sm">
                                    <h6 class="mb-0 text-sm">{{performance.create_date.formatDate()}}</h6>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    <h6 class="mb-0 text-sm">{{performance.performance.numberFormat(2)}} %</h6>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    <h6 class="mb-0 text-sm">{{performance.average.numberFormat(2)}} %</h6>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    <h6 class="mb-0 text-sm">{{performance.extrapol.numberFormat(2)}} %</h6>
                                </td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    `,
}

export { StatsViewer } 