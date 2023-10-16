import { User } from '../../src/js/user.module.js?v=2.6.6'   

const StartViewer = {
    name : 'start-viewer',
    emits : ['selectStep'],
    props : ['step'],
    data() {        
        return {
            User: new User
        }
    },
    template : `
        <div class="py-5 text-center">
            <h4>Sigue estos 3 pasos</h4>
            
            <div @click="$emit('selectStep',1)" :class="step == 1 ? 'big-text' :'text-xs'" class="text-header cursor-pointer my-3 align-items-center">
                <span style="font-size:8px !important" class="badge mt-n3 bg-primary">Paso 1</span>
                Conéctate tu broker
            </div>
            <div @click="$emit('selectStep',2)" :class="step == 2 ? 'big-text' :'text-xs'" class="text-header cursor-pointer my-3">
                <span style="font-size:8px !important" class="badge mt-n3 bg-primary">Paso 2</span>
                Conéctate a Telegram
            </div>
            <div @click="$emit('selectStep',3)" :class="step == 3 ? 'big-text' :'text-xs'" class="text-header cursor-pointer my-3">
                <span style="font-size:8px !important" class="badge mt-n3 bg-primary">Paso 3</span>
                Sigue las señales
            </div>
        </div>
    `,
}

export { StartViewer } 