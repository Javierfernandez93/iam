import { User } from '../../src/js/user.module.js?v=2.6.5'   

const WidgethewalletViewer = {
    name : 'widgethewallet-viewer',
    data() {
        return {
            User: new User,
            profile : null,
            balance : {
                amount: 0,
                licences: 0,
                users: 0,
                credits: 0,
            }
        }
    },
    methods: {
        getProfitStats() {
            this.User.getProfitStats({},(response)=>{
                if(response.s == 1)
                {
                    this.balance = {...response.balance}
                }
            })
        },
        getProfileShort() {
            return new Promise((resolve,reject) => {
                this.User.getProfileShort({},(response)=>{
                    if(response.s == 1)
                    {
                        resolve(response.profile)
                    }

                    reject()
                })
            })
        },
    },
    mounted() 
    {   
        this.getProfileShort().then((profile)=>{
            this.profile = profile
        })
        this.getProfitStats()
    },
    template : `
        <div v-if="profile" class="card bg-gradient-primary animation-fall-down" style="--delay:500ms">
            <div class="card-body">
                <div class="row align-items-center mb-3">
                    <div class="col-12 col-xl-auto">
                        <div class="avatar avatar">
                            <img :src="profile.image" title="user-picture" alt="user" class="avatar avatar rounded-circle"/>
                        </div>
                    </div>
                    <div class="col-12 col-xl">
                        <span class="fw-semibold text-white">
                            {{profile.names}}
                        </span>
                    </div>
                    <div class="col-12 col-xl-auto">
                        <a href="../../apps/backoffice/profile" class="btn btn-sm px-3 shadow-none mb-0 btn-outline-light"><i class="bi fs-6 bi-toggle2-on"></i></a>
                    </div>
                </div>
                <div class="border-bottom mb-3 pb-3">
                    <div><span class="text-light">Balance total</span></div>
                    <div class="align-items-center">
                        <span class="h3 text-white">USD $ {{ balance.amount.numberFormat(2) }}</span>
                        <span class="badge fw-sembold text-success">+0%</span>
                    </div>
                </div>
                <div class="d-grid">
                    <a href="../../apps/ewallet/" class="btn btn-light shadow-none mb-0 btn-lg">
                        Retirar dinero
                    </a>
                </div>
            </div>
        </div>
    `,
}

export { WidgethewalletViewer } 