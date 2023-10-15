import { WelcomeViewer } from '../../src/js/welcomeViewer.vue.js?v=2.6.4'
import { TicketViewer } from '../../src/js/ticketViewer.vue.js?v=2.6.4'
import { AddticketViewer } from '../../src/js/addticketViewer.vue.js?v=2.6.4'
import { FaqViewer } from '../../src/js/faqViewer.vue.js?v=2.6.4'

Vue.createApp({
    components: {
        WelcomeViewer, TicketViewer, FaqViewer, AddticketViewer
    },
    data() {
        return {
            addTicket: false,
            viewFullFaq: false,
        }
    },
    methods: {
        toogleViewFullFaq(state)
        {
            state = state ? state : !this.viewFullFaq

            this.viewFullFaq = state
        },
        toggleMakeTicket()
        {
            this.addTicket = !this.addTicket
        }
    }
}).mount('#app')