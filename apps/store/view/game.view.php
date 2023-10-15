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
            <storeitems-viewer
                v-if="cart.state == STATES.CHOICE_ITEMS"
                :cart="cart"
                @nextstep="nextStep"
                ></storeitems-viewer>
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
            <div
                v-if="cart.state == STATES.NOT_ACTIVE"
                >
                <div class="alert alert-light text-center">
                    <strong>Aviso importante</strong>
                    <div>No estás activo como franquicia, para poder comprar licencias extras debes de estar activo primero. Por favor comunícate con tu patrocinador para que pueda darte los medios de pago correspondientes y pueda activar tu cuenta.</div>
                </div>
            </div>
        </div>
    </div>
</div>