import { User } from '../../src/js/user.module.js?v=2.6.4'   

const AdviceViewer = {
    name : 'advice-viewer',
    props : [],
    emits : [],
    data() {
        return {
            User : new User,
            advice : null,
            advices : null,
            TIME : 5000,
            invertval : null,
            ADVICES_TYPES: {
                SIGNUP: 1,
                ACTIVATION: 2,
                TRANSACTIONS: 3
            }
        }
    },
    methods: {
        getAdvice : function() {
            let advice = false

            this.advices.map((_advice) => {
                if(_advice.showed == false)
                {
                    advice = _advice
                }
            })

            return advice
        },
        setLastAdviceAsShowed : function() {
            if(this.advice)
            {
                this.advice.showed = true
            }
        },
        showAdvice : function() {
            this.setLastAdviceAsShowed()
            this.advice = this.getAdvice()

            if(this.advice)
            {
                $(this.$refs.toast).toast('show')
            } else {
                $(this.$refs.toast).toast('hide');
                clearInterval(this.invertval)
            }
        },
        showAdvices : function() {
            this.showAdvice() 

            this.invertval = setInterval(() => {
                this.showAdvice()
            }, this.TIME) 
        },
        stopFlashNews : function() {
            clearInterval(this.invertval)
        },
        getAdvices : function() {
            return new Promise((resolve) => {
                this.User.getAdvices({},(response)=>{
                    if(response.s == 1)
                    {
                        resolve(response.advices)
                    }
                })
            })
        }
    },
    updated() {
    },
    mounted() 
    {   
        this.getAdvices().then((advices)=>{
            this.advices = advices

            this.showAdvices()
        })
    },
    template : `
        <div v-show="advice" class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div id="liveToast" ref="toast" class="toast hide shadow-xl bg-gradient-dark text-white" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-transparent text-white px-3">
                    <strong class="me-auto"><i class="bi bi-globe2"></i> Funnel7 flash news</strong>
                    <button @click="stopFlashNews" type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div v-if="advice" class="toast-body">
                    <div v-if="advice.advice_type == ADVICES_TYPES.SIGNUP">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <img :src="advice.country_id.getCoutryImage()" class="img-fluid">
                            </div>
                            <div class="col">
                                ¡Nuevo socio DummieTrading <strong>{{advice.names}}</strong> desde <strong>{{advice.country}}</strong> <i class="bi bi-emoji-wink-fill"></i>!
                            </div>
                        </div>
                    </div>
                    <div v-else-if="advice.advice_type == ADVICES_TYPES.ACTIVATION">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                
                            </div>
                            <div class="col">
                                ¡Nueva compra <strong>{{advice.formated_items}}</strong> por <strong>{{advice.names}}</strong> <i class="bi bi-emoji-sunglasses"></i>!
                            </div>
                        </div>
                    </div>
                    <div v-else-if="advice.advice_type == ADVICES_TYPES.TRANSACTIONS">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                
                            </div>
                            <div class="col">
                                ¡Se han realizado <strong>{{advice.transactions}}</strong> transacciones <strong>por ewallet</strong> <i class="bi bi-emoji-heart-eyes"></i>!
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { AdviceViewer } 