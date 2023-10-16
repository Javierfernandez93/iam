import { User } from '../../src/js/user.module.js?v=2.6.5'   

const CountryViewer = {
    name : 'country-viewer',
    props : ['viewcountries'],
    emits : ['addcountry','deletecountry'],
    data() {
        return {
            User: new User,
            query: null,
            countriesAux: null,
            countries: null,
        }
    },
    watch : {
        query: {
            handler() {
                this.filterData()
            },
            deep: true
        }
    },
    methods: {
        filterData: function () {
            this.countries = this.countriesAux

            this.countries = this.countries.filter((country) => {
                return country.nicename.toLowerCase().includes(this.query.toLowerCase())
            })
        },
        addCountry: function (country) {
            country.added = true
            this.$emit('addcountry',country)
        },
        deleteCountry: function (country) {
            this.$emit('deletecountry',country)
        },
        getAllCountries: function () {
            return new Promise((resolve)=>{
                this.User.getAllCountries({},(response)=>{
                    if(response.s == 1)
                    {
                        resolve(response.countries)
                    }
                })
            })
        },
    },
    mounted() {
        this.getAllCountries().then((countries)=>{
            this.countriesAux = countries
            this.countries = this.countriesAux
        })
    },
    template : `
        <div v-if="countries && viewcountries">
            <div class="card mb-3">
                <div class="input-group input-group-lg input-group-merge">
                    <input v-model="query" :autofocus="true" type="text" class="form-control" placeholder="Buscar país por nombre..." />
                </div>
            </div>

            <ul v-if="query" class="list-group">
                <li v-for="country in countries" class="list-group-item">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="badge bg-gradient-primary">
                                <i class="bi bi-globe"></i>
                            </span>
                        </div>
                        <div class="col">
                            {{country.nicename}}
                        </div>
                        <div class="col-auto">
                            <button v-if="!country.added" @click="addCountry(country)" type="button" class="btn btn-success btn-sm m-0">Añadir país</button>
                            <button v-else @click="deleteCountry(country)" type="button" class="btn btn-danger btn-sm m-0">Eliminar país</button>
                        </div> 
                    </div> 
                </li> 
            </ul>
        </div> 
    `,
}

export { CountryViewer } 