<div class="my-1 p-1 bg-white rounded shadow-sm box-visitors">
	<div class="row">
		<div class="col-12 my-5 col-md-10 offset-md-1">
			<div class="row pb-3 mb-5">
				<div class="col-12">
					<p class="lead p-0 m-0" style="font-size: 32px"><a href=".../../../../apps/medical/" class="btn btn-outline-primary">Atrás</a> Enfermedad <b><?php echo $CatalogCaseFile->suffering;?></b></p>
					<p class="lead p-0 m-0" style="font-size: 22px; color: #333">Fecha de creación <b><?php echo date("Y-m-d",$CatalogCaseFile->create_date);?></b></p>
					<p class="lead p-0 m-0" style="font-size: 22px; color: #333">Fecha de actualización <b><?php echo date("Y-m-d",$CatalogCaseFile->update_date);?></b></p>
					<div class="row my-3">
						<div class="col-12">
							<?php $key_words = explode(",", $CatalogCaseFile->key_words); ?>
							<?php if(isset($key_words)) { ?>
								<p class="lead m-0 p-0">KeyWords</p>
								<?php foreach($key_words as $key_word): ?>
									<span class="badge badge-success"><?php echo $key_word; ?></span>	
								<?php endforeach ?>
							<?php } else { ?>
								<p class="lead m-0 p-0">No tiene KeyWords</p>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>

			<table class="table">
			  <thead>
			    <tr>
  			      <th class="text-center" scope="col">ID</th>
			      <th class="text-center" scope="col">Enfermedad</th>
			      <th class="text-center" scope="col">Regla de género</th>
			      <th class="text-center" scope="col">Regla de edad</th>
			      <th class="text-center" scope="col">Regla de IMC</th>
			      <th class="text-center" scope="col">Regla de alcoholismo</th>
			      <th class="text-center" scope="col">Regla de tabaquismo</th>
			      <th class="text-center" scope="col">Regla de cirugías</th>
			      <th class="text-center" scope="col">Opciones</th>
			    </tr>
			  </thead>
				<?php if($catalog_signs = $CatalogSign->getIn($CatalogCaseFile->catalog_sign_ids)) { ?>
				  <tbody>
					<?php foreach($catalog_signs as $catalog_sign): ?>
					    <tr>
					      <th class="text-center align-middle" scope="row"><?php echo $catalog_sign['catalog_sign_id']; ?></th>
					      <td class="text-center align-middle"><?php echo $catalog_sign['sign']; ?></td>
					      <th class="text-center align-middle" scope="row">
					     	<p class="lead" style="font-size: 12px">
						      	<?php if($catalog_sign['gender'] == 1) { ?>
						      		 <?php echo $CatalogSign->getGenderFormat($catalog_sign['gender']); ?>
						      	<?php } else if($catalog_sign['gender'] == 2) { ?>
						      		 <?php echo $CatalogSign->getGenderFormat($catalog_sign['gender']); ?>
						      	<?php } else { ?>
						      		 <?php echo $CatalogSign->getGenderFormat($catalog_sign['gender']); ?>
						      	<?php } ?>
				      	  	</p>
			      	  	  </th>

			      	  	  <th class="text-center align-middle" scope="row">
					     	<p class="lead" style="font-size: 12px">
						      	<?php if($catalog_sign['age'] == 1) { ?>
						      		 <?php echo $CatalogSign->getAgeFormat($catalog_sign['age']); ?>
						      	<?php } else if($catalog_sign['age'] == 2) { ?>
						      		 <?php echo $CatalogSign->getAgeFormat($catalog_sign['age']); ?>
						      	<?php } else if($catalog_sign['age'] == 3) { ?>
						      		 <?php echo $CatalogSign->getAgeFormat($catalog_sign['age']); ?>
						      	<?php } else if($catalog_sign['age'] == 4) { ?>
						      		 <?php echo $CatalogSign->getAgeFormat($catalog_sign['age']); ?>
						      	<?php } else { ?>
						      		 <?php echo $CatalogSign->getAgeFormat($catalog_sign['age']); ?>
						      	<?php } ?>
						    </p>
					      </th>  

					      <th class="text-center align-middle" scope="row">
					     	<p class="lead" style="font-size: 12px">
						      	<?php if($catalog_sign['imc'] == 1) { ?>
						      		 <?php echo $CatalogSign->getIMCFormat($catalog_sign['imc']); ?>
						      	<?php } else if($catalog_sign['imc'] == 2) { ?>
						      		 <?php echo $CatalogSign->getIMCFormat($catalog_sign['imc']); ?>
						      	<?php } else if($catalog_sign['imc'] == 3) { ?>
						      		 <?php echo $CatalogSign->getIMCFormat($catalog_sign['imc']); ?>
						      	<?php } else if($catalog_sign['imc'] == 4) { ?>
						      		 <?php echo $CatalogSign->getIMCFormat($catalog_sign['imc']); ?>
						      	<?php } else if($catalog_sign['imc'] == 5) { ?>
						      		 <?php echo $CatalogSign->getIMCFormat($catalog_sign['imc']); ?>
						      	<?php } else if($catalog_sign['imc'] == 6) { ?>
						      		 <?php echo $CatalogSign->getIMCFormat($catalog_sign['imc']); ?>
						      	<?php } else { ?>
						      		 <?php echo $CatalogSign->getIMCFormat($catalog_sign['imc']); ?>
						      	<?php } ?>
					      	</p>
				      	  </th>

				      	  <th class="text-center align-middle" scope="row">
					     	<p class="lead" style="font-size: 12px">
					      	 	<?php if($catalog_sign['alcoholism'] == 1) { ?>
						      		 <?php echo $CatalogSign->getAlcoholismFormat($catalog_sign['alcoholism']); ?>
						      	<?php } else { ?>
						      		 <?php echo $CatalogSign->getAlcoholismFormat($catalog_sign['alcoholism']); ?>
						      	<?php } ?>
				      	  	</p>
			      	  	  </th>

			      	  	  <th class="text-center align-middle" scope="row">
					     	<p class="lead" style="font-size: 12px">
						      	<?php if($catalog_sign['smoke'] == 1) { ?>
						      		 <?php echo $CatalogSign->getSmokeFormat($catalog_sign['smoke']); ?>
					      		<?php } else if($catalog_sign['smoke'] == 2) { ?>
					      			 <?php echo $CatalogSign->getSmokeFormat($catalog_sign['smoke']); ?>
					      		<?php } else if($catalog_sign['smoke'] == 3) { ?>
					      			 <?php echo $CatalogSign->getSmokeFormat($catalog_sign['smoke']); ?>
					      		<?php } else if($catalog_sign['smoke'] == 4) { ?>
					      			 <?php echo $CatalogSign->getSmokeFormat($catalog_sign['smoke']); ?>
						      	<?php } else { ?>
						      		 <?php echo $CatalogSign->getSmokeFormat($catalog_sign['smoke']); ?>
						      	<?php } ?>
					      	</p>
			      		  </th>

			      		  <th class="text-center align-middle" scope="row">
					     	<p class="lead" style="font-size: 12px">
						      	<?php if($catalog_sign['previous_surgeries'] == 1) { ?>
						      		 <?php echo $CatalogSign->getPreviousSurgeriesFormat($catalog_sign['previous_surgeries']); ?>
					      		<?php } else if($catalog_sign['previous_surgeries'] == 2) { ?>
					      			 <?php echo $CatalogSign->getPreviousSurgeriesFormat($catalog_sign['previous_surgeries']); ?>
					      		<?php } else if($catalog_sign['previous_surgeries'] == 3) { ?>
					      			 <?php echo $CatalogSign->getPreviousSurgeriesFormat($catalog_sign['previous_surgeries']); ?>
					      		<?php } else if($catalog_sign['previous_surgeries'] == 4) { ?>
					      			 <?php echo $CatalogSign->getPreviousSurgeriesFormat($catalog_sign['previous_surgeries']); ?>
					      		<?php } else if($catalog_sign['previous_surgeries'] == 5) { ?>
					      			 <?php echo $CatalogSign->getPreviousSurgeriesFormat($catalog_sign['previous_surgeries']); ?>
					      		<?php } else if($catalog_sign['previous_surgeries'] == 6) { ?>
					      			 <?php echo $CatalogSign->getPreviousSurgeriesFormat($catalog_sign['previous_surgeries']); ?>
					      		<?php } else if($catalog_sign['previous_surgeries'] == 7) { ?>
					      			 <?php echo $CatalogSign->getPreviousSurgeriesFormat($catalog_sign['previous_surgeries']); ?>
						      	<?php } else { ?>
						      		 <?php echo $CatalogSign->getPreviousSurgeriesFormat($catalog_sign['previous_surgeries']); ?>
						      	<?php } ?>
					      	</p>
				      	  </th>
			
					      <td class="text-center align-middle">
					      	<div class="btn-group" role="group">
							    <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							      Opciones
							    </button>
							    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
							      <a class="dropdown-item" onclick="changeSignName(<?php echo $catalog_sign['catalog_sign_id'];?>)" href="#">Cambiar nombre</a>
							    </div>
							  </div>
					      </td>
					      <td class="text-center align-middle">
					      	<!-- <button class="btn btn-danger" onclick="deleteSign()">Eliminar</button> -->
					      	<div class="btn-group" role="group">
							    <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							      Reglas
							    </button>
							    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
							      <a class="dropdown-item" onclick="changeGenderRuleCatalogSign(<?php echo $catalog_sign['catalog_sign_id'];?>)" href="#">Cambiar regla de género</a>
							      <a class="dropdown-item" onclick="changeAgeRuleCatalogSign(<?php echo $catalog_sign['catalog_sign_id'];?>)" href="#">Cambiar regla de edad</a> 
							      <a class="dropdown-item" onclick="changeIMCRuleCatalogSign(<?php echo $catalog_sign['catalog_sign_id'];?>)" href="#">Cambiar regla de IMC</a>
							      <a class="dropdown-item" onclick="changeAlcoholismRuleCatalogSign(<?php echo $catalog_sign['catalog_sign_id'];?>)" href="#">Cambiar regla de Alcoholismo</a>
							      <a class="dropdown-item" onclick="changeSmokeRuleCatalogSign(<?php echo $catalog_sign['catalog_sign_id'];?>)" href="#">Cambiar regla de Tabaquismo</a>
							      <a class="dropdown-item" onclick="changePreviousSurgeriesRuleCatalogSign(<?php echo $catalog_sign['catalog_sign_id'];?>)" href="#">Cambiar regla de Cirugías previas</a>
							    </div>
							  </div>
					      </td>
					    </tr>
					<?php endforeach ?>
				  </tbody>
				<?php } ?>
			</table>
		</div>
	</div>
</div>