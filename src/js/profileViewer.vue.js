import { User } from '../../src/js/user.module.js?v=2.6.6'   

const ProfileViewer = {
    name : 'profile-viewer',
    data() {
        return {
            User: new User,
            user: null,
            countries : null,
            timezones : null,
            lastReferrals : null,
        }
    },
    watch : {
        user : {
            handler() {
                this.editProfile()
            },
            deep: true
        },
    },
    methods: {
        getProfile() {
            return new Promise((resolve) => {
                this.User.getProfile({include_countries:true},(response)=>{
                    if(response.s == 1)
                    {
                        resolve(response)
                    }
                })
            }) 
        },
        editProfile() {
            this.User.editProfile(this.user,(response)=>{
                if(response.s == 1)
                {
                    
                }
            })
        },
        getLastReferrals() {
            return new Promise((resolve) => {
                this.User.getLastReferrals({},(response)=>{
                    if(response.s == 1)
                    {
                        resolve(response.lastReferrals)
                    }
                })
            })
        },
        getCatalogTimezones() {
            return new Promise((resolve) => {
                this.User.getCatalogTimezones({},(response)=>{
                    if(response.s == 1)
                    {
                        resolve(response.timezones)
                    }
                })
            })
        },
        checkFields() {
        },
        openFileManager() 
        {
            this.$refs.file.click()
        },
        uploadFile() 
        {
            $(".progress").removeClass("d-none")

            let files = $(this.$refs.file).prop('files');
            var form_data = new FormData();
          
            form_data.append("file", files[0]);
          
            this.User.uploadImageProfile(form_data,$(".progress-chat").find(".progress-bar"),(response)=>{
              if(response.s == 1)
              {
                  this.user.image = response.target_path
              }
            });
        },
    },
    mounted() 
    {   
        if(getParam("e"))
        {
            setTimeout(()=>{
                const element = getParam("e")
                _scrollTo($(`#${element}`).offset().top)
            },1000)
        }
        this.getProfile().then((response) => {
            this.getCatalogTimezones().then((timezones) => {
                this.timezones = timezones

                $(this.$refs.phone).mask('(00) 0000-0000')

                this.user = response.user
                this.countries = response.countries

                this.getLastReferrals().then(lastReferrals => this.lastReferrals = lastReferrals)
            })
        })
    },
    template : `
        <div v-if="user" class="mt-n5">
            <div class="container-fluid">
                <div class="page-header min-height-300 border-radius-xl mt-4"
                    style="background-image: url('../../src/img/bg-marketing.jpg?t=1'); background-position-y: 50%;">
                    <span class="mask bg-gradient-dark opacity-6"></span>
                </div>
                <div class="card blur shadow-blur card-body mx-4 mt-n6 overflow-hidden">
                    <div class="row gx-4">
                        <div class="col-auto">
                            <div class="avatar avatar-xl avatar-editable overflow-hidden img-upload position-relative" @click="openFileManager">
                                                                
                                <div v-if="user.image" class="avatar avatar-xl">
                                    <img :src="user.image" alt="usuario"
                                        class="border-radius-lg shadow">
                                </div>
                                <div v-else>
                                    <div v-if="user.names" class="avatar avatar-xl bg-dark">
                                        {{ user.names.getFirstLetter() }}
                                    </div>
                                </div>

                                <input class="d-none" ref="file" @change="uploadFile($event)" capture="filesystem" type="file"
                                    accept=".jpg, .png, .jpeg" />
                            </div>
                        </div>
                        <div class="col my-auto">
                            <div class="h-100">
                                <h5 class="mb-1 fw-sembold text-dark">
                                    {{ user.names }}
                                </h5>
                                <p class="mb-0 text-secondary text-sm">
                                    {{ user.email }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid py-4">
                <div class="row mb-3">
                    <div class="col-12 col-xl-6 mb-3 mb-xl-0">
                        <div class="card mb-3">
                            <div class="card-header pb-0 p-3">
                                <h6 class="mb-0">Configuración de plataforma</h6>
                            </div>
                            <div class="card-body p-3">
                                <h6 class="text-uppercase text-body text-xs font-weight-bolder">Compras</h6>
                                <ul class="list-group">
                                    <li class="list-group-item border-0 px-0">
                                        <div class="form-check form-switch ps-0">
                                            <input class="form-check-input ms-auto" type="checkbox" id="referral_email" v-model="user.referral_email">
                                            <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0" for="referral_email">Recibir información de comrpas</label>
                                        </div>
                                    </li>
                                </ul>
                                <h6 class="text-uppercase text-body text-xs font-weight-bolder mt-4">Cuenta</h6>
                                <ul class="list-group">
                                    <li class="list-group-item border-0 px-0">
                                        <div class="form-check form-switch ps-0">
                                            <input class="form-check-input ms-auto" type="checkbox" id="info_email" v-model="user.info_email">
                                            <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0" for="info_email">Recibir correos electrónicos con información</label>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-6" id="profile">
                        <div class="card h-100">
                            <div class="card-header pb-0 p-3">
                                <div class="row">
                                    <div class="col-md-8 d-flex align-items-center">
                                        <h6 class="mb-0">Información de perfil</h6>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <a href="javascript:;">
                                            <i class="fas fa-user-edit text-secondary text-sm" data-bs-toggle="tooltip"
                                                data-bs-placement="top" aria-hidden="true" aria-label="Edit Profile"></i><span
                                                class="sr-only">Edit Profile</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div v-if="user" class="card-body p-3">
                                <ul class="list-group">
                                    <li class="list-group-item border-0 ps-0 text-sm">
                                        <label>ID de afiliado</label>
                                        <input type="text" readonly v-model="user.company_id" disabled class="form-control d-inline" placeholder="Email"/>
                                    </li>

                                    <li class="list-group-item border-0 ps-0 pt-0 text-sm">
                                        <label>Nombre</label>
                                        <input type="text" v-model="user.names" class="form-control d-inline" placeholder="Nombre"/>
                                    </li>
                                    <li class="list-group-item border-0 ps-0 text-sm">
                                        <label>Teléfono</label>
                                        <div class="row">
                                            <div class="col">
                                                <select class="form-select" v-model="user.country_id" aria-label="Selecciona tu país">
                                                    <option>Selecciona tu país</option>
                                                    <option v-for="country in countries" v-bind:value="country.country_id">
                                                        {{ country.nicename }} <span v-if="country.phone_code > 0">+ {{ country.phone_code }}</span>
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-auto">
                                                <input type="text" v-model="user.phone" ref="phone" class="form-control" placeholder="Celular"/> 
                                            </div>
                                        </div>
                                    </li>
                                    <li v-if="timezones" class="list-group-item border-0 ps-0 text-sm">
                                        <label>Zona horaria</label>
                                        <div class="row">
                                            <div class="col">
                                                <select class="form-select" v-model="user.catalog_timezone_id" aria-label="Selecciona tu zona horaria">
                                                    <option>Selecciona tu zona horaria</option>
                                                    <option v-for="timezone in timezones" v-bind:value="timezone.catalog_timezone_id">
                                                        {{ timezone.timezone }} 
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item border-0 ps-0 text-sm">
                                        <label>Dirección</label>
                                        <input type="text" v-model="user.address" class="form-control d-inline" placeholder="address"/>
                                    </li>
                                    <li class="list-group-item border-0 ps-0 text-sm">
                                        <label>Correo electrónico</label>
                                        <input type="text" v-model="user.email" :disabled="true" class="form-control d-inline" placeholder="Email"/>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-4">
                        <div v-if="lastReferrals" class="card h-100">
                            <div class="card-header pb-0 p-3">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="mb-0">Últimos referidos</h6>
                                    </div>
                                    <div v-if="lastReferrals.length > 0" class="col-auto">
                                        <span class="badge bg-secondary">Cantidad {{lastReferrals.length}}</span>
                                    </div>
                                </div>
                            </div>
                            <div v-if="lastReferrals" class="card-body p-4">
                                <ul class="list-group">
                                    <li v-for="lastReferral of lastReferrals" class="list-group-item border-0 px-0 mb-2">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <div v-if="lastReferral.image" class="avatar avatar-sm">
                                                    <img :src="lastReferral.image" alt="referido"
                                                        class="border-radius-lg shadow">
                                                </div>
                                                <div v-else>
                                                    <div class="avatar avatar-sm bg-dark">
                                                        {{ lastReferral.names.getFirstLetter() }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <h6 class="mb-0 text-sm">{{lastReferral.names}}</h6>
                                                <p class="mb-0 text-xs">Miembro desde {{lastReferral.signup_date.formatDate()}}</p>
                                            </div>
                                            <div class="col-auto">
                                                <span class="badge bg-primary">ID {{lastReferral.user_login_id}}</span>
                                            </div>
                                        </li>
                                    </li>
                                </ul>
                            </div>
                            <div v-else class="card-body">
                                <div class="text-center">
                                    No tenemos información de tus últimos referidos, si aún no has invitado a unirse a tu grupo puedes hacerlo desde tu <a class="fw-semibold" href="../../apps/backoffice"><u>Oficina virtual</u></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row d-none">
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-xl-6 mb-xl-0 mb-4">
                                <div class="card bg-transparent shadow-xl">
                                    <div class="overflow-hidden position-relative border-radius-xl"
                                        style="background-image: url('../../src/img/curved-images/curved14.jpg');">
                                        <span class="mask bg-gradient-dark"></span>
                                        <div class="card-body position-relative z-index-1 p-3">
                                            <i class="fas fa-wifi text-white p-2" aria-hidden="true"></i>
                                            <h5 class="text-white mt-4 mb-5 pb-2">
                                                4562&nbsp;&nbsp;&nbsp;1122&nbsp;&nbsp;&nbsp;4594&nbsp;&nbsp;&nbsp;7852</h5>
                                            <div class="d-flex">
                                                <div class="d-flex">
                                                    <div class="me-4">
                                                        <p class="text-white text-sm opacity-8 mb-0">Card Holder</p>
                                                        <h6 class="text-white mb-0">Jack Peterson</h6>
                                                    </div>
                                                    <div>
                                                        <p class="text-white text-sm opacity-8 mb-0">Expires</p>
                                                        <h6 class="text-white mb-0">11/22</h6>
                                                    </div>
                                                </div>
                                                <div class="ms-auto w-20 d-flex align-items-end justify-content-end">
                                                    <img class="w-60 mt-2" src="../../src/img/logos/mastercard.png" alt="logo">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header mx-4 p-3 text-center">
                                                <div
                                                    class="icon icon-shape icon-lg bg-gradient-primary shadow text-center border-radius-lg">
                                                    <i class="fas fa-landmark opacity-10" aria-hidden="true"></i>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0 p-3 text-center">
                                                <h6 class="text-center mb-0">Salary</h6>
                                                <span class="text-xs">Belong Interactive</span>
                                                <hr class="horizontal dark my-3">
                                                <h5 class="mb-0">+$2000</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-md-0 mt-4">
                                        <div class="card">
                                            <div class="card-header mx-4 p-3 text-center">
                                                <div
                                                    class="icon icon-shape icon-lg bg-gradient-primary shadow text-center border-radius-lg">
                                                    <i class="fab fa-paypal opacity-10" aria-hidden="true"></i>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0 p-3 text-center">
                                                <h6 class="text-center mb-0">Paypal</h6>
                                                <span class="text-xs">Freelance Payment</span>
                                                <hr class="horizontal dark my-3">
                                                <h5 class="mb-0">$455.00</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-lg-0 mb-4">
                                <div class="card mt-4">
                                    <div class="card-header pb-0 p-3">
                                        <div class="row">
                                            <div class="col-6 d-flex align-items-center">
                                                <h6 class="mb-0">Payment Method</h6>
                                            </div>
                                            <div class="col-6 text-end">
                                                <a class="btn bg-gradient-dark mb-0" href="javascript:;"><i class="fas fa-plus"
                                                        aria-hidden="true"></i>&nbsp;&nbsp;Add New Card</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="row">
                                            <div class="col-md-6 mb-md-0 mb-4">
                                                <div
                                                    class="card card-body border card-plain border-radius-lg d-flex align-items-center flex-row">
                                                    <img class="w-10 me-3 mb-0" src="../../src/img/logos/mastercard.png"
                                                        alt="logo">
                                                    <h6 class="mb-0">
                                                        ****&nbsp;&nbsp;&nbsp;****&nbsp;&nbsp;&nbsp;****&nbsp;&nbsp;&nbsp;7852
                                                    </h6>
                                                    <i class="fas fa-pencil-alt ms-auto text-dark cursor-pointer"
                                                        data-bs-toggle="tooltip" data-bs-placement="top" aria-hidden="true"
                                                        aria-label="Edit Card"></i><span class="sr-only">Edit Card</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div
                                                    class="card card-body border card-plain border-radius-lg d-flex align-items-center flex-row">
                                                    <img class="w-10 me-3 mb-0" src="../../src/img/logos/visa.png" alt="logo">
                                                    <h6 class="mb-0">
                                                        ****&nbsp;&nbsp;&nbsp;****&nbsp;&nbsp;&nbsp;****&nbsp;&nbsp;&nbsp;5248
                                                    </h6>
                                                    <i class="fas fa-pencil-alt ms-auto text-dark cursor-pointer"
                                                        data-bs-toggle="tooltip" data-bs-placement="top" aria-hidden="true"
                                                        aria-label="Edit Card"></i><span class="sr-only">Edit Card</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card h-100">
                            <div class="card-header pb-0 p-3">
                                <div class="row">
                                    <div class="col-6 d-flex align-items-center">
                                        <h6 class="mb-0">Invoices</h6>
                                    </div>
                                    <div class="col-6 text-end">
                                        <button class="btn btn-outline-primary btn-sm mb-0">View All</button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-3 pb-0">
                                <ul class="list-group">
                                    <li
                                        class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark font-weight-bold text-sm">March, 01, 2020</h6>
                                            <span class="text-xs">#MS-415646</span>
                                        </div>
                                        <div class="d-flex align-items-center text-sm">
                                            $180
                                            <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4"><i
                                                    class="fas fa-file-pdf text-lg me-1" aria-hidden="true"></i> PDF</button>
                                        </div>
                                    </li>
                                    <li
                                        class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                        <div class="d-flex flex-column">
                                            <h6 class="text-dark mb-1 font-weight-bold text-sm">February, 10, 2021</h6>
                                            <span class="text-xs">#RV-126749</span>
                                        </div>
                                        <div class="d-flex align-items-center text-sm">
                                            $250
                                            <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4"><i
                                                    class="fas fa-file-pdf text-lg me-1" aria-hidden="true"></i> PDF</button>
                                        </div>
                                    </li>
                                    <li
                                        class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                        <div class="d-flex flex-column">
                                            <h6 class="text-dark mb-1 font-weight-bold text-sm">April, 05, 2020</h6>
                                            <span class="text-xs">#FB-212562</span>
                                        </div>
                                        <div class="d-flex align-items-center text-sm">
                                            $560
                                            <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4"><i
                                                    class="fas fa-file-pdf text-lg me-1" aria-hidden="true"></i> PDF</button>
                                        </div>
                                    </li>
                                    <li
                                        class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                        <div class="d-flex flex-column">
                                            <h6 class="text-dark mb-1 font-weight-bold text-sm">June, 25, 2019</h6>
                                            <span class="text-xs">#QW-103578</span>
                                        </div>
                                        <div class="d-flex align-items-center text-sm">
                                            $120
                                            <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4"><i
                                                    class="fas fa-file-pdf text-lg me-1" aria-hidden="true"></i> PDF</button>
                                        </div>
                                    </li>
                                    <li class="list-group-item border-0 d-flex justify-content-between ps-0 border-radius-lg">
                                        <div class="d-flex flex-column">
                                            <h6 class="text-dark mb-1 font-weight-bold text-sm">March, 01, 2019</h6>
                                            <span class="text-xs">#AR-803481</span>
                                        </div>
                                        <div class="d-flex align-items-center text-sm">
                                            $300
                                            <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4"><i
                                                    class="fas fa-file-pdf text-lg me-1" aria-hidden="true"></i> PDF</button>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { ProfileViewer } 