<div class="container-fluid py-4" id="app">
    <div class="container">
    
        <div v-if="cart.state" class="row align-items-center pb-5">
            <div class="col">
                <div class="h3 text-dark mb-0">{{cart.state.text}}</div>
            </div>
            <div class="col-auto">
                <button 
                    v-if="cart.state == STATES.CHOICE_ITEMS"
                    @click="nextStep"
                    :disabled="!cart.hasItems"
                    class="btn btn-dark shadow-none mb-0">Elegir método de pago</button>
                <button 
                    v-if="cart.state == STATES.CHOICE_PAYMENT_METHOD"
                    @click="nextStep"
                    :disabled="!cart.catalog_payment_method_id"
                    class="btn btn-dark shadow-none mb-0">Continuar con pago</button>
            </div>
        </div>
        <div v-if="cart.state">
            <storecredit-viewer
                v-if="cart.state == STATES.CHOICE_ITEMS"
                :cart="cart"
                @nextstep="nextStep"
                ></storecredit-viewer>
            <storepaymentmethods-viewer
                v-if="cart.state == STATES.CHOICE_PAYMENT_METHOD"
                :cart="cart"
                @nextstep="nextStep"
                ></storepaymentmethods-viewer>
            <storecheckout-viewer
                v-if="cart.state == STATES.CHECKOUT"
                @nextstep="nextStep"
                :cart="cart"></storecheckout-viewer>
            <storeinvoice-viewer
                v-if="cart.state == STATES.INVOICE"
                :cart="cart"></storeinvoice-viewer>
        </div>
    </div>
</div>