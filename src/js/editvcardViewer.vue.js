import { User } from '../../src/js/user.module.js?v=2.6.6'   

const EditvcardViewer = {
    name : 'editvcard-viewer',
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
            step: null,
            error: null,
            vcard: {
                saved: false,
                vcard_per_user_id: null,
                template_id: null,
                title: null,
                route: '',
                catalog_tags_template: null
            },
            STEPS : {
                SELECT_VCARD: 1,
                CONFIGURE_BASICS: 2,
            },
            ERRORS: {
                ROUTE_EXIST :{ 
                    code: 1,
                    text: 'La URL de alias introducida ya éxiste, por favor elije otra',
                }
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
        vcard : {
            handler() {
                // this.isVCardSelected = this.vcard.template_id !== null
            },
            deep: true
        }
    },
    methods: {
        filterData: function () {
            this.templates = this.templatesAux

            this.templates = this.templates.filter((template) => {
                return template.title.toLowerCase().includes(this.query.toLowerCase()) 
            })
        },
        publishVcard: function (vcard) {
            this.User.publishVcard({vcard_per_user_id:vcard.vcard_per_user_id}, (response) => {
                if (response.s == 1) {
                    vcard.status = response.status
                }
            })
        },
        unPublishVcard: function (vcard) {
            this.User.unPublishVcard({vcard_per_user_id:vcard.vcard_per_user_id}, (response) => {
                if (response.s == 1) {
                    vcard.status = response.status
                }
            })
        },
        deleteCampaign: function (vcard_per_user_id) {
            this.User.deleteCampaign({vcard_per_user_id:vcard_per_user_id}, (response) => {
                if (response.s == 1) {
                    this._getCampaigns()
                }
            })
        },
        getAllTemplates: function () {
            return new Promise((resolve,reject) => {
                this.User.getAllTemplates({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.templates)
                    }

                    reject()
                })
            })
        },
        openStorage: function (catalog_tag_template) 
        {
            this.$emit('openmodal',catalog_tag_template)
        },
        getVcardConfiguration: function () {
            return new Promise((resolve,reject) => {
                this.User.getVcardConfiguration({vcard_per_user_id:this.vcard.vcard_per_user_id}, (response) => {
                    if (response.s == 1) {
                        resolve(response.catalog_tags_template)
                    }

                    reject()
                })
            })
        },
        updateVCard: function (target) {
            this.error = null
            this.vcard.saved = false

            this.User.updateVCard(this.vcard, (response) => {
                if (response.s == 1) {
                    this.vcard_per_user_id = response.vcard_per_user_id

                    target.innerText = 'VCard actualizada'

                    this.vcard.saved = true
                }  else if(response.r == 'ROUTE_EXIST') {
                    this.error = this.ERRORS.ROUTE_EXIST
                }
            })
        },
        getVcard: function () {
            return new Promise((resolve) => {
                this.User.getVcard({vcard_per_user_id:this.vcard.vcard_per_user_id}, (response) => {
                    if (response.s == 1) {
                        resolve(response.vcard)
                    }
                })
            })
        },
        getCountriesPhones: function () {
            return new Promise((resolve) => {
                this.User.getCountriesPhones({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.countries)
                    }
                })
            })
        },
        nextStep: function(step)
        {
            this.step = step
        },
        goToPreview: function(vcard_per_user_id)
        {
            window.open(`../../apps/vCard/preview.php?vpulid=${vcard_per_user_id}`)
        },
        getTemplateThumbnail: function(template_id)
        {
            return `../../src/img/screenshots/${template_id}.png`
        },
        goToAddVCard: function()
        {
            window.location.href = '../../apps/vCard/add'
        },
    },
    mounted() {
        getParam('vpulid')
        {
            this.vcard.vcard_per_user_id = getParam('vpulid')
            this.step = this.STEPS.CONFIGURE_BASICS

            this.getVcard().then((vcard) => {
                this.vcard = vcard

                this.getCountriesPhones().then((countries) => {
                    this.countries = countries

                    this.getVcardConfiguration().then((catalog_tags_template) => {
                        this.vcard.catalog_tags_template = catalog_tags_template
                    }).catch(() => this.vcard.catalog_tags_template = false)
                }).catch(() => this.countries = false)
            }).catch(() => this.vcard.catalog_tags_template = false)

        }
    },
    template : `
        <div v-if="step == STEPS.CONFIGURE_BASICS">
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
                            <button @click="updateVCard($event.target)" class="btn shadow-none  btn-success mb-0">Actualizar</button>
                            
                            <button v-if="vcard.saved" 
                                    @click="goToPreview(vcard.vcard_per_user_id)" class="btn mb-0 ms-2 shadow-none btn-primary">Pre-Visualizar VCard</button>
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
                        <div class="col-12 col-xl-6">
                            <div class="form-floating">
                                <input 
                                    v-model="vcard.title"
                                    :class="vcard.title ? 'is-valid' : ''"
                                    @keydown.enter.exact="$refs.route.focus()"
                                    type="text" class="form-control" id="floatingInput" placeholder="name@example.com">
                                <label for="floatingInput">Nombre de la tarjeta virtual</label>
                            </div>
                        </div>
                        <div class="col-12 col-xl-6">
                            <div class="form-floating"
                                :class="vcard.route ? 'form-floating-badge' :''">
                                <span v-if="vcard.route" class="badge bg-light text-primary">https://vcard.iam.com.mx/</span>
                                <input 
                                    v-model="vcard.route"
                                    :class="vcard.route ? 'is-valid' : 'is-invalid'"
                                    ref="route"
                                    @keydown.space.prevent
                                    type="text" class="form-control" id="floatingInput" placeholder="name@example.com">
                                <label for="floatingInput">URL de alias</label>
                            </div>
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
                                    <div class="row align-items-center">
                                        <div v-if="catalog_tag_template.value" class="col-auto avatar avatar-xl">
                                            <img :src="catalog_tag_template.value" class="img-fluid img-thumbnail">
                                        </div>
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

export { EditvcardViewer } 