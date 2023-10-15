<!-- <script type="text/javascript" src="https://sw.banwire.com/checkout.js"></script> -->
<section class="default-landscape">
    <article class="main-box">
    	<div class="row margen">		    
			<div class="offset-md-1 col-md-10" >

				<?php if($reg_buys || $validate_buys || $shared_shipping) { ?>
					<!-- Nav tabs -->
					<ul class="nav nav-tabs register-tabs" role="tablist">
						<li class="nav-item">
					    	<a class="nav-link <?php echo ($tab == "buys" || !$tab) ? 'active':'' ; ?>" href="#buys" role="tab" data-toggle="tab">Compras por registrar</a>
					  	</li>
					  	<li class="nav-item">
					    	<a class="nav-link <?php echo ($tab == "registrated") ? 'active':'' ; ?>" href="#registrated" role="tab" data-toggle="tab">Compras registradas</a>
					  	</li>	

						<?php 
						if($shared_shipping){
						?>
							<li class="nav-item">
						    	<a class="nav-link <?php echo ($tab == "sharedshipping") ? 'active':'' ; ?>" href="#sharedshipping" role="tab" data-toggle="tab">Compras Compartidas</a>
						  	</li>
						<?php
						}
						?>

					</ul>
					
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane fade <?php echo ($tab == "buys" || !$tab) ?'in active show' :''?>" id="buys">
					        <div>
						    	<?php if($reg_buys) { ?>
								    <table class="text-center table">
								    	<thead>
								    		<tr>
								    			<th>Número de compra</th>
								    			<th>Envio</th>
								    			<th>Monto a pagar</th>
								    			<th>Puntos</th>
								    			<th>Fecha de creación</th>
								    			<th>Opciones</th>
			 					    		</tr>
								    	</thead>
								    	<tbody>
									    	<?php foreach ($reg_buys as $buy_per_user_login => $buy) { ;?>
										    	<?php if($buy['payment_date'] == '0000-00-00 00:00:00') { ?>
										    		<tr data-target_id='<?php echo $buy["buy_per_user_login_id"]; ?>' class='buy_data'>
											    		<td data-step="1" data-intro='<center>Número de compra de su pedido</center>' class='watch_buy' data-buy_per_user_login='<?php echo $buy["buy_per_user_login_id"]; ?>' data-products='<?php echo ($buy['products'])?>'><?php echo $buy["buy_per_user_login_id"]; ?></td>
														
														<?php 
															$shippingShared = $ShippingShared->getShippingShared($buy['buy_per_user_login_id']);

															if($shippingShared){
																$envio = "Si";
															}else{
																$envio = ($buy['shipping']>0) ? 'Sí' : 'No';
															}

														?>
			

											    		<td><?php echo $envio;?></td>
											    		<?php $ammount = $buy['ammount']+ $buy['charges'] + $buy['shipping'] ?>
											    		<td>$ <?php echo number_format($ammount,2);?> </td>
											    		<td><?php echo $buy['points']?></td>
											    		<td><?php echo $buy['date_create']?></td>
											    		<td>
											    		<?php if($buy['payment_method'] != 5) { ?>
											    		  <button data-buy_per_user_login='<?php echo $buy["buy_per_user_login_id"]; ?>' class="btn btn-primary btn-sm registrate">Registrar</button>
											    		<?php } else if($buy['payment_method'] == 5) { ?>
											    		  <button data-buy_per_user_login='<?php echo $buy["buy_per_user_login_id"]; ?>' class="btn btn-primary btn-sm pay-banwire">Pagar ahora</button>
											    		<?php } ?>
											    		<button class="btn btn-success btn-sm delete" data-buy_per_user_login='<?php echo $buy["buy_per_user_login_id"]; ?>'>Eliminar</button></td>
										    		</tr>
										    	<?php } ?>
									    	<?php } ?>
								    	</tbody>
								    	<tfoot>
								    		<tr>
								    			<td colspan="6"> <b>Para ver más información de su compra presione en el <n class='anchor' id="show_buy_number">número de compra</n> de algún pedido</b> </td>
								    		</tr>
								    	</tfoot>
								    </table>
								<?php } else { ?>
									<div class="col-md-12 text-center">
									    <h1>Lo sentimos no cuenta compras para registrar <br>
										    <small>Para hacer su pedido por favor diríjase a <a href="./" class="links">carrito de compras</a></small>
										</h1>
									</div>
								<?php } ?>
					        </div>
						</div>

						<div role="tabpanel" class="tab-pane fade <?php echo ($tab == "registrated") ?'in active show' :''?>" id="registrated">
					        <div>
						    	<?php if($validate_buys) { ?>
								    <table class="text-center table">
								    	<thead>
								    		<tr>
								    			<th>Número de compra</th>
								    			<th>Envio</th>
								    			<th>Monto Pagado</th>
								    			<th>Puntos</th>
								    			<th>Fecha de creación</th>
								    			<th>Guia envio</th>
								    			<th>Estatus</th>
								    		</tr>
								    	</thead>
								    	<tbody>
									    	<?php foreach ($validate_buys as $buy_per_user_login => $buy) {

									    		if($buy['verified'] == '1' || $buy['verified'] == '0'){
									    		?>

									    		<tr data-target_id='<?php echo $buy["buy_per_user_login_id"]; ?>' class='buy_data <?php if($bpulid == $buy["buy_per_user_login_id"]) { ?> selected <?php } ?>'>
										    		<td class='watch_buy' data-buy_per_user_login='<?php echo $buy["buy_per_user_login_id"]; ?>' data-products='<?php echo ($buy['products'])?>'><?php echo $buy["buy_per_user_login_id"]; ?></td>
										    		<td><?php echo ($buy['shipping']>0) ? 'Sí' : 'No';?></td>
										    		<?php $ammount = $buy['ammount']+ $buy['charges'] + $buy['shipping'] ?>
										    		<td>$ <?php echo $ammount;?> </td>
										    		<td><?php echo $buy['points']?></td>
										    		<td><?php echo $buy['date_create']?></td>
											    	<td>
											    		<?php echo $buy["shipping_guide"]; ?>

											    		<?php if($buy["shipping_guide"]) { ?>
												    		<?php if($buy['tracker_id'] == 0){ ?>
												    		<span class="box-shipping box-shipping-dhl">DHL</span>
												    		<?php } else if($buy['tracker_id'] == 1){ ?>
												    		<span class="box-shipping box-shipping-redpack">Red Pack</span>
												    		<?php } else if($buy['tracker_id'] == 2){ ?>
												    		<span class="box-shipping box-shipping-estafeta">Estafeta</span>
												    		<?php } ?>
											    		<?php } ?>
												    </td>
											    	<?php if($buy['date_validation'] == '0000-00-00 00:00:00') { ?>
											    		<td class="success">
													        Pendiente de validar
											    		</td>
											    	<?php } else { ?>
												    	<td class="info">
											    		    Validada
												    	</td>
											    	<?php } ?>
									    		</tr>
									    	<?php }
									    		}
									    	?>
								    	</tbody>
								    	<tfoot >
								    		<tr>
								    			<td colspan="7">

								    			<h2 style="margin-top: 10px;">
								    			    Para ver el detalle de tu compra da click en el número de compra.
									    			<br>
									    			<small>Las compras pendientes de válidar, serán verificadas por el área de ventas de
									    			<a class="links" href='http://www.dineroexpressmexico.com'>dineroexpressmexico.com</a>
									    			</small>
								    			</h2>
								    			</td>
								    		</tr>
								    	</tfoot>
								    </table>
								<?php } else { ?>
								    <div class="col-md-12 text-center">
									    <h2>Lo sentimos no cuenta con compras registradas <br>
										    <small>Para válidar una compra dírijase a "Compras por registrar"</small>
										</h2>
								    </div>
								<?php } ?>
					        </div>

						</div>
						
						<div role="tabpanel" class="tab-pane fade <?php echo ($tab == "sharedshipping") ?'in active show' :''?>" id="sharedshipping">
					        <div>
						    	<?php if($shared_shipping) { 

						    		// echo "<pre>";
						    		foreach ($shared_shipping as $key => $buy) {

						    			$ShippingShared->loadWhere("user_login_id=? AND buy_per_user_login_id=? ",[$buy["user_login_id"],$buy["buy_per_user_login_id"]]);
						    			
						    			$purchases=$ShippingShared->getPurchasesShared($ShippingShared->send_with_order);
						    			?>
						    				<div class="col-md-12 col-lg-12 " >
						    					<div class="row">
													<div class="col-md-12 col-lg-12  box-shared-shipping" >
														Pedido Compartido para orden de compra: <b><?php echo $ShippingShared->send_with_order; ?></b>
													</div>													
												</div>
												<div class="row">
													<div class="col-md-12 col-lg-12  " >
														<table class="text-center table table-hover">
													    	<thead>
													    		<tr>
													    			<th>Número de compra Principal</th>
													    			<th>Numero de Compra Anexa</th>
													    			<th>Folio o Referencia</th>
													    			<th>Id Distribuidor</th>
													    			<th>Status</th>													    			
													    		</tr>
													    	</thead>
												    		<tbody>
												    			<?php
												    				foreach ($purchases as $key => $shared) {
												    					?>
												    						<tr  class='buy_data'>
												    							<td class='watch_buy' data-buy_per_user_login='<?php echo $buy["buy_per_user_login_id"]; ?>' data-products='<?php echo ($shared["user_login_id"]==$UserLogin->company_id) ? $buy['products'] : ""?>'><?php echo $ShippingShared->send_with_order; ?></td>
													    						<!-- <td><?php echo $shared["send_with_order"];?> </td> -->
					    											    		<td><?php echo $shared["buy_per_user_login_id"]?></td>
					    											    		<td><?php echo $shared["reference"]?></td>
					    											    		<td><?php echo $shared["user_login_id"]?></td>
					    											    		<td>
					    											    			<?php 
					    											    				if($shared["reference"]==''){
					    											    					if($ShippingShared->buy_per_user_login_id==$ShippingShared->send_with_order){
					    											    						?>
					    											    							<button class="btn btn-sm btn-primary" onclick="registrateShared(<?php echo $shared["buy_per_user_login_id"];?>)">Registrar</button>
					    											    							<button class="btn btn-sm btn-warning" onclick="deleteShared(<?php echo $shared["buy_per_user_login_id"];?>,<?php echo $shared["send_with_order"];?>)">Eliminar</button>
					    											    						<?php
					    											    					}else if($ShippingShared->buy_per_user_login_id==$shared["buy_per_user_login_id"]){					    											    							
					    											    						if($ShippingShared->user_login_id==$UserLogin->company_id){
								    											    			?>
								    											    				<button class="btn btn-sm btn-primary" onclick="registrateShared(<?php echo $shared["buy_per_user_login_id"];?>)">Registrar</button>
					    											    							<button class="btn btn-sm btn-warning" onclick="deleteShared(<?php echo $shared["buy_per_user_login_id"];?>,<?php echo $shared["send_with_order"];?>)">Eliminar</button>
								    											    			<?php				
					    											    						}else{
					    											    						?>
					    											    							<button class="btn btn-sm btn-primary" onclick="registrateShared(<?php echo $shared["buy_per_user_login_id"];?>)">Registrar</button>
					    											    						<?php
					    											    						}
					    											    					}else{
					    											    						if($ShippingShared->user_login_id==$UserLogin->company_id){
								    											    			?>
								    											    				<button class="btn btn-sm btn-primary" onclick="registrateShared(<?php echo $shared["buy_per_user_login_id"];?>)">Registrar</button>
					    											    							<button class="btn btn-sm btn-warning" onclick="deleteShared(<?php echo $shared["buy_per_user_login_id"];?>,<?php echo $shared["send_with_order"];?>)">Eliminar</button>
								    											    			<?php				
					    											    						}else{

					    											    							echo "<span class='not-register-purchase'>Pendiente de Registrar </span >";	
					    											    						}
					    											    					}
					    											    				}else{
					    											    					echo "<span class='register-purchase'> Registrada </span >";
					    											    				}
					    											    			?>
					    											    			
					    											    		</td>
					    											    	</tr>
												    					<?php
												    				}
												    			?>
												    		</tbody>
												    		<tfoot >
													    		<tr>
													    			<td colspan="5"> 
													    				Te recordamos que los pedidos compartidos pasaran al área de validación una vez que todos los pedidos sean registrados 
													    				<b>Nota: Todo pago de producto debe ser realizado directamente a la cuenta de la empresa. No nos hacemos responsables por depósitos a cuenta de terceros.</b>
													    			</td>
													    		</tr>
													    	</tfoot>													    	
													    </table>
													</div>
												</div>

						    				</div>
						    			<?php						    			
						    		}						    		
						    	?>								
								<?php } else { ?>
									<div class="col-md-12 text-center">
									    <h1>Lo sentimos no cuenta compras para registrar <br>
										    <small>Para hacer su pedido por favor diríjase a <a href="./" class="links">carrito de compras</a></small>
										</h1>
									</div>
								<?php } ?>
					        </div>
						</div>


					    <div class="tracking-box d-none">
					    	<div class="tracking-box-title"></div>
					    	<div class="tracking-box-state"></div>
					    	<div class="tracking-box-ul"></div>
					    </div>
			        </div>
			    <?php } else { ?>
				    <h2>Lo sentimos no cuenta con compras por registrar <br>
					    <small>Para hacer su pedido por favor diríjase a <a href="./">carrito de compras</a></small>
					</h2>
				<?php } ?>
		    </div>
		</div>
    </article>
</section>