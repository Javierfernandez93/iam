import { User } from '../../src/js/user.module.js?v=2.6.4'   

Vue.createApp({
    components : { 
        
    },
    data() {
        return {
            User : null,
            plans : {},
        }
    },
    watch : {
    },
    methods: {
        getPlans : function() {
            this.User.getPlans({},(response)=>{
                if(response.s == 1)
                {
                    this.plans = response.plans
                }
            })
        },
    },
    mounted() 
    {
        this.User = new User
        
        this.getPlans()
    },
}).mount('#app')