import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.6'

const AdminacademyViewer = {
    name : 'adminacademy-viewer',
    data() {
        return {
            UserSupport : new UserSupport,
            query : null,
            courses : null,
            coursesAux: null,
            grid : false
        }
    },
    watch : {
        query: {
            handler()
            {
                this.filterData()
            },
            deep: true
        },
    },
    methods: {
        filterData() {
            this.courses = this.coursesAux

            this.courses = this.courses.filter((course) => {
                return course.title.toLowerCase().includes(this.query.toLowerCase()) || course.price.toLowerCase().includes(this.query.toLowerCase())
            })
        },
        toggleGrid()
        {
            this.grid = !this.grid
        },
        toggleGrid()
        {
            this.grid = !this.grid
        },
        goToPreview(course_id)
        {
            window.open(`../../apps/academy/lesson.php?cid=${course_id}`)
        },
        edit(course_id)
        {
            window.location.href = `../../apps/admin-academy/edit.php?cid=${course_id}`
        },
        toggleEditingRoute(sheet)
        {
            sheet.editingRoute = !sheet.editingRoute
        },
        goToSheet(proyect_id)
        {
            window.location.href = `../../apps/proyects/sheets?pid=${proyect_id}`
        },
        goToVConfigureCard(sheet_per_proyect_id)
        {
            window.location.href = `../../apps/v-card/config?sppid=${sheet_per_proyect_id}`
        },
        unpublish(course)
        {
            this.UserSupport.changeCourseStatus({course_id:course.course_id,status:0},(response)=>{
                if(response.s == 1)
                {
                    course.status = response.status
                }
            })
        },
        publish(course)
        {
            this.UserSupport.changeCourseStatus({course_id:course.course_id,status:1},(response)=>{
                if(response.s == 1)
                {
                    course.status = response.status
                }
            })
        },
        delete(course)
        {
            this.UserSupport.changeCourseStatus({course_id:course.course_id,status:-1},(response)=>{
                if(response.s == 1)
                {
                    course.status = response.status
                }
            })
        },
        getCourses() 
        {
            this.UserSupport.getCourses({},(response)=>{
                this.coursesAux = response.courses
                this.courses = this.coursesAux
            })
        }
    },
    mounted() 
    {   
        this.getCourses()
    },
    template : `

        <div class="row mb-3">
            <div class="col-12 col-xl">
                <input :autofocus="true" v-model="query" type="text" class="form-control border-0 shadow-lg" placeholder="Buscar curso por nombre o precio...">
            </div>

            <div class="col-12 col-xl-auto">
                <button
                    @click="toggleGrid"
                    class="btn btn-dark">
                    <span v-if="grid">
                        <i class="bi bi-list"></i>
                    </span>
                    <span v-else>
                        <i class="bi bi-grid-fill"></i>
                    </span>
                </button>
                <a
                    href="../../apps/admin-academy/add"
                    class="btn btn-dark">
                    añadir
                </a>
            </div>
        </div>

        <div class="row">
            <div    
                :class="grid ? 'col-6' : 'col-12'"
                v-for="course in courses"> 
                
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-12 col-xl">
                                <div>
                                    <span v-if="course.status == 0" class="badge bg-secondary me-2">
                                        Despublicado
                                    </span>
                                    <span v-else-if="course.status == 1" class="badge bg-success me-2"> 
                                        Publicado
                                    </span>

                                    <span class="badge bg-secondary me-2">Creado hace {{course.create_date.timeSince()}}</span>
                                
                                    <span v-if="course.price > 0" class="badge bg-gradient-success me-2">
                                        {{ course.currency }} $ {{ course.price.numberFormat(2) }}
                                    </span>
                                    <span v-else class="badge bg-primary me-2">
                                        <t>Gratis</t>
                                    </span>


                                    <span class="badge bg-warning">
                                        {{course.type}}
                                    </span>
                                </div>
                                <div
                                    class="h3"
                                    @click="goToSheet(proyect.proyect_id)">
                                    <n class="font-bold text-dark fw-semibold">
                                        {{course.title}} 
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="row">
                                    <div class="col">
                                        <i class="bi bi-hand-thumbs-up"></i>
                                        <div class="h4">{{course.like}}</div>
                                    </div>

                                    <div class="col">
                                        <i class="bi bi-chat"></i>
                                        <div class="h4">{{course.comment}}</div>
                                    </div>
                                    
                                    <div class="col">
                                        <i class="bi bi-person"></i>
                                        <div class="h4">{{course.user}}</div>
                                    </div>

                                    <div class="col">
                                        <i class="bi bi-eye"></i>
                                        <div class="h4">{{course.visit}}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="dropdown">
                                    <button type="button" class="btn btn-outline-primary px-3 btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">

                                    </button>
                                    <ul class="dropdown-menu shadow">
                                        <li><button class="dropdown-item"@click="goToPreview(course.course_id)"><t>Ver previo</t></button></li>
                                        <li><button class="dropdown-item"@click="edit(course.course_id)"><t>Editar</t></button></li>
                                        <li v-if="course.status == 1"><button class="dropdown-item"@click="unpublish(course)"><t>Despublicar</t></button></li>
                                        <li v-if="course.status == 0"><button class="dropdown-item"@click="publish(course)"><t>Publicar</t></button></li>
                                        <li><button class="dropdown-item"@click="delete(course)"><t>Eliminar</t></button></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { AdminacademyViewer } 