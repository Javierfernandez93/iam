import { UserSupport } from '../../src/js/userSupport.module.js?v=2.6.6'

/* vue */

Vue.createApp({
    components: {
    },
    data() {
        return {
            UserSupport: null,
            user: {
                user_login_id: null,
                signup_date: 0,
                email: null,
                image: null,
                names: null,
                phone: null,
                country_id: 159, // México country is loaded by default 
                ammount: 0,
                originalAmmount: 0,
                catalog_plan_id: 0
            },
            selectedPlan: {
                name: 0
            },
            runComission : {
                start_date: 0,
                end_date: 0,
            },
            deposit: {
                ammount: null,
                originalAmmount: null,
            },
            responses: [],
            plans: {},
        }
    },
    watch: {
        deposit: {
            handler() {
                this.user.ammount = this.user.originalAmmount

                if(this.deposit.ammount)
                {
                    this.user.ammount += parseFloat(this.deposit.ammount)

                    this.getPlan(this.user.ammount)
                }
            },
            deep: true
        },
        user: {
            handler() {
                this.getPlan(this.user.ammount)
            },
            deep: true
        }
    },
    methods: {
        zerofill: function(i) {
            return (i < 10 ? '0' : '') + i;
        },
        runOldComissions: function (user_login_id) {
            this.responses = []

            const date1 = new Date(this.runComission.start_date);
            const date2 = new Date(this.runComission.end_date);
            
            const diffTime = date2.getTime() - date1.getTime();
            const diffDays = diffTime / (1000 * 3600 * 24)

            for(let i = 0; i <= diffDays; i++)
            {
                var date = new Date(this.runComission.start_date);
                date.setDate(date.getDate() + (i+1));

                const year = date.getFullYear();
                const month = this.zerofill(date.getMonth()+1);
                const day = this.zerofill(date.getDate());
            
                const _day = `${year}/${month}/${day} 09:00:00`

                this.UserSupport.disperseGains({day:_day,user_login_id:user_login_id},(response) => {
                    this.responses.push({
                        r: response.r,
                        s: response.s,
                        day: response.day,
                        time: response.total_execution_time
                    })
                })
            }
        },
        addDeposit: function () {
            const ammount = this.user.ammount - this.user.originalAmmount

            iam.com.mx = alertCtrl.create({
                title: "Aviso",
                subTitle: "¿Estás seguro de añadir éste fondeo?. Se recalculará el plan del usuario",
                buttons: [
                    {
                        text: "Sí, añadir depósito",
                        role: "cancel",
                        class: 'btn-success',
                        handler: (data) => {
                            this.UserSupport.addDeposit({ user_login_id: this.user.user_login_id, catalog_plan_id: this.selectedPlan.catalog_plan_id, total_ammount: this.user.ammount, ammount: ammount }, (response) => {
                                if(response.s == 1)
                                {
                                    this.$refs.buttonAddDeposit.innerText = 'Depósito realizado'

                                    this.getUserProfile(getParam('ulid'))
                                }
                            })
                        },
                    },
                    {
                        text: "Cancelar",
                        role: "cancel",
                        handler: (data) => {
                        },
                    },
                ],
            })

            alertCtrl.present(alert.modal); 
        },
        getPlan: function (ammount) {
            this.selectedPlan = {
                name: 0
            }
            
            if(ammount >= parseFloat(this.plans[0].name))
            {
                for (let i = 0; i < this.plans.length; i++) {
                    const nextVal = this.plans[i + 1] != undefined ? parseFloat(this.plans[i + 1].name) : Infinity
    
                    if (ammount >= parseFloat(this.plans[i].name) && ammount < nextVal) {
                        this.selectedPlan = this.plans[i]
                    }
                }
            } else {
                this.selectedPlan = this.plans[0]
            }
        },
        viewDeposits: function (company_id) {
            window.location.href = '../../apps/admin-users/deposits?ulid=' + company_id
        },
        updatePlan: function () {

            let message = this.user.originalAmmount != this.user.ammount ? '. <br><b>Aviso importante: Monto invertido ha sido modificado por lo cual se actualizará la diferencia como un fondeo</br>' : ''
            iam.com.mx = alertCtrl.create({
                title: "Aviso",
                html: "¿Estás seguro de guardar/actualizar ésta activación?"+ message,
                buttons: [
                    {
                        text: "Sí, actualizar plan",
                        role: "cancel",
                        class: 'btn-success',
                        handler: (data) => {
                            this.UserSupport.updatePlan({ user_login_id: this.user.user_login_id, catalog_plan_id: this.selectedPlan.catalog_plan_id, additional_profit: this.user.additional_profit, originalAmmount: this.user.originalAmmount ,ammount: this.user.ammount, sponsor_profit: this.user.sponsor_profit }, (response) => {
                                if (response.s == 1) {
                                    this.$refs.buttonPlan.innerText = 'Actualizado con éxito'
                                } else if(response.r == 'PROFIT_EXCEDS_MAX_LIMIT') {
                                    alertMessage(`La suma de los profits excede el máximo que se puede otorgar (${response.MAX_PROFIT} %). Ingresa porcentajes menores al máximo.`)
                                }
                            })
                        },
                    },
                    {
                        text: "Cancelar",
                        role: "cancel",
                        handler: (data) => {
                        },
                    },
                ],
            })

            alertCtrl.present(alert.modal); 
            
        },
        getUserProfile: function (user_login_id) {
            this.UserSupport.getUserProfile({ user_login_id: user_login_id }, (response) => {
                if (response.s == 1) {
                    response.user.user_plan = {
                        catalog_plan_id: response.user.catalog_plan_id
                    }

                    Object.assign(this.user, response.user)

                    this.user.originalAmmount = this.user.ammount
                }
            })
        },
        getPlans: function () {
            return new Promise((resolve) => {
                this.UserSupport.getPlans({}, (response) => {
                    if (response.s == 1) {
                        this.plans = response.plans
                    }

                    resolve()
                })
            })
        },
    },
    mounted() {
        this.UserSupport = new UserSupport

        if (getParam('ulid')) {
            this.getPlans().then(() => {
                this.getUserProfile(getParam('ulid'))
            })
        }
    },
}).mount('#app')