import { Guest } from '../../src/js/guest.module.js?v=2.6.4'   

const VcardqrViewer = {
    name : 'vcardqr-viewer',
    props : ['vcard'],
    emits : [],
    data() {
        return {
            Guest: new Guest,
            route: null,
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
        getVcardQR: function (vcard_per_user_id) {
            return new Promise((resolve,reject) => {
                this.Guest.getVcardQR({vcard_per_user_id:vcard_per_user_id}, (response) => {
                    if (response.s == 1) {
                        resolve(response.route)
                    }

                    reject()
                })
            })
        },
        copyLink: function(link,target) {   
            navigator.clipboard.writeText(link).then(() => {
                target.innerText = 'copiado'
            });
        },
    },
    mounted() {
    },
    template : `
        <div v-if="vcard.route != null" class="card bg-dark" id="card-qr">
            <div class="card-body">
                <div class="row mt-3 text-center align-items-center">
                    <div class="col">
                        <img :src="vcard.route.getQrCodeFromRoute()" class="img-fluid">
                    </div>
                    <div class="col-auto">
                        <a :href="vcard.route.getQrCodeFromRoute()" download="QR" class="btn btn-primary">Descargar mi código QR</a>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { VcardqrViewer } 