<div class="container-fluid py-4" id="app">
	<div class="row justify-content-center">
		<div class="col-12 col-xl-4">
			<div class="card">
				<div class="card-body text-center">
					<div v-if="loading">
						<div class="spinner-border" role="status">
							<span class="sr-only"></span>
						</div>
					</div>
					<div v-else>
						<div 
							v-if="paymentStatus == STATUS.PAYMENT_DONE">
							<div class="fs-4 fw-semibold text-gradient text-primary"><i class="bi bi-bookmark-check-fill"></i></div>
							<div class="fs-4 fw-semibold text-gradient text-primary">Pago aprovado</div>
							<div class="badge p-0 text-dark">Muchas gracias tu pago ha sido aprobado</div>
						</div>
						<div 
							v-else-if="paymentStatus == STATUS.PAYMENT_EXPIRED">
							<strong>Aviso</strong>
							El pago ya fue procesado anteriormente
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>