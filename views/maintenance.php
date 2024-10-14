<?php if($App->maintenanceMode): ?>
	<main class="d-flex space-between">
        <div class="row h-100">
            <div class="col-12 d-flex flex-column justify-content-center align-items-center bg-white">
                <img src="public/assets/img/logo-<?php echo $App->GetLogo(); ?>.png" class="img-fluid mx-auto" alt="Fincatech" style="max-height: 70px;">
                <h1 class="text-center px-3">Modo mantenimiento</h1>
                <p class="font-weight-bold">Actualmente nos encontramos realizando tareas de mantenimiento en nuestro sistema</p>
                <p>El tiempo estimado es de <?php echo $App->estimatedMaintenanceTime; ?>.</p>
                <img src="public/assets/img/maintenance.jpg" style=" max-height: 480px;" alt="Fincatech" title="Fincatech" class="img-fluid">
                <p>Para cualquier consulta o urgencia, por favor, contacte con nosotros a través del e-mail <a href="mailto:info@fincatech.es" target="_blank" title="Envier e-mail">info@fincatech.es</a> o <a href="mailto:soporte@fincatech.es" title="Enviar correo a soporte" target="_blank">soporte@fincatech.es</a></p>
            </div>
        </div>
	</main>
<?php endif; ?>