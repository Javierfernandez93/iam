<div class="container-fluid py-4" id="app">
    <div class="row justify-content-center">
        <div class="col-12">
            <div v-if="cart.state" class="row align-items-center pb-3">
                <div class="col-auto">
                    <span class="badge bg-primary">{{cart.state.text.getAcronime()}}</span>
                </div>
                <div class="col">
                    <div class="h3 text-dark mb-0">{{cart.state.text}}</div>
                </div>
                <div class="col-auto">
                    <button 
                        v-if="cart.state == STATES.CHOICE_ITEMS"
                        @click="nextStep"
                        :disabled="!cart.package_id"
                        class="btn btn-dark mb-0">Elegir método de pago</button>
                    <button 
                        v-if="cart.state == STATES.CHOICE_PAYMENT_METHOD"
                        @click="nextStep"
                        :disabled="!cart.catalog_payment_method_id"
                        class="btn btn-dark mb-0">Continuar con pago</button>
                </div>
            </div>
            <div v-if="cart.state">
                <storeitems-viewer
                    v-if="cart.state == STATES.CHOICE_ITEMS"
                    :cart="cart"></storeitems-viewer>
                <storepaymentmethods-viewer
                    v-if="cart.state == STATES.CHOICE_PAYMENT_METHOD"
                    :cart="cart"></storepaymentmethods-viewer>
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
</div>