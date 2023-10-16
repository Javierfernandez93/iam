import { User } from '../../src/js/user.module.js?v=2.6.5'   

const LinksViewer = {
    name : 'links-viewer',
    data() {
        return {
            User: new User,
            banners : null
        }
    },
    methods: {
        getBanners() {    
            this.User.getBanners({},(response)=>{
                if(response.s == 1)
                {
                    this.banners = response.banners
                }
            })
        },
    },
    mounted() 
    { 
        this.getBanners()  
    },
    template : `
        <div v-if="banners" class="row align-items-center justify-content-center">
            <div v-for="(banner,index) in banners" class="col-12 col-xl animation-fall-down mb-5" :style="{'--delay':((index+1)*150)+'ms'}">
                <div class="card">
                    <div class="overflow-hidden position-relative border-radius-lg bg-cover h-100" :style="{'background-image':'url('+banner.image+')'}">
                        <div class="mask bg-gradient-dark"></div>

                        <div class="card-body position-relative z-index-1 h-100 text-white">
                            <div class="fs-3 fw-sembold">{{banner.title}}</div>
                            <div style="line-height:3rem" class="fs-2">{{banner.subtitle}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { LinksViewer } 