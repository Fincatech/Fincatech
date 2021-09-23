	<main class="d-flex space-between">

		<div class="container d-flex flex-column flex-grow-1">

			<div class="row h-100">

				<div class="col-sm-10 col-md-8 col-lg-6 mx-auto d-table h-100">

					<div class="d-table-cell align-middle">

						<div class="text-center mt-4">
							<h1 class="h2">Fincatech</h1>
							<p class="lead">Accede a tu cuenta</p>
						</div>

						<div class="card">

							<div class="card-body">

								<div class="m-sm-4">

									<div class="text-center">
										<img src="assets/img/avatars/avatar.jpg" alt="Charles Hall" class="img-fluid rounded-circle" width="132" height="132" />
									</div>

									<form class="form-data" id="formLogin" name="formLogin" autocomplete="off">
										<input autocomplete="new-password" type="text" style="display:none;">
										<div class="mb-3">
											<label class="form-label">Email</label>
											<input autocomplete="off" class="form-control form-control-lg form-required data no-autofill" id="email" type="email" hs-entity="Login" hs-field="email" hs-error-msg="" name="email" placeholder="Login de acceso" required />
										</div>
										<div class="mb-3">
											<label class="form-label">Password</label>
											<input id="password" class="form-control form-control-lg form-required data" type="password" hs-entity="Login" hs-field="password" hs-error-msg="" name="password" placeholder="Contraseña" required />
											<small>
                                                <a href="pages-reset-password.html">Has olvidado la contraseña?</a>
                                            </small>
										</div>
										<div>
											<label class="form-check">
                                                <input class="form-check-input remember-me data" hs-entity="Login" hs-field="recordar" type="checkbox" hs-error-msg="" value="1" name="recordar" id="recordar" checked>
                                                <span class="form-check-label">Mantener sesión activa</span>
                                            </label>
										</div>
										<div class="text-center mt-3">
											<button type="button" class="btn btn-lg btn-primary btnAuthenticate">Entrar</button>
											<!-- <button type="submit" class="btn btn-lg btn-primary">Sign in</button> -->
										</div>
									</form>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</main>