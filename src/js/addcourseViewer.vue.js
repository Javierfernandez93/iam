import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.4'

const AddcourseViewer = {
    name : 'addcourse-viewer',
    emits : ['addSession','selectSession'],
    data() {
        return {
            UserSupport : new UserSupport,
            min_number_of_sessions : 0,
            catalog_courses : {},
            catalog_currencies : {},
            editor : null,
            TARGETS: [
                {
                    code: -1,
                    text: 'Para todos',
                },
                {
                    code: 0,
                    text: 'Sólo no activos',
                },
                {
                    code: 1,
                    text: 'Sólo activos',
                }
            ],
            course : {
                title : null,
                tag : null,
                image : null,
                duration : null,
                catalog_course_id : 1,
                catalog_currency_id : 154,
                free : true, // PREMIUM
                sessions : [],
                target : -1
            },
        }
    },
    methods: {
        saveCourse()
        {
            this.course.tag = JSON.stringify(this.$refs.tag.value)

            this.UserSupport.saveCourse(this.course,(response)=>{
                if(response.s == 1)
                {
                    alertInfo({
                        icon:'<i class="bi bi-ui-checks"></i>',
                        message: `Guardamos tu curso ${this.course.title}`,
                        _class:'bg-gradient-success text-white'
                    })
                }
            })
        },
        openFileManager(ref) 
        {
            ref.click();
        },
        updateCourseCover(path) 
        {
            this.course.image = path
        },
        uploadCoverCourse(element) 
        {
            return new Promise((resolve) => {
                const files = $(element).prop('files');
                let form_data = new FormData();
            
                form_data.append("file", files[0]);
            
                this.UserSupport.uploadCoverCourse(form_data,$(".progress").find(".progress-bar"),(response)=>{
                    if(response.s == 1)
                    {
                        resolve(response.target_path)
                    }
                });
            })
        },
        getSessionKey(unique_id)
        {
            let key = -1

            if(this.course.sessions.length > 0)
            {
                for(let i = 0; i < this.course.sessions.length; i++)
                {
                    if(this.course.sessions[i].unique_id == unique_id)   
                    {
                        key = i
                    }
                }
            }

            return key
        },
        deleteSession(unique_id)
        {
            this.course.sessions = this.course.sessions.filter(function(session) { 
                return session.unique_id != unique_id 
            })
        },
        saveSession(session)
        {
            const key = this.getSessionKey(session.unique_id)

            if(key == -1)
            {
                this.course.sessions.push(session)
            } else {
                this.course.sessions[key] = session
            }
        },
        getCourseFormAddVars() 
        {
            return new Promise((resolve) => {
                this.UserSupport.getCourseFormAddVars({},(response)=>{
                    this.catalog_currencies = response.catalog_currencies
                    this.catalog_courses = response.catalog_courses

                    resolve()
                })
            })
        },
        init()
        {
            $("#price").mask('#,##0.00', { reverse: true });
            $("#duration").mask('00:00');

            var input = document.querySelector('input[name=basic]');
            new Tagify(input);
        },
        initEditor()
        {
            this.editor = new Quill('#editor', {
                theme: 'snow'
            });

            this.editor.on('text-change', () => {
                this.course.description = this.editor.root.innerHTML
            });
        },
    },
    mounted() 
    {   
        this.getCourseFormAddVars().then(()=>{
            this.init()
            this.initEditor()
        })
    },
    template : `
        <div class="row mb-3 align-items-center">
            <div class="col-12 col-xl">
                <h4>Añadir curso</h4>
            </div>
            <div class="col-12 col-xl-auto">
                <button 
                    :disabled="course.sessions.length < min_number_of_sessions"
                    @click="saveCourse" class="btn btn-primary me-2">
                    <t>Guardar curso</t>
                </button>

                <button @click="$emit('addSession')" class="btn btn-primary ">
                    <t>Añadir lección</t>
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="d-flex row justify-content-center">
                    <div class="col-12 col-xl-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="row pb-3 align-items-center">
                                    <div class="col lead">
                                        <t>Información</t>
                                    </div>
                                    <div class="col-auto form-check form-switch">
                                        <input v-model="course.free" class="form-check-input" type="checkbox" reef="free">
                                        <label class="form-check-label" for="free">
                                            <t>Ofrecer curso gratuito</t>
                                        </label>
                                    </div>
                                    <div class="col-12 col-xl">
                                        <select class="form-select" v-model="course.target" aria-label="Campaña">
                                            <option v-for="target in TARGETS" v-bind:value="target.code">
                                                {{ target.text }} 
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-12 col-xl-8">
                                        <div class="form-floating">
                                            <input v-model="course.title" :class="course.title ? 'is-valid' : ''" @keydown.enter.exact.prevent="$refs.duration.focus()" type="text" autofocus class="form-control" ref="title" placeholder="name@example.com">
                                            <label for="title">
                                                <t>Nombre del curso</t>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-xl">
                                        <div class="form-floating">
                                            <input v-model="course.duration" id="duration" :class="course.duration ? 'is-valid' : ''" @keydown.enter.exact.prevent="$refs.description.focus()" type="text" class="form-control" ref="duration" data-mask="HH:MM" placeholder="name@example.com">
                                            <label for="duration">
                                                <t>Horas del curso</t>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description">Descripción del curso</label>
                                    
                                    <div id="editor" style="height: 130px;"></div>
                                </div>

                                <div>
                                    <label for="tag">
                                        <t>Tags del curso</t>
                                    </label>
                                    <input  name="basic" ref="tag" id="tag" value="" data-tagify="true">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex row justify-content-center mt-3">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                Lecciones del curso
                                            </div>
                                            <div class="col-auto">
                                                {{ course.sessions.length }}
                                            </div>
                                        </div>
                                    </div>
                                    <ul class="list-group list-group-flush">
                                        <li v-for="(session, index) in course.sessions" class="list-group-item">
                                            <div class="row align-items-center">
                                                <div class="col-auto text-gradient-primary">
                                                    <span v-if="session.catalog_multimedia_id == 1">
                                                        <i class="bi fs-3 text-primary text-gradient bi-body-text"></i>
                                                    </span>
                                                    <span v-else-if="session.catalog_multimedia_id == 2">
                                                        <i class="bi fs-3 text-primary text-gradient bi-camera-video"></i>
                                                    </span>
                                                    <span v-else-if="session.catalog_multimedia_id == 3">
                                                        <i class="bi fs-3 text-primary text-gradient bi-collection-play"></i>
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="fw-semibold">{{session.title}}</div>
                                                    
                                                    <div v-if="session.catalog_multimedia_id == 1">
                                                        <div class="text-secondary">{{session.course}}</div>
                                                    </div>
                                                    <div v-else-if="session.catalog_multimedia_id == 2">
                                                        <div class="row">
                                                            <div class="col-12 col-lg-4">
                                                                <img :src="session.course.youtubeThumbnail()" class="img-fluid">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="d-grid">
                                                        <button class="btn btn-dark mb-1" @click="deleteSession(session.unique_id)">Borrar</button>
                                                    </div>
                                                    <div class="d-grid">
                                                        <button class="btn btn-dark" @click="$emit('selectSession',session)">Editar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-xl-4">
                        <div :style="{backgroundImage: course.image ? 'url(' +course.image+')' : ''}" class="card mb-3 cursor-pointer" style="height:300px;background-size:cover;">
                            <div class="card-body">
                                <div @click="openFileManager($refs.uploadCoverCourse)" class="row cover h-100 d-flex align-items-center text-center">
                                    <div class="col-12">
                                        <div v-if="!course.image">
                                            <div class="lead text-xdark">
                                                <t>Imagen de portada</t>
                                            </div>

                                            <div class="text-muted info small">
                                                <t>Arrastra tu archivo aquí. Mínimo 500 pixeles de ancho por 500 pixeles de alto</t>
                                            </div>
                                            <span class="badge bg-secondary">JPG</span>
                                            <span class="badge bg-secondary">PNG</span>
                                            <span class="badge bg-secondary">JPEG</span>
                                        </div>
                                        <div v-else>
                                            Cambiar foto deportada
                                        </div>
                                    </div>
                                </div>

                                <input class="upload-file d-none" ref="uploadCoverCourse" @change="uploadCoverCourse($refs.uploadCoverCourse).then((res)=>{ updateCourseCover(res)})" capture="filesystem" type="file" accept=".jpg, .png, jpeg" />
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="form-floating mb-3">
                                    <select :class="course.catalog_course_id ? 'is-valid' : ''" class="form-select" ref="catalog_course_id" v-model="course.catalog_course_id" aria-label="Selecciona el tipo de proyecto">
                                        <option v-for="catalog_course in catalog_courses" v-bind:value="catalog_course.catalog_course_id">
                                            {{ catalog_course.name }}
                                        </option>
                                    </select>
                                    <label for="catalog_proyect_id">
                                        <t>Categoría del curso</t>
                                    </label>
                                </div>

                                <div v-if="!course.free" class="row">
                                    <div class="col-12 col-xl">
                                        <div class="row">
                                            <div class="col-12 col-xl-6">
                                                <div class="form-floating">
                                                    <input v-model="course.price" :class="course.price ? 'is-valid' : ''" @keydown.enter.exact.prevent="$refs.catalog_currency_id.focus()" type="text" class="form-control" ref="price" id="price" placeholder="">

                                                    <label for="price">
                                                        <t>Precio</t>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-12 col-xl-6">
                                                <div class="form-floating">
                                                    <select :class="course.catalog_currency_id ? 'is-valid' : ''" class="form-select" ref="catalog_currency_id" v-model="course.catalog_currency_id" aria-label="Selecciona el tipo de moneda">
                                                        <option v-for="catalog_currency in catalog_currencies" v-bind:value="catalog_currency.country_id">
                                                            {{ catalog_currency.currency }}
                                                        </option>
                                                    </select>
                                                    <label for="catalog_currency_id">
                                                        <t>Moneda</t>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
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

export { AddcourseViewer } 