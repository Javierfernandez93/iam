import { AdviceViewer } from '../../src/js/adviceViewer.vue.js?v=2.6.6'

const AboutViewer = {
    name : 'about-viewer',
    methods : {
        initScroll() {

            window.addEventListener("scroll", function(event) {
                if(this.scrollY > 100)
                {
                    $("#navbar").addClass("bg-gradient-dark shadow-xl")

                } else {
                    $("#navbar").removeClass("bg-gradient-dark shadow-xl")
                }
            })
        }
    },
    mounted() {
        this.initScroll()
    },
    template : `
        <div id="section-1" class="overflow-hidden bg-dark d-flex align-items-end pb-5">
            <div class="container text-center z-index-1">
                <h3 class="text-white">Sobre nosotros</h3>
            </div>
        </div>

        <div id="section-2" class="bg-light py-5">
            <div class="container">
                <h3 class="text-center">Nuestra historia</h3>
                <div class="row py-5">
                    <div class="col-12 col-xl-4">
                        <h3>Noviembre 2022</h3>
                        <p>Comenzamos a diseñar un sistema de trading para hacer realidad los sueños de los traders</p>
                    </div>
                    <div class="col-12 col-xl-4">
                        <h3>Marzo 2023</h3>
                        <p>Se inician los trabajos en el desarrollo de DummieTrading</p>
                    </div>
                    <div class="col-12 col-xl-4">
                        <h3>Abril 2023</h3>
                        <p>Lanzamos la única plataforma en latinoamérica para hacer Trading y ganar dinero real</p>
                    </div>
                </div>
            </div>
        </div>
    `
}

export { AboutViewer }