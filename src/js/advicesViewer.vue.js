import { User } from '../../src/js/user.module.js?v=2.6.6'   

const AdvicesViewer = {
    name : 'advices-viewer',
    data() {
        return {
            User: new User,
            query : null,
            buys : null,
            buysAux : null,
            STATUS : {
                PENDING: 1,
                DELETED: -1,
                VERIFIED: 2,
            }
        }
    },
    methods: {
        getLastOrders() {    
            this.User.getLastOrders({},(response)=>{
                if(response.s == 1)
                {
                    this.buys = response.buys
                }
            })
        },
    },
    mounted() 
    {   
    },
    template : `
        <div class="card animation-fall-down p-2 bg-white shadow-lg" style="--delay:300ms">
            <div class="overflow-hidden position-relative border-radius-lg bg-cover h-100" style="background-image:url(../../src/img/bg.jpg)">
                <div class="mask bg-gradient-dark"></div>

                <div class="card-body position-relative z-index-1 h-100 text-white">
                    <div style="line-height:3rem" class="fs-1 fw-semibold">Aceite de Excelencia</div>
                    <div class="fs-4 py-3">Nutre Tu Piel con Nuestro Aceite Natural</div>
                    <a href="../../apps/store/package" class="btn btn-outline-light">¿Ya lo probaste?</a>
                </div>
            </div>
        </div>
    `,
}

export { AdvicesViewer } 