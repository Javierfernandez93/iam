<div class="my-1 p-1 bg-white rounded shadow-sm box-visitors">
	<div class="row">
		<div class="col-12 my-5 col-md-10 offset-md-1">
			<div class="row">
				<div class="col-12">

					<div class="row pb-2  text-center mb-4">
						<div class="col-12">
							<p class="lead" style="font-size: 22px">Horario de servicio</p>
							
							<div class="alert alert-primary" role="alert">
							  En esta área debes de seleccionar tu horario, los días que podrás brindar tus servicios médicos en las videoconsultas, así también como el horario.
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-12 col-md-6 offset-md-3">
							<div class="row">
								<div class="col-12">
								  <p>
								    <input type="checkbox"  value="1" id="Lunes"  />
								    <label for="Lunes">Lunes</label>
								  </p><p>
								    <input type="checkbox"   value="2"id="Martes" />
								    <label for="Martes">Martes</label>
								  </p><p>
								    <input type="checkbox"   value="3" id="Miercoles"/>
								    <label for="Miercoles">Miércoles</label>
								  </p><p>
								    <input type="checkbox" value="4" id="Jueves"/>
								    <label for="Jueves">Jueves</label>
								  </p><p>
								    <input type="checkbox"  value="5" id="Viernes"  />
								    <label for="Viernes">Viernes</label>
								  </p>
								  <p>
								    <input type="checkbox"   value="6"id="Sabado" />
								    <label for="Sabado">Sábado</label>
								  </p>
								  <p>
								    <input type="checkbox"  value="7" id="Domingo" />
								    <label for="Domingo">Domingo</label>
								  </p>
								</div>
							</div>

						  	<div class="row">      
					          <div class="col-12">
					          	<div class="input-group mb-3">
								  <div class="input-group-prepend">
								    <span class="input-group-text" id="basic-addon1">Inicio</span>
								  </div>
								  <input class="form-control" type="time" name="hora" value="11:00:00" max="22:30:00" min="10:00:00" id="entrada">
								
								  <div class="input-group-append">
								    <span class="input-group-text" id="basic-addon1">Fin</span>
								  </div>
								  <input class="form-control" type="time" name="hora" value="11:00:00" max="22:30:00" min="10:00:00" id="entrada">
								</div>
					          </div>     
					       	</div>
					   	</div>
					</div>

					<div class="row">
						<div class="col-12 col-md-6 offset-md-3">
					       <button type="button" class="btn btn-block btn-primary align-middle" onclick="save_schedule()">Guardar</button>
					   	</div>
				   	</div>
			   	</div>
			</div>
		</div>
	</div>
</div>