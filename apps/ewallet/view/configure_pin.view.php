<div class="header">
  <div class="header-body">
    <div class="row align-items-end">
      <div class="col">
        <h6 class="header-pretitle">
          BILLETERA
        </h6>

        <h1 class="text-xdark header-title">
          Configurar código Pin
        </h1>
      </div>
    </div> 
  </div> 
</div>

<div class="col-12">
  <div class="row">
     <!-- Area Chart -->
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header">
          <h4 class="card-header-title text-xdark">Ingresa un pin de 4 dígitos para configurarlo</h4>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-lg-4 mx-auto">
            	<div class="input-group mb-3">
              	<?php for ($i=0; $i < EWallet\Wallet::$PIN_LENGTH; $i++) { ?>
				          <input type="number" min="0" onkeyup="checkPinLenght(this)" style="font-size: 33px;" maxlength="1" class="font-bold form-control text-center pin-form" placeholder="" aria-label="" aria-describedby="basic-addon1">
              	<?php } ?>
							</div>
            </div>
          </div>
        </div>
        <div class="card-footer text-center">
          <button class="btn btn-primary" disabled="true" id="save-pin-configuration" onclick="savePinConfigurated(this)"><t>Guardar Pin</t></button>
        </div>
      </div>
    </div>

    
  </div>
</div>