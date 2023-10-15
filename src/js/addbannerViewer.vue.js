import { User } from '../../src/js/user.module.js?v=2.6.4'   

const AddbannerViewer = {
    name : 'addbanner-viewer',
    props : [],
    emits : [],
    data() {
        return {
            User: new User,
            isCampaignFilled: false,
            banners: null,
            position: null,
            campaign: {
                name: '',
                campaign_banner_per_user_id: null
            },
            ERRORS: {
                INVALID_WIDTH: {
                    code: 1,
                    text: 'El ancho de la imagen no corresponde a las especificaciones',
                },
                INVALID_HEIGHT: {
                    code: 2,
                    text: 'El alto de la imagen no corresponde a las especificaciones',
                },
                INVALID_LINK: {
                    code: 3 ,
                    text: 'El link proporcionado es incorrecto',
                },
                INVALID_IMAGE: {
                    code: 4 ,
                    text: 'Es necesario subir una imagen con el formato correcto',
                },
            }
        }
    },
    watch : {
        // banners: {
        //     handler() {
        //         this.validateUrls()
        //     },
        //     deep: true
        // }
    },
    methods: {
        getCampaign: function () {
            return new Promise((resolve,reject) => {
                this.User.getCampaign({campaign_banner_per_user_id:this.campaign.campaign_banner_per_user_id}, (response) => {
                    if (response.s == 1) {
                        resolve(response.campaign)
                    }

                    reject()
                })
            })
        },
        getBanners: function () {
            return new Promise((resolve,reject) => {
                this.User.getBanners({campaign_banner_per_user_id:this.campaign.campaign_banner_per_user_id}, (response) => {
                    if (response.s == 1) {
                        resolve(response.banners)
                    }

                    reject()
                })
            })
        },
        setBannerPerCampaign: function (campaignBannerPerUser,source) {
            campaignBannerPerUser.source = source
        },
        saveBannerPerCampaign: function (banner,source,target) {
            this.User.saveBannerPerCampaign({link:banner.link,catalog_banner_id:banner.catalog_banner_id,source:source,campaign_banner_per_user_id:this.campaign.campaign_banner_per_user_id}, (response) => {
                if (response.s == 1) {
                    banner.source = source

                    if(target)
                    {
                        target.innerText = 'Acualizado con éxito'
                    }
                } else if(response.r == 'INVALID_LINK') {
                    banner.error = this.ERRORS.INVALID_LINK
                } else if(response.r == 'INVALID_IMAGE') {
                    banner.error = this.ERRORS.INVALID_IMAGE
                }
            })
        },
        addBanner: function (campaignBannerPerUser) {
            const { campaign_banner_per_user_id } = campaignBannerPerUser
            window.location.href  = `../../apps/banner/addBanner?cbpid=${campaign_banner_per_user_id}`
        },
        setFocusCatalogBanner: function (position) {
            this.position = position
        },
        uploadFile: function (event,banner) 
        {
            banner.error = null
            return new Promise((resolve) => {
                let files = $(event.target).prop('files');
                const file = files[0]

                this.loadImage(file,banner).then(() => {
                    var form_data = new FormData();
                
                    form_data.append("file", file);
                
                    this.User.uploadImageBanner(form_data,$(".progress-chat").find(".progress-bar"),(response)=>{
                        if(response.s == 1)
                        {
                            resolve(response.target_path)
                        }
                    })
                }).catch( error => { banner.error = error })
            })
        },
        loadImage: function (file,banner) {
            var _URL = window.URL || window.webkitURL;

            return new Promise((resolve,reject)=>{
                const { INVALID_HEIGHT, INVALID_WIDTH } = this.ERRORS
                const { width, height } = banner

                const image = new Image();
                
                image.src = _URL.createObjectURL(file);

                image.onload = function() {
                    console.log(this.width,this.height)
                    if(this.width == width)
                    {
                        if(this.height == height)
                        {
                            resolve()
                        } else {
                            reject(INVALID_HEIGHT)
                        }
                    } else {
                        reject(INVALID_WIDTH)
                    }
                }
            })
        },
        openFileManager: function (_ref) {
            Object.keys(this.$refs).forEach(ref => {
                if(ref == _ref)
                {
                    this.$refs[ref].click()
                }
            })
        },
    },
    mounted() {
        if(getParam('cbpid'))
        {
            this.campaign.campaign_banner_per_user_id = getParam('cbpid')
            this.getBanners().then((banners) => {
                this.banners = banners

                this.getCampaign().then(campaign => this.campaign  = campaign)
                    .catch(() => this.campaign = false)
            }).catch(() => this.campaign = false)
        }
    },
    template : `
        <div v-if="banners">
            <div class="row">
                <div class="col-12 col-xl-9">
                    <div v-if="campaign" class="mb-3">
                        <div class="card bg-dark">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="badge bg-gradient-warning fs-4">{{campaign.name.getAcronime()}}</span>
                                    </div>
                                    <div class="col">
                                        <span class="badge border border-light text-xxs text-light">Campaña</span>
                                        <div class="text-white fw-semibold fs-4">{{campaign.name}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-for="banner in banners">
                        <div @mouseover="setFocusCatalogBanner(banner.position)" 
                            class="card mb-3 cursor-pointer"
                            :class="banner.position == position ? 'shadow-xl' : ''">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col-auto">  
                                        <span class="badge bg-secondary"><i class="bi bi-badge-ad fs-4"></i></span>
                                    </div>
                                    
                                    <div class="col">
                                        <span class="badge text-secondary p-0">Posición </span>
                                        <div class="text-primary fw-semibold text-uppercase">{{banner.name}}</div>
                                        <div class=""><span class="badge p-0 text-secondary">* Formato de imagen PNG JPG JPEG GIF con dimensiones {{banner.height}} pixeles de alto por {{banner.width}} pixeles de ancho</span></div>
                                    </div>

                                    <div class="col-auto">
                                        <button @click="saveBannerPerCampaign(banner,banner.source,$event.target)" class="btn btn-success m-0">Actualizar banner</button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <div class="row align-items-center">
                                            <div class="col-12 col-xl-6">
                                                <div class="form-floating">
                                                    <input 
                                                        v-model="banner.link"
                                                        :class="banner.link.isValidUrl() ? 'is-valid' : 'is-invalid'"
                                                        type="url" class="form-control" id="floatingInput" placeholder="name@example.com">
                                                    <label for="floatingInput">Link</label>

                                                </div>
                                            </div>
                                            
                                            <div class="col-12 col-xl-6">
                                                <button class="btn btn-lg m-0 w-100" @click="openFileManager(banner.position)"
                                                    :class="!banner.source ? 'btn-primary' : 'btn-light'">
                                                    <span v-text="!banner.source ? 'Seleccionar imagen' : 'Cambiar imagen'"></span>
                                                </button>
                                                
                                                <input class="d-none" :ref="'' + banner.position" @change="uploadFile($event,banner).then((src) => { setBannerPerCampaign(banner,src)})" capture="filesystem" type="file" accept=".jpg, .png, .jpeg" />
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="banner.source" class="col-4">
                                        <img :src="banner.source" class="img-fluid img-thumbnail">
                                    </div>
                                </div>
                                <div v-if="banner.error" class="mt-3">
                                    <div class="alert alert-warning text-white">
                                        {{banner.error.text}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-3">
                    <div class="card overflow-hidden mb-3">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-auto">
                                    <i class="bi bi-grid-1x2-fill"></i>
                                </div>
                                <div class="col">
                                    Posición del banner
                                </div>
                            </div>
                        </div>
                        <div class="row box-position">
                            <div class="col-3 bg-light">
                                <div class="row align-items-end h-100 p-3">
                                    <div class="col rounded-2"
                                        :class="position == 3 ? 'bg-gradient-warning text-white fw-semibold' : 'bg-secondary'">
                                        <span v-text="position == 3 ? 'F7' : '-'"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-9">
                                <div class="row p-3 gx-3">
                                    <div class="col-6 rounded-2"
                                        :class="position == 1 ? 'bg-gradient-warning text-white fw-semibold' : 'bg-secondary'">
                                        <span v-text="position == 1 ? 'F7' : '-'"></span>
                                    </div>
                                    <div class="col-6 rounded-2"
                                        :class="position == 2 ? 'bg-gradient-warning text-white fw-semibold' : 'bg-secondary'">
                                        <span v-text="position == 2 ? 'F7' : '-'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { AddbannerViewer } 