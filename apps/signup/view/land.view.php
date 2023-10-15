<div class="hero-1 bg-white h-100" id="home">
    <div class="bg-overlay overflow-hidden bg-transparent">
        <div class="hero-1-bg"></div>
    </div>

    <div class="row d-flex justify-content-center text-center">
        <div class="col-12 col-xl-10">
            <div class="display-2 text-primary">
               <t>Únete ahora a</t> <n class="text-pink">CodDex.com</n>
            </div>

            <div class="display-1 text-dark mb-5 bold" id="name">
               
            </div>

            <div class="row d-flex justify-content-center">
	            <div class="col-12 col-xl-5">
		            <div class="card">
			            <div class="card-header">
				            <div class="card-header-title h1 bold py-4 mb-0">
				            	<t>Ingresa tus datos para comenzar</t>
				            </div>
			            </div>
		            	<div class="card-body px-5">
		            		<div class="input-group mb-4">
							  <span class="input-group-text text-primary" id="basic-addon1"><i class="far fa-user"></i></span>
							  <input autofocus="true" type="text" id="names" onkeyup="setName(this,event,'#last_name')" class="form-control" placeholder="Nombre(s)" aria-label="Nombre(s)" aria-describedby="basic-addon1">
							  <span class="input-group-text text-primary" id="basic-addon1"><i class="far fa-user"></i></span>
							  <input type="text" id="last_name" onkeyup="setLastName(this,event,'#email')" class="form-control" placeholder="Apellido(s)" aria-label="Apellido(s)" aria-describedby="basic-addon1">
							</div>

							<div class="input-group mb-4">
							  <span class="input-group-text text-primary" id="basic-addon1"><i class="far fa-envelope"></i></span>
							  <input type="email" id="email" onkeyup="checkEmail(this,event,'#password')" class="form-control" placeholder="Correo electrónico" aria-label="Correo electrónico" aria-describedby="basic-addon1">
							</div>

							<div class="input-group mb-5">
							  <span class="input-group-text text-primary" id="basic-addon1"><i class="fas fa-unlock-alt"></i></span>
							  <input type="password" id="password" onkeyup="setPassword(this,event,'#re_password')" class="form-control" placeholder="Contraseña" aria-label="Contraseña" aria-describedby="basic-addon1">
							  <span class="input-group-text text-primary" id="basic-addon1"><i class="fas fa-unlock-alt"></i></span>
							  <input type="password" id="re_password" onkeyup="setRePassword(this,event,'#sign')" class="form-control" placeholder="Repita contraseña" aria-label="Repita contraseña" aria-describedby="basic-addon1">
							</div>

							<div class="form-check text-left">
							  <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
							  <label class="form-check-label" for="defaultCheck1">
							    Acepto los <a href="">términos de uso</a> y de <a href="">terminos de servicio</a>.
							  </label>
							</div>
		            	</div>
		            	<div class="card-footer row">
		            		<div class="col-12 col-xl-6">
			            		<button class="btn btn-primary btn-block">
				            		<div>Ingresar Facebook <i class="fab fa-facebook-f mt-1"></i></div>
				            		
				            	</button>
			            	</div>
		            		<div class="col-12 col-xl-6">
			            		<button class="btn btn-danger btn-block">
				            		<div>Ingresar Google <i class="fab fa-google"></i></div>
				            	</button>
			            	</div>
		            	</div>
		            	<div class="card-footer">
		            		<button id="sign" class="btn btn-secondary btn-block btn-lg" disabled="true">Unirme a CodDex.com <i class="fas fa-arrow-right"></i></button>
		            	</div>
		            </div>
		            <div>¿Ya tienes una cuenta?</div>
		            <div><a href="../../apps/login">Ingresa aquí a tu cuenta dando clic aquí</a></div>
	            </div>
            </div>
        </div>
    </div>
</div>