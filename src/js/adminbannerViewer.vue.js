import { UserSupport } from '../../src/js/userSupport.module.js?t=1.1.4'   

const AdminbannerViewer = {
    name : 'adminbanner-viewer',
    data() {
        return {
            UserSupport: new UserSupport,
            banners : null
        }
    },
    methods: {
        getBanners() {
            this.UserSupport.getBanners({},(response)=>{
                if(response.s == 1)
                {
                    this.banners = response.banners
                }
            })
        },
        saveBanner(banner) {
            banner.busy = true
            this.UserSupport.saveBanner({banner:banner},(response)=>{                
                if(response.s == 1)
                {
                    banner.busy = false
                }
            })
        },
        openFileManager() 
        {
            this.$refs.file.click()
        },
        uploadFile(target,banner) 
        {
            let files = $(target).prop('files');
            var form_data = new FormData();
          
            form_data.append("file", files[0]);
          
            this.UserSupport.uploadImageBanner(form_data,$(".progress-chat").find(".progress-bar"),(response)=>{
              if(response.s == 1)
              {
                banner.image = response.target_path
              }
            });
        },
    },
    mounted() 
    {   
        this.getBanners()
    },
    template : `
        <div class="row px-3 py-3">
            <div class="col bg-secondary opacity-2 line-middle mt-2">
            </div>
            <div class="col-auto text-uppercase text-secondary">
                Banners publicitarios
            </div>
            <div class="col bg-secondary opacity-2 line-middle mt-2">
            </div>
        </div>
        <div v-if="banners" class="row justify-content-center align-items-center">
            <div v-for="banner in banners" class="col-12 col-xl">
                <div class="card card-body mb-3">
                    <div class="mb-3">                                        
                        <img v-if="banner.image" :src="banner.image" class="card-img-top"/>

                        <input class="d-nones" ref="file" @change="uploadFile($event.target,banner)" capture="filesystem" type="file" accept=".jpg, .png, .jpeg" />
                    </div>

                    <textarea type="text" v-model="banner.title" class="form-control mb-3">{{banner.title}}</textarea>
                    <textarea type="text" v-model="banner.subtitle" class="form-control">{{banner.subtitle}}</textarea>

                    <div class="d-grid pt-3">
                        <button :disabled="banner.busy" @click="saveBanner(banner)" class="btn btn-primary mb-0 shadow-none" v-text="banner.busy ? '...': 'Actualizar'">
                        
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { AdminbannerViewer } 