<div class="my-1 p-1 bg-white rounded shadow-sm box-visitors">
	<div class="row">
		<div class="col-12 my-5 col-md-10 offset-md-1">
			<div class="row pb-2  text-center mb-4">
				<div class="col-12">
					<p class="lead" style="font-size: 22px">Especialidades médicas</p>
					
					<div class="alert alert-primary" role="alert">
					  En esta área puedes elegir las especialidades médicas que practiques como profesional.
					</div>
				</div>
			</div>
			<div class="row table-responsive">
				<table class="table table-hover">
				  <thead>
				    <tr>
				      <th class="text-center" scope="col">Especialidad</th>
				      <th class="text-center" scope="col">Descripción</th>	
				      <th class="text-center" scope="col">Opciones</th>
				    </tr>
				  </thead>	
					<?php if($CatalogSpeciality) { ?>
					  <tbody>
						<?php foreach($CatalogSpeciality as $catalogSpeciality){ ?>
						    <tr>
						      <td class="text-center align-middle"><p class="lead m-0"><?php echo ucfirst($catalogSpeciality['speciality']); ?></p></td>
						      <td class="text-center align-middle"><p class="lead m-0"><?php echo ucfirst($catalogSpeciality['description']); ?></p></td>
						      <td class="text-center align-middle">
				                <?php   if(!$CatalogMedicTopic->cargardonde("catalog_speciality_id=? AND user_support_id=? AND status =?",[$catalogSpeciality['catalog_speciality_id'],$UserSupport->getId(),1])){?>
				                  <button type="button" class="btn btn-primary" onclick="add_my_speciality(this,<?php echo $catalogSpeciality['catalog_speciality_id']?>)">Agregar a mis especialidades</button>
				                <?php }else{?>
				                  <button type="button" class="btn btn-danger" onclick="remove_my_speciality(this,<?php echo $catalogSpeciality['catalog_speciality_id']?>)">Remover  de mis especialidades</button>
				                <?php }?>
				                </td>
						    </tr>
						<?php } ?>
					  </tbody>
					<?php } ?>
				</table>
				
			</div>
		</div>
	</div>
</div>