import { User } from '../../src/js/user.module.js?v=2.6.4'   
import { Banner } from '../../src/js/banner.module.js?v=2.6.4'   

const BannertopViewer = {
    name : 'bannertop-viewer',
    props : [],
    emits : [],
    data() {
        return {
            User : new User,
            Banner : new Banner,
            banners : {},
        }
    },
    watch : {
        
    },
    methods: {
        openLinkBanner : function(link) {
            window.open(link)
        },
        getBannersTop : function() {
            return new Promise((resolve,reject) => {
                this.Banner.getBannersTop({},(response)=>{
                    if(response.s == 1)
                    {
                        resolve(response.banners)
                    }

                    reject()
                })
            })
        },
    },
    updated() {
    },
    mounted() 
    {   
        this.getBannersTop().then((banners)=>{
            this.Banner.setBanners(banners)
        })
    },
    template : `
        <div v-if="Banner.banners" class="row mb-3">
            <div class="col-12 mb-3 mb-xl-0 col-xl-6 banner banner-position-1">
                <div @click="openLinkBanner(Banner.getLinkBanner(1))" class="card overflow-hidden cursor-pointer">
                    <img :src="Banner.getSourceBanner(1)"/>

                    <span class="position-absolute text-xs p-2 shadow bg-light text-secondary rounded-bottom start-0 ms-2">Anuncio</span>
                </div>
            </div>
            <div class="col-12 mb-3 mb-xl-0 col-xl-6 banner banner-position-2">
                <div @click="openLinkBanner(Banner.getLinkBanner(2))" class="card overflow-hidden cursor-pointer">
                    <img :src="Banner.getSourceBanner(2)"/>

                    <span class="position-absolute text-xs p-2 shadow bg-light text-secondary rounded-bottom start-0 ms-2">Anuncio</span>
                </div>
            </div>
        </div>
    `,
}

export { BannertopViewer } 