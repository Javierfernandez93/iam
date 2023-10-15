<div class="row justify-content-center">
    <div class="col-11 col-md-6 col-xl-4">
        <div class="card">
            <img src="{{background}}" class="card-img-top">
            <div class="row justify-content-center mt-n6">
                <div class="col-auto">
                    <div class="avatar avatar-xxl">
                        <img src="{{image}}" style="object-fit:cover" class="img-fluid avatar avatar-xxl rounded-circle shadow-lg p-2">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-center text-secondary fs-3">{{name}}</div>
                    <div class="text-center text-secondary">{{email}}</div>
                    <div class="text-center text-secondary">
                        <a href="mailto:{{email}}" class="fw-semibold"><i class="bi bi-pin-map"></i> {{location}}</a>
                    </div>
                    <div class="text-secondary text-center">
                        {{about_me}}
                    </div>
                </div>

                <vcardsocial-viewer
                    ref="social"></vcardsocial-viewer>
                
                <div class="card mb-3 border-0">
                    <div class="card-body">
                        <div class="mb-3 row align-items-center">
                            <div class="col-12 col-xl-6">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <i class="bi bi-phone text-secondary fs-1"></i>
                                    </div>
                                    <div class="col">
                                        <a href="tel:{{phone}}" class="text-secondary fw-semibold">{{phone}}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-xl-6">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <i class="bi bi-envelope text-secondary fs-1"></i>
                                    </div>
                                    <div class="col text-truncate">
                                        <a href="mailto:{{email}}" class="text-secondary fw-semibold">{{email}}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <vcardshare-viewer  
                    ref="share"
                    :vcard="vcard"></vcardshare-viewer>
                    
                <vcardqr-viewer
                    ref="qr"
                    :vcard="vcard"></vcardqr-viewer>
            </div>
        </div>
    </div>
</div>

<div class="text-center">Diseñado por {{designed_by}}</div>