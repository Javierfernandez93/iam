import { User } from './user.module.js'

const StorageViewer = {
    name : 'storage-viewer',
    data() {
        return {
            User : new User,
            storage: {
                percentaje: 0,
                size: null,
                max_size: null,
                files: null,
                filesAux: null,
            },
            catalog_tag_template: null,
            grid: true,
            query: null
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
            this.storage.files = this.storage.filesAux

            this.storage.files = this.storage.files.filter((file) => {
                return file.file.toLowerCase().includes(this.query.toLowerCase())
            })
        },
        copyLink: function(link,event) {     
            navigator.clipboard.writeText(link).then(() => {
                event.target.innerText = 'copiado'
            });
        },
        uploadStorageFile: function (element) 
        {
            return new Promise((resolve) => {
                const files = $(element).prop('files');

                let form_data = new FormData();
            
                form_data.append("file", files[0]);
            
                this.User.uploadStorageFile(form_data,(response)=>{
                    if(response.s == 1)
                    {
                        resolve(response.target_path)
                    }
                });
            })
        },
        openFileManager: function (ref) 
        {
            ref.click();
        },
        insertLink: function(link) {    
            this.catalog_tag_template.value = link

            $(this.$refs.modal).modal('hide')
        },
        openPreview: function(link) {     
            window.open(link)
        },
        deleteFile: function(full_path) {  
            this.User.deleteFile({full_path:full_path},(response)=>{
                if(response.s == 1)
                {
                    this.getStorage()
                }
            })   
        },
        getMyStorageFiles: function() {     
            this.User.getMyStorageFiles({},(response)=>{
                if(response.s == 1)
                {
                    this.storage.filesAux = response.files
                    this.storage.files = this.storage.filesAux
                }
            })
        },
        getStorageCapacity: function() {     
            this.User.getStorageCapacity({},(response)=>{
                if(response.s == 1)
                {
                    this.storage.percentaje = response.percentaje
                    this.storage.size = response.size
                    this.storage.max_size = response.max_size
                }
            })
        },
        toggleMode: function() {     
            this.mode = this.mode == this.MODES.LIST ? this.MODES.ITEMS : this.MODES.LIST 
        },
        openModal: function(catalog_tag_template) {     
            this.catalog_tag_template = catalog_tag_template
            $(this.$refs.modal).modal('show')
        },
        getStorage: function()
        {
            this.getMyStorageFiles()
            this.getStorageCapacity()
        }
    },
    mounted() 
    {   
        this.getStorage()
        
    },
    template : `
        <div ref="modal" class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Almacenamiento</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 col-xl-2">
                                <div class="card bg-soft-primary text-primary">
                                    <div class="card-body">
                                        <div class="row align-items-center pb-3">
                                            <div class="col">
                                                <span class="badge bg-gradient-primary text-white fs-6"><i class="bi bi-cloud"></i></span>
                                            </div>
                                            <div class="col-auto">
                                                <span class="">{{storage.percentaje}}% usado</span>
                                            </div>
                                        </div>
                                        
                                        <div class="progress">
                                            <div 
                                                :style="{width: storage.percentaje + '%'}"
                                                class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="text-center mt-2">{{storage.size}} de {{storage.max_size}}</div>    
                                    </div>
                                </div>
                                <div class="card bg-soft-success text-success text-center shadow-xl cursor-pointer z-zoom-element-sm">
                                    <div @click="openFileManager($refs.uploadFile)" class="card-body">
                                        Subir archivo
                                    </div>

                                    <input class="upload-file d-none" ref="uploadFile" @change="uploadStorageFile($refs.uploadFile).then((res)=>{ getStorage()})" capture="filesystem" type="file" accept=".jpg, .png, .jpeg, .pdf, .doc, .docx, .xls, .mp4, .mov, .avi" />
                                </div>
                            </div>
                            <div class="col-12 col-xl-10">
                                <div class="input-group input-group-lg input-group-merge mb-3">
                                    <input 
                                        :autofocus="true"
                                        v-model="query" type="text" class="form-control border-0 shadow-lg" placeholder="Buscar archivo...">

                                    <button
                                        @click="grid = !grid"
                                        class="btn btn-dark">
                                        <span v-if="grid">
                                            <i class="bi bi-list"></i>
                                        </span>
                                        <span v-else>
                                            <i class="bi bi-grid-fill"></i>
                                        </span>
                                    </button>
                                </div>
                                <div v-if="storage.files">
                                    <div v-if="grid" class="row">
                                        <div v-for="file in storage.files" class="col-12 col-xl-3">
                                            <div class="card card-item">
                                                <div v-if="file.file.isImage()">
                                                    <img :src="file.full_path" class="card-img-top">
                                                </div>
                                                <div class="card-body">
                                                    <div>
                                                        <span v-if="file.file.isImage()"
                                                            class="badge bg-soft-primary text-primary">
                                                            Imagen
                                                        </span>
                                                        <span v-else-if="file.file.isFile()"
                                                            class="badge bg-soft-primary text-primary">
                                                            Archivo
                                                        </span>
                                                        <span v-else-if="file.file.isVideo()"
                                                            class="badge bg-soft-primary text-primary">
                                                            Video
                                                        </span>
                                                        <div class="text-truncate">
                                                            {{file.file}}
                                                        </div>
                                                    </div>

                                                    <div class="btn-group mt-3">
                                                        <button @click="copyLink(file.full_path.replaceFullRoute(),$event)" class="btn btn-light btn-xs" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Copiar link al clipboard" aria-label="Copiar link al clipboard"><i class="bi bi-clipboard"></i></button>
                                                        <button @click="insertLink(file.full_path)" class="btn btn-light btn-xs" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Insertar link" aria-label="Insertar link"><i class="bi bi-clipboard-check"></i></button>
                                                        <button @click="openPreview(file.full_path)" class="btn btn-light btn-xs" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Previsualizar" aria-label="Previsualizar"><i class="bi bi-eye"></i></button>
                                                        <button @click="deleteFile(file.full_path)" class="btn btn-danger btn-xs" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Eliminar" aria-label="Eliminar"><i class="bi bi-trash"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else>
                                        <ul class="list-group">
                                            <li v-for="file in storage.files" class="list-group-item">
                                                <div class="row align-items-center">
                                                    <div class="col-auto">
                                                        <span v-if="file.file.isImage()"
                                                            class="badge bg-soft-primary text-primary">
                                                            Imagen
                                                        </span>
                                                        <span v-else-if="file.file.isFile()"
                                                            class="badge bg-soft-primary text-primary">
                                                            Archivo
                                                        </span>
                                                        <span v-else-if="file.file.isVideo()"
                                                            class="badge bg-soft-primary text-primary">
                                                            Video
                                                        </span>
                                                    </div>
                                                    <div class="col">
                                                        {{file.file}}
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="btn-group">
                                                            <button @click="copyLink(file.full_path.replaceFullRoute(),$event)" class="btn btn-light btn-xs" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Copiar link al clipboard" aria-label="Copiar link al clipboard"><i class="bi bi-clipboard"></i></button>
                                                            <button @click="insertLink(file.full_path)" class="btn btn-light btn-xs" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Insertar link" aria-label="Insertar link"><i class="bi bi-clipboard-check"></i></button>
                                                            <button @click="openPreview(file.full_path)" class="btn btn-light btn-xs" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Previsualizar" aria-label="Previsualizar"><i class="bi bi-eye"></i></button>
                                                            <button @click="deleteFile(file.full_path)" class="btn btn-danger btn-xs" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Eliminar" aria-label="Eliminar"><i class="bi bi-trash"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    `
}

export { StorageViewer } 