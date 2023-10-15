<div class="page-title-box">
    <div class="container-fluid">
        <div class="row align-items-center justify-content-center">
             <div class="col-12 col-xl-9">
                 <div class="row d-flex align-items-center">
                    <div class="col">
                        <div class="page-title">
                            <h4>Administración</h4>
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="../../apps/admin-client">Clientes</a></li>
                                <li class="breadcrumb-item active"><a>Ver cliente</a></li>
                            </ol>
                        </div>
                    </div>
                    <div class="col-auto">
                         <div id="response-pdf">
                            <button class="btn btn-dark rounded-pill px-3"  onclick="getClientPdf(this)">Generar PDF</button>
                         </div>
                     </div>
                 </div>
             </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="page-content-wrapper">
        <div class="row d-flex justify-content-center">
            <div class="col-12 col-xl-9">
                <div id="response"></div>
            </div>
        </div>
    </div>
</div>