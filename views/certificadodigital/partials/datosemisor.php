<div class="row mt-3">
    <div class="col-12 shadow-inset p-3 br-10">
        <div class="row">
            <div class="col-12 col-md-8">
                <h5 class="card-title mb-0 text-uppercase font-weight-normal pt-1"><i class="bi bi-person pr-2"></i> <span>Datos del titular / Representante legal</span></h5>
            </div>
            <div class="col-12 col-md-4 text-right">
            <a href="javascript:void(0);" class="btn btnSaveData btn-success shadow " style="border-radius: 50rem"><i class="bi bi-save mr-2"></i>Actualizar datos emisor</a>
            </div>
        </div>
        <!-- Nombre/Apellidos, nif/cif y e-mail de contacto -->
        <div class="form-group row mt-3">
            <div class="col-12 col-md-4">
                <label for="emisorNombreApellidos">Nombre y apellidos representante legal*</label>
                <input type="text" class="form-control" maxlength="70" name="emisorNombreApellidos" id="emisorNombreApellidos" placeholder="Escriba su nombre y apellidos" required>
            </div>
            <div class="col-12 col-md-2">
                <label for="emisorNIF">NIF/CIF*</label>
                <input type="text" class="form-control text-center" maxlength="20" name="emisorNIF" id="emisorNIF" placeholder="Escriba su CIF/NIF" required>
            </div>
            <div class="col-12 col-md-2">
                <label for="emisorEmail">Teléfono móvil*</label>
                <input type="email" class="form-control" maxlength="15" name="emisorMovil" id="emisorMovil" placeholder="Escriba tel. móvil de contacto" required>
            </div>               
            <div class="col-12 col-md-4">
                <label for="emisorEmail">E-mail de contacto*</label>
                <input type="email" class="form-control" maxlength="255" name="emisorEmail" id="emisorEmail" placeholder="Escriba e-mail de contacto" required>
            </div>                            
        </div>

        <!-- Documento identificativo -->
        <div class="row mt-3 px-2">
            <div class="col-12 col-xl-6 shadow-neumorphic br-10 mt-3 pt-2 border d-flex flex-column mx-auto">
                <h5 class="card-title mb-0 text-uppercase font-weight-normal px-2 pt-1 mb-3 text-center border-bottom pb-2"><i class="bi bi-person-vcard pr-2"></i> Documento identificativo</h5>
                <form>
                    <div class="row pl-2 pr-2">
                        <div class="col-12 table-responsive">
                            <table class="table table-responsive hs-tabla no-clicable">
                                <thead class="thead">
                                    <tr class="text-uppercase">
                                        <th width="160px">Imagen</th>
                                        <th width="140px" class="text-center">Fecha de subida</th>
                                        <!-- <th>Documento</th> -->
                                        <th width="70px" class="text-center">Estado</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Frontal</td>
                                        <td class="text-center">01/01/1900</td>
                                        <!-- <td>
                                            <span class="verdocumento">
                                                <i class="bi bi-search"></i><a href="javascript:void(0);" class="ml-1">ver documento</a>
                                            </span>
                                        </td> -->
                                        <td class="text-center">
                                            <span class="badge rounded-pill text-uppercase bg-success d-block pt-2 pb-2 pl-5 pr-5 mx-3">Subido</span></td>                                                                
                                        </td>                                                        
                                        <td>
                                            <div class="custom-file ml-2 text-center">
                                                <input type="file" accept="image/png, image/jpeg, .jpg" class="custom-file-input text-center" id="fileFrontDocument" name="fileFrontDocument" lang="es">
                                                <label class="custom-file-label d-block w-100" for="fileFrontDocument" style="border-radius: 20px;cursor: pointer;"><i class="bi bi-image"></i> Adjuntar</label>
                                            </div>                                                            
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Anverso</td>
                                        <td class="text-center">01/01/1900</td>
                                        <td>
                                            <span class="badge rounded-pill text-uppercase bg-danger d-block pt-2 pb-2 pl-5 pr-5 mx-3">Pendiente subir</span></td>                                                                
                                        </td>                                                        
                                        <td>
                                            <div class="custom-file ml-2 text-center">
                                                <input type="file" accept="image/png, image/jpeg, .jpg" class="custom-file-input" id="fileFrontDocument" name="fileFrontDocument" lang="es">
                                                <label class="custom-file-label d-block w-100" for="fileFrontDocument" style="border-radius: 20px;cursor: pointer;"><i class="bi bi-image"></i> Adjuntar</label>
                                            </div>                                                            
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Botón de guardar cambios -->
        <div class="form-group row mt-4 justify-content-center d-none">
        <div class="col-12 col-md-5 col-lg-5 col-xl-4 col-12 text-center p-3 shadow-inset rounded-pill border-light border-2">
            <div class="row">
                <div class="col-12">
                    <a href="javascript:void(0);" class="btn btnSaveData btn-success shadow d-block pb-2 pt-2" style="border-radius: 50rem"><i class="bi bi-save mr-2"></i>Actualizar datos emisor</a>
                </div>
            </div>
        </div>
    </div>                        
        <!-- <div class="row mt-2">
            <div class="col-12 text-center">
                <a href="javascript:void(0)" class="btn btn-outline-success text-uppercase rounded-pill shadow pl-2 pr-4 btnSaveAdminDataCertificate"><i class="bi bi-save pr-2 pl-2"></i> ACTUALIZAR DATOS DEL EMISOR</a>                                
            </div>
        </div> -->
    </div>

</div>  