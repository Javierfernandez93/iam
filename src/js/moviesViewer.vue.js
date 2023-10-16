import { User } from '../../src/js/user.module.js?v=2.6.6'   

const MoviesViewer = {
    name : 'movies-viewer',
    props : ['title','filter'],
    data() {
        return {
            User: new User,
            query: null,
            moviesAux: null,
            movies: null
        }
    },
    watch : {
        query : {
            handler() {
                this.filterData()
            },
            deep: true
        }
    },
    methods: {
        filterData: function () {
            this.movies = this.moviesAux

            this.movies = this.movies.filter((movie) => {
                return movie.name.toLowerCase().includes(this.query.toLowerCase()) 
            })
        },
        getMovies(filter) {
            return new Promise((resolve,reject) => {
                this.User.getMovies({filter:filter}, (response) => {
                    if (response.s == 1) {
                        resolve(response.movies)
                    }

                    reject()
                })
            })
        },
        viewMovie(movie)
        {
            window.location.href = `../../apps/movies/watch?mid=${movie.movie_id}`
        },
        _getMovies()
        {
            let filter = null

            if(this.filter)
            {
                filter = JSON.parse(this.filter)
            }

            this.getMovies(filter).then((movies) => {
                this.moviesAux = movies
                this.movies = this.moviesAux
            }).catch(() => this.movies = false)
        }
    },
    mounted() {
        this._getMovies()
        
    },
    template : `
        <div v-if="movies" class="mb-5">
            <div class="mb-3">
                <h3 class="text-white">
                    {{title}}
                </h3>
                <div>Total {{movies.length}} peliculas</div>
            </div>
            <div class="horizontal-scrollable">
                <div class="row align-items-center">
                    <div v-for="movie in movies" class="col-12 col-md-4 col-xl-3">
                        <div @click="viewMovie(movie)" class="card card-movie-outside cursor-pointer f-zoom-element-sm bg-transparent" 
                            :style="{'background-image':'url('+movie.image+')'}">
                            <div class="card-body">
                                <div class="row card-movie align-content-end">
                                    <div class="col-12 text-truncate">
                                        <h4 class="text-white">{{movie.title}}</h4>
                                        <h5 class="text-light">({{movie.year}})</h5>
                                        <div>
                                        <span v-for="gender in movie.genders" class="badge bg-secondary me-2">
                                            {{gender.gender}}
                                        </span>
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

export { MoviesViewer } 