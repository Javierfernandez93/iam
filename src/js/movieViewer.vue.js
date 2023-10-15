import { User } from '../../src/js/user.module.js?v=2.6.4'   

const MovieViewer = {
    name : 'movie-viewer',
    props : ['title'],
    data() {
        return {
            User: new User,
            movie: null
        }
    },
    methods: {
        getMovie(movie_id) {
            return new Promise((resolve,reject) => {
                this.User.getMovie({movie_id:movie_id}, (response) => {
                    if (response.s == 1) {
                        resolve(response.movie)
                    }

                    reject()
                })
            })
        },
        addViewPerMovie(movie_id) {
            return new Promise((resolve,reject) => {
                this.User.addViewPerMovie({movie_id:movie_id}, (response) => {
                    if (response.s == 1) {
                        resolve(response.movie)
                    }

                    reject()
                })
            })
        },
        viewMovie(movie)
        {
            window.location.href = `../../apps/movies/watch?mid=${movie.movie_id}`
        },
    },
    mounted() {
        this.getMovie(getParam("mid")).then((movie) => {
            this.addViewPerMovie(getParam("mid")).then(() => {
                this.movie = movie
            })
        }).catch(() => this.movie = false)
    },
    template : `
        <div v-if="movie">
            <div class="mb-3">
                <h3 class="text-white">
                    {{movie.title}} {{movie.year}}
                </h3>
            </div>
            <iframe :src="movie.link" width="100%" height="980" allow="autoplay"></iframe>
        </div>
    `,
}

export { MovieViewer } 