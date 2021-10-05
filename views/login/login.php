	<main class="d-flex space-between">

		<div class="container d-flex flex-column flex-grow-1">

			<div class="row h-100">

				<div class="col-sm-10 col-md-8 col-lg-6 mx-auto d-table h-100">

					<div class="d-table-cell align-middle">

						<div class="text-center mt-4">
						</div>

						<div class="card" style="box-shadow: 0px 0px 10px lightgrey;">

							<div class="card-body pt-0">

								<div class="m-sm-4">

									<div class="text-center pb-2">
							            <img src="public/assets/img/logo-fincatech.png" alt="Fincatech" style="max-height: 70px;">
									</div>

									<form class="form-data" id="formLogin" name="formLogin" autocomplete="off" style="border: 1px solid #e4e4e4;padding: 24px;border-radius: 10px;box-shadow: inset 0px 0px 16px #efefef;">
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