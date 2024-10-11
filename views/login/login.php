<?php if(!$App->isLogged()): ?>
	<main class="d-flex space-between">

		<!-- <div class="container d-flex flex-column flex-grow-1"> -->

			<div class="row h-100">

				<!-- Imagen + logo -->
				<div class="col-12 col-lg-6 d-flex flex-column justify-content-center align-items-center bg-white">

					<h1 class="text-center px-3">Plataforma digital para el control normativo<br>en comunidades de propietarios</h1>
					<img src="public/assets/img/fincatech--bg-login.jpg" style=" max-height: 600px;" alt="Fincatech" title="Fincatech" class="img-fluid">

				</div>

				<!-- Formulario -->


				<div class="col-12 col-lg-6 justify-content-center align-items-center">

					<div class="row h-100 align-items-center align-content-center justify-content-center">

						<div class="col-12 col-lg-10 d-flex flex-column justify-content-center br-10">

								<img src="public/assets/img/logo-<?php echo $App->GetLogo(); ?>.png" class="img-fluid mx-auto" alt="Fincatech" style="max-height: 70px;">

								<form class="form-data mt-4 bg-white br-10" id="formLogin" name="formLogin" autocomplete="off" style="border: 1px solid #e4e4e4;/* padding: 24px; */box-shadow: 6px 7px 10px 0px #ced4da;">

									<input autocomplete="new-password" type="text" style="display:none;">

									<h3 class="text-center font-weight-bold pt-3 pb-3 bg-success d-flex justify-content-center text-white text-uppercase" style="font-family: 'Work Sans';border-radius: 10px 10px 0px 0px;"><i class="bi bi-shield-lock pr-2"></i> Login</h3>
									<!-- Email -->
									<div class="mb-4 px-5 mt-4">
										<label class="form-label font-weight-bold mb-0"><i class="bi bi-envelope-at"></i> Email</label>
										<input autocomplete="off" class="form-control form-control-lg form-required data no-autofill" id="email" type="email" hs-entity="Login" hs-field="email" hs-error-msg="" name="email" placeholder="Login de acceso" pattern="[a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*@[a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{1,5}" required />
									</div>
									<!-- Password -->
									<div class="mb-3 px-5">
										<label class="form-label font-weight-bold mb-0"><i class="bi bi-lock"></i> Password</label>
										<input id="password" class="form-control form-control-lg form-required data" type="password" hs-entity="Login" hs-field="password" hs-error-msg="" name="password" placeholder="Contraseña" required />
										
									</div>

									<div class="row px-5">
										<!-- Mantener sesión activa -->
										<div class="col-6">
											<div class="form-check form-switch">
												<input class="form-check-input remember-me data" hs-entity="Login" hs-field="recordar" type="checkbox" id="recordar" name="recordar">
												<label class="form-check-label" for="recordar">Mantener sesión activa</label>
											</div>										
										</div>
										<!-- Resetear contraseña -->
										<div class="col-6">
											<a href="javascript:void(0);" class="btnResetpassword d-block text-dark text-right"><i class="bi bi-shield-lock"></i> Has olvidado la contraseña?</a>
										</div>
											
									</div>
									<div class="text-center mt-4 px-5 mb-4">
										<button type="button" class="btn btn-lg btnAuthenticate br-16 btn-outline-success px-5 py-2" style="transition: all 500ms ease-in-out"><i class="bi bi-box-arrow-in-right"></i> Entrar</button>
									</div>
								</form>

						</div>
					</div>

				</div>
			</div>
		<!-- </div> -->
	</main>
<?php endif; ?>