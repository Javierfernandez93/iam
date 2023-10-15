<div class="container" id="app">
    <welcome-viewer></welcome-viewer>
    <div class="card card-body">
        <div class="row justify-content-center">
            <div class="col-12"
                :class="viewFullFaq ? ' col-xl-8' : ''">
                <div class="row">
                    <div class="col-12"
                        :class="viewFullFaq ? '' : 'col-xl-4'">
                        <faq-viewer
                            @togglemaketicket="toggleMakeTicket"
                            @toogleviewfullfaq="toogleViewFullFaq"></faq-viewer>
                    </div>
                    <div class="col-12 col-xl-8"
                        :class="viewFullFaq ? 'd-none' : ''">
                        <ticket-viewer 
                            v-if="!addTicket"
                            @togglemaketicket="toggleMakeTicket"
                            >
                        </ticket-viewer>
                        <addticket-viewer
                            v-else
                            @togglemaketicket="toggleMakeTicket"></addticket-viewer>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>