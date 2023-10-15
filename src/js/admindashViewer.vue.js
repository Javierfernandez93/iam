import { User } from '../../src/js/user.module.js?t=1.1.4'   

const AdmindashViewer = {
    name : 'admindash-viewer',
    data() {
        return {
            User: new User,
        }
    },
    methods: {
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
        this.getChatIaFirstMessage()
    },
    template : `
        
    `,
}

export { AdmindashViewer } 