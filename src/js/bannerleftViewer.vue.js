import { User } from '../../src/js/user.module.js?v=2.6.4'   
import { Banner } from '../../src/js/banner.module.js?v=2.6.4'   

const BannerleftViewer = {
    name : 'bannerleft-viewer',
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
        getBannersLeft : function() {
            return new Promise((resolve,reject) => {
                this.Banner.getBannersLeft({},(response)=>{
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
        this.getBannersLeft().then((banners)=>{
            this.Banner.setBanners(banners)
        })
    },
    template : `
        <div v-if="Banner.banners" class="row mt-3">
            <div class="col-12 banner banner-position-3">
                <div @click="openLinkBanner(Banner.getLinkBanner(3))" class="card overflow-hidden cursor-pointer">
                    <img :src="Banner.getSourceBanner(3)"/>
                    <span class="position-absolute text-xs p-2 shadow bg-light text-secondary rounded-bottom start-0 ms-2">Anuncio</span>
                </div>
            </div>
        </div>
    `,
}

export { BannerleftViewer } 