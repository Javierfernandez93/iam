<div class="row justify-content-center">
    <div class="col-11 col-md-6 col-xl-4">
        <div class="card bg-dark"> <img src="{{background}}" class="card-img-top">
            <div class="card-body mb-0 pb-0">
                <div class="card bg-secondary border-0 mb-0 pb-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-auto">
                                <div class="avatar avatar-xxl"> <img src="{{image}}" style="object-fit:cover" class="img-fluid avatar avatar-xxl rounded-circle shadow-lg bg-secondary p-2"> </div>
                            </div>
                            <div class="col-auto">
                                <div>
                                    <div class="text-light fs-2 border-bottom border-secondary pb-3 mb-3">{{name}}</div>
                                    <div class="text-light">{{email}}</div>
                                    <div class="text-light"> <a href="mailto:{{email}}" class="fw-semibold"><i class="bi bi-pin-map"></i> {{location}}</a> </div>
                                    <div class="text-light"> {{about_me}} </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
               
                <vcardsocial-viewer ref="social"></vcardsocial-viewer>
                <div class="card mb-3 bg-secondary border-0">
                    <div class="card-body">
                        <div class="mb-3 row align-items-center">
                            <div class="col-12 col-xl-6">
                                <div class="row align-items-center">
                                    <div class="col-auto"> <i class="bi bi-phone text-light fs-1"></i> </div>
                                    <div class="col"> <a href="tel:{{phone}}" class="text-light fw-semibold">{{phone}}</a> </div>
                                </div>
                            </div>
                            <div class="col-12 col-xl-6">
                                <div class="row align-items-center">
                                    <div class="col-auto"> <i class="bi bi-envelope text-light fs-1"></i> </div>
                                    <div class="col text-truncate"> <a href="mailto:{{email}}" class="text-light fw-semibold">{{email}}</a> </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <vcardshare-viewer ref="share" :vcard="vcard"></vcardshare-viewer>
                <vcardqr-viewer ref="qr" :vcard="vcard"></vcardqr-viewer>
            </div>
        </div>
    </div>
</div>
<div class="text-center">Diseñado por {{designed_by}}</div>