import { User } from '../../src/js/user.module.js?v=2.6.4'   

const AddvcardViewer = {
    name : 'addvcard-viewer',
    props : [],
    emits : ['openmodal'],
    data() {
        return {
            User: new User,
            query: null,
            templatesAux: null,
            countries: null,
            templates: null,
            isVCardSelected: false,
            error: null,
            step: null,
            vcard: {
                vcard_per_user_id: null,
                template_id: null,
                title: null,
                route: '',
                catalog_tags_template: null
            },
            ERRORS: {
                ROUTE_EXIST :{ 
                    code: 1,
                    text: 'La URL de alias introducida ya éxiste, por favor elije otra',
                }
            },
            STEPS : {
                SELECT_VCARD: 1,
                CONFIGURE_BASICS: 2,
            },
            STATUS : {
                UNPUBLISHED: 0,
                PUBLISHED: 1
            },
            TAG_VALUE : {
                TEXT : 1,
                PHONE : 2,
                IMAGE : 3,
                EDITOR : 4,
            },
        }
    },
    watch : {
        query : {
            handler() {
                this.filterData()
            },
            deep: true
        },
        step : {
            handler() {
                this.stepServices()
            },
            deep: true
        },
        vcard : {
            handler() {
                this.isVCardSelected = this.vcard.template_id !== null
            },
            deep: true
        }
    },
    methods: {
        filterData() {
            this.templates = this.templatesAux

            this.templates = this.templates.filter(template => template.title.toLowerCase().includes(this.query.toLowerCase()))
        },
        publishVcard(vcard) {
            this.User.publishVcard({vcard_per_user_id:vcard.vcard_per_user_id}, (response) => {
                if (response.s == 1) {
                    vcard.status = response.status
                }
            })
        },
        openStorage(catalog_tag_template) 
        {
            this.$emit('openmodal',catalog_tag_template)
        },
        unPublishVcard(vcard) {
            this.User.unPublishVcard({vcard_per_user_id:vcard.vcard_per_user_id}, (response) => {
                if (response.s == 1) {
                    vcard.status = response.status
                }
            })
        },
        deleteCampaign(vcard_per_user_id) {
            this.User.deleteCampaign({vcard_per_user_id:vcard_per_user_id}, (response) => {
                if (response.s == 1) {
                    this._getCampaigns()
                }
            })
        },
        getAllTemplates() {
            return new Promise((resolve,reject) => {
                this.User.getAllTemplates({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.templates)
                    }

                    reject()
                })
            })
        },
        getVcardConfiguration() {
            return new Promise((resolve,reject) => {
                this.User.getVcardConfiguration({vcard_per_user_id:this.vcard.vcard_per_user_id}, (response) => {
                    if (response.s == 1) {
                        resolve(response.catalog_tags_template)
                    }

                    reject()
                })
            })
        },
        saveVCard() {
            return new Promise((resolve) => {
                this.User.saveVCard(this.vcard, (response) => {
                    if (response.s == 1) {
                        this.vcard.vcard_per_user_id = response.vcard_per_user_id

                        resolve()
                    }
                })
            })
        },
        updateVCard(target) {
            this.error = null
            this.User.updateVCard(this.vcard, (response) => {
                if (response.s == 1) {

                    target.innerText = 'Tarjeta virtual creada con éxito'

                    this.vcard_per_user_id = response.vcard_per_user_id
                } else if(response.r = 'ROUTE_EXIST') {

                }
            })
        },
        nextStep(step)
        {
            this.step = step
        },
        goToEditVCard(vcard_per_user_id)
        {
            window.location.href = `../../apps/vCard/edit?vplid=${vcard_per_user_id}`
        },
        getTemplateThumbnail(template_id)
        {
            return `../../src/img/screenshots/${template_id}.png`
        },
        stepServices()
        {
            if(this.step == this.STEPS.SELECT_VCARD)
            {
                this.getAllTemplates().then((templates) => {
                    this.templatesAux = templates
                    this.templates = this.templatesAux
                }).catch(() => this.templates = false)
            } else if(this.step == this.STEPS.CONFIGURE_BASICS) {
                this.getVcardConfiguration().then((catalog_tags_template) => {
                    this.vcard.catalog_tags_template = catalog_tags_template

                    this.getCountriesPhones().then((countries) => {
                        this.countries = countries
                    }).catch(() => this.countries = false)
                }).catch(() => this.vcard.catalog_tags_template = false)
            }
        },
        getCountriesPhones () {
            return new Promise((resolve) => {
                this.User.getCountriesPhones({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.countries)
                    }
                })
            })
        },
        goToAddVCard()
        {
            window.location.href = '../../apps/vCard/add'
        },
    },
    mounted() {
        this.step = this.STEPS.SELECT_VCARD
        // this.step = this.STEPS.CONFIGURE_BASICS
    },
    template : `
        <div v-if="step == STEPS.SELECT_VCARD">
            <div class="card mb-3">
                <div class="input-group input-group-lg input-group-merge">
                    <input
                        v-model="query"
                        :autofocus="true"
                        type="text" class="form-control border-0 shadow-lg" placeholder="Buscar template..."/>
                </div>
            </div>

            <div v-if="templates" class="row">
                <div v-for="template in templates" class="col-12 col-xl-4 mb-3">
                    <div class="card f-zoom-element shadow-lg overflow-hidden"
                        :class="vcard.template_id == template.template_id ? 'border border-5 border-success' : ''">
                        <img :src="getTemplateThumbnail(template.template_id)" class="card-img-top" style="border-radius:0;">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <span class="badge text-secondary p-0">Creado hace {{template.create_date.timeSince()}}</span>
                                    <div class="fw-sembold text-dark fs-5">{{template.title}}</div>
                                </div>
                                <div class="col-auto">
                                    <button @click="vcard.template_id = template.template_id" class="btn m-0"
                                        :class="vcard.template_id == template.template_id ? 'btn-outline-light' : 'btn-outline-danger'"
                                        v-text="vcard.template_id == template.template_id ? 'Seleccionado' : 'Seleccionar'"></button>
                                </div>
                            </div>

                            <div v-if="isVCardSelected && vcard.template_id == template.template_id" class="row mt-3">
                                <div class="col-12">
                                    <button @click="saveVCard().then(() => { nextStep(STEPS.CONFIGURE_BASICS) })" class="btn btn-lg btn-success w-100 shadow-none mb-0">Continuar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else-if="step == STEPS.CONFIGURE_BASICS">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="badge bg-danger text-white fs-5">
                                <i class="bi bi-toggles"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div>
                                <span class="badge p-0 text-secondary">Configuración</span>
                            </div>
                            <span class="fw-semibold">Ingresa la información de tu V-Card</span>
                        </div>
                        <div class="col-auto">
                            <button @click="updateVCard($event.target)" class="btn mb-0 shadow-none btn-success">Guardar</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div v-if="error" class="alert alert-light fw-semibold text-dark">
                        {{error.text}}
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <span class="badge text-secondary p-0">Información de la VCard</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="form-floating col-12 col-xl-6">
                            <input 
                                v-model="vcard.title"
                                :class="vcard.title ? 'is-valid' : ''"
                                @keydown.enter.exact="$refs.route.focus()"
                                type="text" class="form-control" id="floatingInput" placeholder="name@example.com">
                            <label for="floatingInput">Nombre de la tarjeta virtual</label>
                        </div>
                        <div class="form-floating col-12 col-xl-6"
                            :class="vcard.route ? 'form-floating-badge' :''">
                            <span v-if="vcard.route" class="badge bg-light text-primary">https://www.iam.com.mx/</span>
                            <input 
                                v-model="vcard.route"
                                :class="vcard.route ? 'is-valid' : 'is-invalid'"
                                ref="route"
                                @keydown.space.prevent
                                type="text" class="form-control" id="floatingInput" placeholder="name@example.com">
                            <label for="floatingInput">URL de alias</label>
                        </div>
                    </div>
                    <div if="vcard.catalog_tags_template">
                        <div class="row mb-3">
                            <div class="col-12">
                                <span class="badge text-secondary p-0">Configuración de la VCard</span>
                            </div>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li v-for="catalog_tag_template in vcard.catalog_tags_template" class="list-group-item px-0 border-0">
                                <div v-if="catalog_tag_template.catalog_tag_value_id == TAG_VALUE.TEXT">
                                    <div class="form-floating">
                                        <input 
                                            v-model="catalog_tag_template.value" 
                                            :class="catalog_tag_template.value ? 'is-valid' : 'is-invalid'"
                                            :placeholder="catalog_tag_template.description"
                                            type="text" class="form-control">
                                        <label for="">{{catalog_tag_template.description}}</label>
                                    </div>
                                </div>
                                <div v-else-if="catalog_tag_template.catalog_tag_value_id == TAG_VALUE.EDITOR">
                                    <div>
                                        <label class="text-muted" for="">{{catalog_tag_template.description}}</label>
                                        <textarea class="mt-3 editor">{{catalog_tag_template.value}}</textarea>
                                    </div>
                                </div>
                                <div v-else-if="catalog_tag_template.catalog_tag_value_id == TAG_VALUE.PHONE">
                                    <div class="row align-items-center">
                                        <div v-if="countries" class="col-auto">
                                            <div class="form-floating">
                                                <select class="form-select" 
                                                    v-model="catalog_tag_template.value.country_id" 
                                                    :class="catalog_tag_template.country_id ? 'is-valid' :''" 
                                                    aria-label="Selecciona tu país">
                                                    <option>Selecciona tu país</option>
                                                    <option v-for="country in countries" v-bind:value="country.country_id">
                                                        {{ country.nicename }} <span v-if="country.phone_code > 0">+ {{ country.phone_code }}</span>
                                                    </option>
                                                </select>
                                                <label for="floatingSelect">País</label>
                                            </div>
                                        </div>
                                        <div class="col pr-0">
                                            <div class="form-floating">
                                                <input 
                                                    v-model="catalog_tag_template.value.phone"
                                                    :class="catalog_tag_template.value ? 'is-valid' : ''"
                                                    :placeholder="catalog_tag_template.description"
                                                    type="text" class="form-control phone">

                                                <label for="">{{catalog_tag_template.description}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else-if="catalog_tag_template.catalog_tag_value_id == TAG_VALUE.IMAGE">
                                    <div class="row">
                                        <div class="col pr-0">
                                            <div class="form-floating">
                                                <input 
                                                    v-model="catalog_tag_template.value"
                                                    :class="catalog_tag_template.value ? 'is-valid' : ''"
                                                    :placeholder="catalog_tag_template.description"
                                                    type="text" class="form-control" style="border-radius: 0.5rem 0 0 0.5rem;">
            
                                                <label for="">{{catalog_tag_template.description}}</label>
                                            </div>
                                        </div>
                                        <div class="col-auto d-flex justify-content-center pl-0">
                                            <button 
                                                @click="openStorage(catalog_tag_template)"
                                                class="btn btn-light bg-white btn-password-toggle">
                                                <i class="fas fa-hdd"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { AddvcardViewer } 