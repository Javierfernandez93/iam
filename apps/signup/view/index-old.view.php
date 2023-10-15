<div class="row">	
	<div class="col-12 py-5">	
		<div class="row first_step steps_img">	
			<div class="col-12 col-md-4 offset-md-4 box px-4 py-3">
				<div class="row box-vertical-center">
					<div class="col-12 px-3 py-3">
						<div class="row pb-2 mb-3 text-center border-bottom">
							<div class="col-12">
								<p class="lead mb-0"><t>Ingresa la información que se te pide</t></p>
								<p class="lead text-muted mb-0" style="font-size: 12px"><t>(* campos obligatorios)</t></p>
								<span class="badge badge-primary"><t>Paso 1</t></span>
							</div>
						</div>
						<div data-form='mail' class="form-group">
							<label for="mail"><t>Correo electrónico</t>*</label>
				     		<input type="email" class="form-control" id="mail" data-field="true" data-message="Ingresa un correo electrónico válido" data-type="mail"  placeholder="alguien@example.com" >
						</div>
						<div data-form='password' class="form-group">
							<label for="password"><t>Contraseña</t> *</label>
				     		<input type="password" class="form-control" id="password" data-field="true" data-message="Ingresa una contraseña" data-type="text">
						</div>
						<div data-form='reafirm_password' class="form-group">
							<label for="reafirm_password"><t>Repita su contraseña</t> *</label>
				     		<input type="password" class="form-control" id="reafirm_password" data-message="Repite tu contraseña" data-type="text">
						</div>
						<div data-form='name' class="form-group">
					<label for="name"><t>Nombre</t> *</label>
		     		<input type="text" class="form-control" id="name" data-field="true" data-message="Ingresa tu nombre" data-type="text">
				</div>

			  	<div data-form='last_name' class="form-group">
					<label for="last_name"><t>Apellido paterno</t> *</label>
		     		<input type="text" class="form-control" id="last_name" data-field="true" data-message="Ingresa tu apellido" data-type="text">
				</div>

				<div data-form='second_last_name' class="form-group">
					<label for="second_last_name"><t>Apellido materno</t></label>
		     		<input type="text" class="form-control" id="second_last_name" value="x" 
		     		data-field="true" data-message="Ingresa tu apellido materno" data-type="text">
			    </div>

			    <div data-form='phone' class="form-group">
					<label for="phone"><t>Teléfono fijo</t></label>
		     		<input type="number" class="form-control" id="phone"data-field="true" data-message="Ingresa tu número de teléfono" data-minlenght="10" data-type="number">
				</div>

				<div data-form='cellular' class="form-group">
					<label for="cellular"><t>Celular</t></label>
		     		<input type="number" class="form-control" id="cellular" data-field="true"  data-message="Ingresa tu número de celular"  data-minlenght="10" data-type="number">
				</div>

				<div data-form='sex' class="form-group">
					<label for="cellular"><t>Género</t> *</label>
					<div class="radio">
						<label>
							<input type="radio" name="sex" id="male" value="male" checked>
							<i class="fa fa-male"></i> <t>Masculino</t>
						</label>
					</div>
					<div class="radio">
						<label>
							<input type="radio" name="sex" id="female" value="female">
							<i class="fa fa-female"></i> <t>Femenino</t>
						</label>
					</div>
				</div>
				
				<div data-form='sponsor_id' class="form-group box-typehead">
					<label for="sponsor_id"><t>ID de patrocinador</t></label>
		      		<?php if(!$sponsor_id) { ?>
		     		    <input type="text" class="box-autocomplete-input form-control" id="sponsor_id" data-message="Ingresa un ID de patrocinador válido" data-type="text" data-field="true">
		     		    
						<div  class="text-center" id="sponsor_name"></div>
		      		<?php } else { ?>
		     		    <input type="text" class="box-autocomplete-input form-control" disabled value="<?php echo $sponsor_id?>" id="sponsor_id" data-field="true" data-message="Ingresa tu patrocinador" data-type="number">
		      		<?php } ?>
		     	</div>

				<div class="text-center box-info-colocation">
			    	<div class="text-center">
					    <div data-form='terms' class="checkbox">
						    <label>
						      <input name="terms" id="terms" type="checkbox"> <t>Acepto los</t> <a target="_blank" href="http://<?php echo HCStudio\Connection::$proyect_url;?>/apps/home/legal.php?file=terms_service"><t>términos y condiciones</t></a> <t>y el</t> <a target="_blank" href="http://<?php echo HCStudio\Connection::$proyect_url;?>/apps/home/legal.php?file=independent_affiliate_agreement"><t>contrato de afiliado independiente<t></a>
						    </label>
						</div>
					</div>
		    	</div>	
				
				<div class="row text-center">
					<div class="offset-3 col-6">
						<button class="btn btn-outline-primary btn-radius" id="accept_step_5" onclick="createNewUser()"> <t>Crear cuenta</t></button>				
					</div>
				</div>
				    </div>
			    </div>
			</div>
		</div>

		<!-- <div class="row second_step steps_img py-5 d-none">
			<div class="col-12 col-md-4 offset-md-4 box px-4 py-3">
				<div class="row pb-2 mb-3 text-center border-bottom">
					<div class="col-12">
						<p class="lead mb-0"><t>Ingresa la información que se te pide</t></p>
						<p class="lead text-muted mb-0" style="font-size: 12px"><t>(* campos obligatorios)</t></p>
						<span class="badge badge-primary"><t>Paso 2</t></span>
					</div>
				</div>

				<div data-form='name' class="form-group">
					<label for="name"><t>Nombre</t> *</label>
		     		<input type="text" class="form-control" id="name" data-field="true" data-message="Ingresa tu nombre" data-type="text">
				</div>

			  	<div data-form='last_name' class="form-group">
					<label for="last_name"><t>Apellido paterno</t> *</label>
		     		<input type="text" class="form-control" id="last_name" data-field="true" data-message="Ingresa tu apellido" data-type="text">
				</div>

				<div data-form='second_last_name' class="form-group">
					<label for="second_last_name"><t>Apellido materno</t></label>
		     		<input type="text" class="form-control" id="second_last_name" value="x" 
		     		data-field="true" data-message="Ingresa tu apellido materno" data-type="text">
			    </div>

			    <div data-form='phone' class="form-group">
					<label for="phone"><t>Teléfono fijo</t></label>
		     		<input type="number" class="form-control" id="phone"data-field="true" data-message="Ingresa tu número de teléfono" data-minlenght="10" data-type="number">
				</div>

				<div data-form='cellular' class="form-group">
					<label for="cellular"><t>Celular</t></label>
		     		<input type="number" class="form-control" id="cellular" data-field="true"  data-message="Ingresa tu número de celular"  data-minlenght="10" data-type="number">
				</div>

				<div data-form='sex' class="form-group">
					<label for="cellular"><t>Género</t> *</label>
					<div class="radio">
						<label>
							<input type="radio" name="sex" id="male" value="male" checked>
							<i class="fa fa-male"></i> <t>Masculino</t>
						</label>
					</div>
					<div class="radio">
						<label>
							<input type="radio" name="sex" id="female" value="female">
							<i class="fa fa-female"></i> <t>Femenino</t>
						</label>
					</div>
				</div>

				<div class="col-12 text-center">
					<button class="btn btn-outline-primary btn-radius" id="accept_step_1" onclick="stepOne(-1)"> <t>Atrás</t> </button>
			   		<button class="btn btn-outline-primary btn-radius" id="accept_step_1" onclick="stepOne(2)"> <t>Siguiente</t> </button>
				</div>
		   	</div>
		</div>

		<div class="row third_step steps_img d-none py-5">
		  	<div class="col-12 col-md-4 offset-md-4 box px-4 py-3">
		  		<div class="row pb-2 mb-3 text-center border-bottom">
					<div class="col-12">
						<p class="lead mb-0"><t>Ingresa la información que se te pide</t></p>
						<span class="badge badge-primary"><t>Paso 3</t></span>
					</div>
				</div>
				<div class="text-center box-title-step">
					<h3><t>Proporcione su domicilio</t></h3>
				</div>	 	  
				<div class="form-group">
					<label for="exampleFormControlSelect1"><t>Seleccione un país</t></label>
					<select class="form-control" id="country">
					<?php $countrys = $Country->getAllByWeb(); ?> 
						<?php foreach($countrys as $key => $country): ?>	
							<option value="<?php echo $country['country_id'];?>" <?php if($country['country_id'] == 159) {?>selected<?php } ?>><?php echo $country['nicename'];?></option>
						<?php endforeach ?>
					</select>
				</div>
				<div data-form='street' class="form-group">
					<label for="street"><t>Dirección</t></label>
		     		<input type="text" id="street" class="form-control street"
		     		data-field="true" data-message="Ingresa tu domicilio" data-type="text">
			 	</div>
				<div data-form='colony' class="form-group">
					<label for="colony"><t>Colonia</t></label>
			     	<input type="text" id="colony" class="form-control colony" data-field="true" data-message="Ingresa tu colonia" data-type="text">
				</div>
				<div data-form='zip_code' class="form-group">
					<label for="zip_code"><t>Código postal</t></label>
		     		<input type="number" id="zip_code" class="form-control zip_code" data-field="true" data-message="Ingresa tu código postal (5 dígitos)" data-minlenght="5" data-type="number">
			    </div>
			    <div data-form='city' class="form-group">
					<label for="city"><t>Ciudad</t></label>
			    	<input type="text" id="city" class="form-control city" data-field="true" data-message="Ingresa tu ciudad" data-type="text">
				</div>
				<div data-form='state' class="form-group">
					<label for="state"><t>Estado</t></label>
			     	<input type="text" id="state" class="form-control state" data-field="true" data-message="Ingresa tu estado" data-type="text">
				</div>
				<div class="col-12 text-center">
					<button class="btn btn-outline-primary btn-radius" id="accept_step_1" onclick="stepOne(9)"> <t>Atrás</t> </button>
			   		<button class="btn btn-outline-primary btn-radius" id="accept_step_1" onclick="stepOne(4)"> <t>Siguiente</t> </button>
				</div>
			</div>
		</div> -->
				
	<!-- 	<div class="row fifth_step steps_img d-none py-5">
		  	<div class="col-12 col-md-4 offset-md-4 box px-4 py-3">
		  		<div class="row pb-2 mb-3 text-center border-bottom">
					<div class="col-12">
						<p class="lead mb-0"><t>Ingresa la información que se te pide</t></p>
						<span class="badge badge-primary"><t>Paso 4</t></span>
					</div>
				</div>

				<div data-form='sponsor_id' class="form-group box-typehead">
					<label for="sponsor_id"><t>ID de patrocinador</t></label>
		      		<?php if(!$sponsor_id) { ?>
		     		    <input type="text" class="box-autocomplete-input form-control" id="sponsor_id" data-message="Ingresa un ID de patrocinador válido" data-type="text" data-field="true">
		     		    
						<div  class="text-center" id="sponsor_name"></div>
		      		<?php } else { ?>
		     		    <input type="text" class="box-autocomplete-input form-control" disabled value="<?php echo $sponsor_id?>" id="sponsor_id" data-field="true" data-message="Ingresa tu patrocinador" data-type="number">
		      		<?php } ?>
		     	</div>

				<div class="text-center box-info-colocation">
			    	<div class="text-center">
					    <div data-form='terms' class="checkbox">
						    <label>
						      <input name="terms" id="terms" type="checkbox"> <t>Acepto los</t> <a target="_blank" href="http://<?php echo HCStudio\Connection::$proyect_url;?>/apps/home/legal.php?file=terms_service"><t>términos y condiciones</t></a> <t>y el</t> <a target="_blank" href="http://<?php echo HCStudio\Connection::$proyect_url;?>/apps/home/legal.php?file=independent_affiliate_agreement"><t>contrato de afiliado independiente<t></a>
						    </label>
						</div>
					</div>
		    	</div>	
				
				<div class="row text-center">
					<div class="offset-3 col-6">
						<button class="btn btn-outline-primary btn-radius" id="accept_step_5" onclick="createNewUser()"> <t>Crear cuenta</t></button>				
					</div>
				</div>

			</div>
		</div> -->
	</div>
</div>