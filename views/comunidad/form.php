<div class="row">

    <div class="col-md-3 col-xl-2 pb-4">

        <div class="row h-100">

            <div class="col-12 rounded-lg mb-1">

                <div class="card h-100 mb-1 shadow-neumorphic bg-light">

                    <div class="card-header bg-info">
                        <h5 class="card-title mb-0 titulo font-weight-light text-uppercase text-light"><i class="bi bi-three-dots-vertical"></i> Menú</h5>
                    </div>

                    <div class="list-group list-group-flush" role="tablist">
                        <a class="list-group-item list-group-item-action active" data-toggle="list" href="#datos" role="tab" aria-selected="true"><i class="bi bi-archive mr-2"></i> Datos</a>
                        <a class="list-group-item list-group-item-action" data-toggle="list" href="#documentos" role="tab" aria-selected="false"><i class="bi bi-folder-check mr-2"></i> Documentos</a>
                        <a class="list-group-item list-group-item-action" data-toggle="list" href="#empresa" role="tab" aria-selected="false"><i class="bi bi-truck mr-2"></i> CAE</a>
                        <a class="list-group-item list-group-item-action" data-toggle="list" href="#dpd" role="tab" aria-selected="false"><i class="bi bi-chat-right-text mr-2"></i> DPD</a>
                        <a class="list-group-item list-group-item-action disabled" data-toggle="list" href="#maquinaria" role="tab" aria-selected="false"><i class="bi bi-truck mr-2"></i> Maquinaria / Químicos</a>
                        <a class="list-group-item list-group-item-action disabled" data-toggle="list" href="#rgpd" role="tab" aria-selected="false"><i class="bi bi-truck mr-2"></i> RGPD</a>
                        <!-- <a class="list-group-item list-group-item-action disabled" data-toggle="list" href="#controlhorario" role="tab" aria-selected="false"><i class="bi bi-truck mr-2"></i> Control horario</a> -->
                        <?php if ($App->isSudo()) : ?>
                        <a class="list-group-item list-group-item-action btnCargarComunidadesExcel" href="javascript:void(0);" role="tab" aria-selected="false"><i class="bi bi-file-earmark-spreadsheet mr-2"></i> Cargar desde archivo</a>
                        <?php endif; ?>
                    </div>
                    
                </div>

            </div>

        </div>

    </div>

    <div class="col-md-9 col-xl-10">

        <div class="tab-content">

            <!-- Datos de la comunidad -->
            <div class="tab-pane fade show active" id="datos" role="tabpanel">

                <div class="row">

                    <div class="col-12 d-flex">

                        <div class="card flex-fill shadow-neumorphic pl-3 pb-3 pt-2 pr-3">

                            <div class="card-header pl-1 mb-2">

                                <h5 class="card-title mb-0"><i data-feather="<?php echo $iconoAccion; ?>"></i> <span class="titulo titulo-modulo">Comunidad</span></h5>

                            </div>
                    
                            <div class="card-body shadow-inset rounded-lg border border-white">

                                <form class="form-data form-floating">

                                    <!-- Código - CIF - Nombre -->
                                    <div class="form-group row mb-2">

                                        <!-- Código de comunidad -->
                                        <div class="col-12 col-md-2">
                                            <label for="codigo" class="pl-0"><i class="bi bi-key-fill pr-2"></i>Código*</label>
                                                <input type="text" class="form-control data text-center" id="codigo" name="codigo" placeholder="Código interno" hs-entity="Comunidad" hs-field="codigo" required>
                                        </div>

                                        <!-- CIF -->
                                        <div class="col-12 col-md-2 text-left">
                                            <label for="cif" class="pl-0"><i class="bi bi-credit-card-2-front pr-2"></i>CIF*</label>
                                                <input type="text" class="form-control data text-center" id="cif" name="cif" placeholder="CIF/NIF"  hs-entity="Comunidad" hs-field="cif" required>
                                        </div>  

                                        <!-- Administrador asignado -->
                                        <!-- Sólo pueden los sudo -->
                                        <?php if( $App->isSudo() ): ?>
                                        <div class="col-12 col-md-8">
                                            <label for="usuarioId"><i class="bi bi-person-fill pr-2"></i>Administrador asignado</label>
                                            <select id="usuarioId" name="usuarioId" class="select-data custom-select data form-control selectpicker" data-live-search="true" hs-entity="Comunidad" hs-field="usuarioId" hs-list-entity="Administrador" hs-list-field="Usuario.nombre" hs-list-value="Usuario.id"></select>
                                        </div>                 
                                        <?php endif; ?>

                                    </div>

                                    <!-- Nombre de la comunidad - Nombre del presidente -->
                                    <div class="form-group row mb-2">

                                        <!-- Nombre de la comunidad -->
                                        <div class="col-12 col-md-9 text-left">
                                            <label for="nombre"><i class="bi bi-building pr-2"></i>Nombre</label>
                                            <input type="text" class="form-control data" id="nombre" name="nombre" placeholder="Nombre de la comunidad"  hs-entity="Comunidad" hs-field="nombre" aria-label="nombre" aria-describedby="addon-nombre" required>
                                        </div>                      

                                        <!-- Nombre del presidente -->
                                        <div class="col-12 col-md-3 text-left">
                                            <label for="presidente"><i class="bi bi-person pr-2"></i>Nombre del presidente</label>
                                            <input type="text" class="form-control data" id="presidente" name="presidente" placeholder="Nombre del presidente"  hs-entity="Comunidad" hs-field="presidente" aria-label="nombre" aria-describedby="addon-nombre" required>
                                        </div>  

                                    </div>

                                    <!-- Spa -->
                                    <div class="form-group row mb-4">
                                        <div class="col-12">
                                            <label for="nombre"><i class="bi bi-shield-check pr-2"></i>SPA Asignado</label>
                                            <select id="idspa" name="idspa" class="select-data data custom-select form-control selectpicker" data-live-search="true" hs-entity="Usuario" hs-field="idspa" hs-list-entity="Spa" hs-list-field="nombre" hs-list-value="id"></select>
                                        </div>
                                    </div> 

                                    <!-- dirección -->
                                    <div class="form-group row mb-2">
                                        <div class="col-12">
                                            <label for="direccion"><i class="bi bi-map pr-2"></i>Dirección</label>                        
                                            <input type="text" class="form-control data" id="direccion" name="direccion" placeholder="Dirección"  hs-entity="Comunidad" hs-field="direccion" required>
                                        </div>
                                    </div> 

                                    <!-- Código postal, Localidad y provincia -->                   
                                    <div class="form-group row mb-2">
                                        <div class="col-12 col-md-2">
                                            <label for="codpostal"><i class="bi bi-mailbox pr-2"></i>Código postal</label>
                                                    <input type="text" class="form-control data text-center" id="codpostal" name="codpostal" placeholder="Código postal"  hs-entity="Comunidad" hs-field="codpostal">
                                        </div>
                                        <div class="col-12 col-md-7">
                                            <label for="localidad"><i class="bi bi-geo-alt pr-2"></i>Localidad</label>
                                                <input type="text" class="form-control data" id="localidad" name="localidad" placeholder="Localidad"  hs-entity="Comunidad" hs-field="localidad" required>
                                        </div>
                                        <div class="col-12 col-md-3">
                                            <label for="provincia"><i class="bi bi-geo-alt pr-2"></i>Provincia</label>
                                            <select id="provinciaid" name="provinciaid" class="select-data custom-select data form-control selectpicker" data-live-search="true" hs-entity="Comunidad" hs-field="provinciaid" hs-list-entity="Provincia" hs-list-field="Nombre" hs-list-value="Id"></select>
                                        </div>                        
                                    </div> 

                                    <!-- Teléfono y e-mail -->
                                    <div class="form-group row mb-2">
                                        <div class="col-12 col-md-2">
                                            <label for="telefono"><i class="bi bi-phone pr-2"></i>Teléfono</label>                          
                                                <input type="text" class="form-control data text-center" id="telefono" name="telefono" placeholder="Teléfono"  hs-entity="Comunidad" hs-field="telefono">
                                        </div>
                                        <div class="col-12 col-md-7">
                                            <label for="email"><i class="bi bi-envelope pr-2"></i>E-Mail</label>              
                                                <input type="text" class="form-control data text-left" id="email" name="email" placeholder="E-mail"  hs-entity="Comunidad" hs-field="emailcontacto" required>
                                        </div>                       

                                    </div>                    

                                    <!-- Servicios contratados -->
                                    <div class="form-group row mb-2">

                                        <div class="col-12">

                                            <div class="card border rounded-0 mt-3">

                                                <div class="card-header p-0">
                                                    <div class="alert alert-success m-0 justify-content-center rounded" role="alert">
                                                        <p class="m-0 p-3 text-uppercase">Servicios contratados</p>
                                                    </div>
                                                </div>

                                                <div class="card-body">
                                                    <?php include_once('servicioscontratados.php') ; ?>
                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                    <?php $App->renderActionButtons(); ?>

                                </form>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <!-- Datos de documentos asociados -->
            <div class="tab-pane fade" id="documentos" role="tabpanel">

                <div class="row">

                    <div class="col-12 d-flex">

                        <div class="card flex-fill shadow-neumorphic pl-3 pb-3 pt-2 pr-3">

                            <div class="card-header pl-1 mb-2">

                                <h5 class="card-title mb-0"><i class="bi bi-folder-check mr-2"></i> <span class="titulo">Documentos</span></h5>

                            </div>
                    
                            <div class="card-body shadow-inset rounded-lg border mb-1 border-white">

                                <form class="form-data form-floating">

                                </form>

                            </div>

                        </div>


                    </div>

                </div>
            
            </div>

            <!-- Datos de empresa / empleados -->
            <?php include('tabs/cae.php'); ?>            

            <!-- DPD -->
            <?php include('tabs/dpd.php'); ?>

            <!-- Cargar desde archivo -->
            <div class="tab-pane fade" id="cargaarchivo" role="tabpanel">

                <div class="row">

                    <div class="col-12 d-flex">

                        <div class="card flex-fill shadow-neumorphic pl-3 pb-3 pt-2 pr-3">

                            <div class="card-header pl-1 mb-2">

                                <h5 class="card-title mb-0"><i class="bi bi-file-earmark-spreadsheet mr-2"></i> <span class="titulo">Cargar desde archivo</span></h5>

                            </div>
                    
                            <div class="card-body shadow-inset rounded-lg border mb-1 border-white">

                                <form class="form-data form-floating">

                                </form>

                            </div>

                        </div>


                    </div>

                </div>
            
            </div>

        </div>

    </div>

</div>