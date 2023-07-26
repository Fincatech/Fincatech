<div class="row">

    <div class="col-12 d-flex">

        <div class="card flex-fill shadow-neumorphic pl-3 pb-3 pt-2 pr-3">

            <div class="card-header pl-1 mb-2">

                <h5 class="card-title mb-0"><i data-feather="<?php echo $iconoAccion; ?>"></i> <span class="titulo titulo-modulo">Representante Legal</span></h5>

            </div>
    
            <div class="card-body shadow-inset rounded-lg border mb-1 border-white">

                <form class="form-data form-floating form-representante-legal" autocomplete="false">
                    <input autocomplete="new-password" name="hidden" type="text" style="display:none;">
                    <!-- Estado del usuario -->
                    <div class="form-group row mb-4 justify-content-end">
                        <div class="col-12 col-md-2 text-left">
                            <label for="estado"><i class="bi bi-geo-alt pr-2"></i>Estado</label>
                            <select id="estado" name="estado" class="select-data data custom-select form-control selectpicker" data-live-search="true" hs-entity="Representantelegal" hs-field="estado" hs-list-entity="Estado" hs-list-field="nombre" hs-list-value="sId"></select>
                        </div>
                    </div>                    
                    <!-- Nombre y apellidos -->
                    <div class="form-group row mb-4">
                         <div class="col-12 col-md-4 text-left">
                            <label for="nombre" class="pl-0"><i class="bi bi-person pr-2"></i>Nombre*</label>
                            <input type="text" class="form-control form-required data text-left" id="nombre" name="nombre" maxlength="100" placeholder="Nombre" hs-entity="Representantelegal" hs-field="nombre" form-error="Nombre" required>
                        </div>                        
                        <div class="col-12 col-md-4">
                            <label for="apellido"><i class="bi bi-person pr-2"></i>Primer Apellido*</label>              
                            <input type="text" class="form-control data form-required text-left" id="apellido" name="apellido" placeholder="Primer Apellido" maxlength="100" hs-entity="Representantelegal" hs-field="primerapellido"  form-error="Primer apellido" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="apellido2"><i class="bi bi-person pr-2"></i>Segundo Apellido*</label>              
                            <input type="text" class="form-control data form-required text-left form-required" id="apellido2" name="apellido2" hs-entity="Representantelegal" placeholder="Segundo Apellido" hs-field="segundoapellido" form-error="Segundo Apellido" maxlength="100" required>
                        </div>  

                                                                      
                    </div> 
                    <!-- E-mail -->
                    <div class="form-group row mb-4">

                        <div class="col-12 col-md-4 col-xl-4">
                            <label for="email"><i class="bi bi-envelope-at pr-2"></i>E-mail de contacto*</label>                          
                            <input type="text" maxlength="255" class="form-control data form-required text-left" id="email" name="email" placeholder="E-mail de contacto" hs-entity="Representantelegal" hs-field="email" form-error="E-mail de contacto" required>
                        </div>

                        <div class="col-12 col-md-4 col-xl-2">
                            <label for="telefono"><i class="bi bi-credit-card-2-front pr-2"></i>CIF/NIF*</label>                          
                            <input type="text" maxlength="15" class="form-control data form-required text-center" id="documento" name="documento" placeholder="CIF/NIF" hs-entity="Representantelegal" hs-field="documento" form-error="CIF/NIF" required>
                        </div> 

                        <div class="col-12 col-md-4 col-xl-2">
                            <label for="telefono"><i class="bi bi-phone pr-2"></i>Teléfono móvil*</label>                          
                            <input type="text" maxlength="15" class="form-control data form-required text-center" id="telefono" name="telefono" placeholder="Teléfono móvil" hs-entity="Representantelegal"  form-error="Teléfono de contacto" form-error="Teléfono" hs-field="telefono" required>
                        </div>  
                   
                    </div>
                    <!-- Documento identificativo -->                   
                    <div class="form-group row mb-4">

                        <div class="col-12 col-xl-8 col-xxl-6 mt-3 pt-2 d-flex flex-column mx-auto">
                            <div class="shadow-neumorphic br-10 border ">

                                <h5 class="card-title mb-0 text-uppercase font-weight-normal px-2 pt-1 mb-3 text-center border-bottom pb-2"><i class="bi bi-person-vcard pr-2"></i> Documento identificativo</h5>

                                <div class="row pl-2 pr-2">
                                    <div class="col-12 table-responsive">
                                        <table class="table hs-tabla no-clicable w-100">
                                            <thead class="thead">
                                                <tr class="text-uppercase">
                                                    <th width="160px">Imagen</th>
                                                    <th width="140px" class="text-center">Fecha de subida</th>
                                                    <th width="70px" class="text-center">Estado</th>
                                                    <th>&nbsp;</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Frontal</td>
                                                    <td class="text-center fecha-frontal">N/D</td>
                                                    <td class="text-center status-frontal">
                                                        <span class="badge rounded-pill text-uppercase bg-danger d-block pt-2 pb-2 pl-5 pr-5 mx-3">Pendiente subir</span>
                                                    </td>                                                                
                                                    </td>                                                        
                                                    <td>
                                                        <div class="custom-file ml-2 text-center">
                                                            <input type="file" accept="image/png, image/jpeg, .jpg" class="custom-file-input text-center file-attach" id="fileFrontDocument" name="fileFrontDocument" lang="es">
                                                            <label class="custom-file-label d-block w-100" for="fileFrontDocument" style="border-radius: 20px;cursor: pointer;"><i class="bi bi-image"></i> Adjuntar</label>
                                                        </div>                                                            
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Anverso</td>
                                                    <td class="text-center fecha-anverso">N/D</td>
                                                    <td class="status-anverso">
                                                        <span class="badge rounded-pill text-uppercase bg-danger d-block pt-2 pb-2 pl-5 pr-5 mx-3">Pendiente subir</span></td>                                                                
                                                    </td>                                                        
                                                    <td>
                                                        <div class="custom-file ml-2 text-center">
                                                            <input type="file" accept="image/png, image/jpeg, .jpg" class="custom-file-input file-attach" id="fileRearDocument" name="fileRearDocument" lang="es">
                                                            <label class="custom-file-label d-block w-100" for="fileRearDocument" style="border-radius: 20px;cursor: pointer;"><i class="bi bi-image"></i> Adjuntar</label>
                                                        </div>                                                            
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>                  

                <!-- Observaciones -->

                    <div class="form-group row mb-4">

                        <div class="col-12">
                            <label for="observaciones"><i class="bi bi-pencil pr-2"></i>Observaciones</label>                          
                            <textarea id="observaciones" name="observaciones" class="form-control data text-left shadow-inset border-0" rows="5" hs-entity="Representantelegal" hs-field="observaciones"></textarea>
                        </div>

                    </div>  

                    <?php if($App->getAction() == 'add'): ?>

                        <input type="hidden" id="hadministradorid" name="hadministradorid" value="<?php echo $_GET['idadmin']; ?>" class="data" hs-entity="Representantelegal" hs-field="administradorid">>

                    <?php endif; ?>

                    <?php if($App->getAction() == 'get'): ?>

                        <input type="hidden" id="administradorid" class="data form-control" hs-entity="Representantelegal" hs-field="administradorid" name="administradorid">

                        <div class="card-header pl-1 mb-2">

                        <!-- <h5 class="card-title mb-0"><span class="titulo">Comunidades Asignadas / Representantes Legales</span></h5> -->

                        </div>

                    <?php //$App->renderView('administrador/tabs'); ?>

                    <?php endif; ?>

                    <?php $App->renderActionButtons(); ?>

                </form>

            </div>

    </div>

</div>