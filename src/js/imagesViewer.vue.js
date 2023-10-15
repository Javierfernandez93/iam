import { User } from '../../src/js/user.module.js?v=2.6.4'   

const ImagesViewer = {
    name : 'images-viewer',
    data() {
        return {
            User : new User,
            images : null
        }
    },
    methods: {
        getImages : function() {
            return new Promise((resolve) => {
                this.User.getImages({},(response)=>{
                    if(response.s == 1)
                    {
                        resolve(response.images)
                    }
                })
            })
        }
    },
    mounted() 
    {   
        this.getImages().then((images)=>{
            this.images = images
        })
    },
    template : `
       <div v-if="images" class="row">
            <div v-for="image in images" class="col-12 col-md-6 col-xl-4 p-3">
                <div class="card f-zoom-element-sm">
                    <div class="card-body">
                        <img :src="image.src" :title="image.title" class="img-responsive img-thumbnail"/>
                        
                        <div class="row pt-3">
                            <div class="col-12">
                                <span v-for="tag in image.tag" class="badge bg-secondary me-2">
                                    {{tag.target}}
                                </span>
                            </div>
                        </div>
                        <div class="row pt-3">
                            <div class="col-12 col-xl-6">
                                <div class="d-grid">
                                    <a :href="image.src" class="btn shadow-none mb-0 btn-primary">Ver</a>
                                </div>
                            </div>
                            <div class="col-12 col-xl-6">
                                <div class="d-grid">
                                    <a :href="image.src" :download="image.title" class="btn shadow-none mb-0 btn-success">Descargar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
       </div>
    `,
}

export { ImagesViewer } 