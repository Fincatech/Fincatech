<div class="row">

    <div class="col-12 d-flex">

        <div class="card flex-fill shadow-neumorphic pl-3 pb-3 pt-2 pr-3">

            <div class="card-header pl-1 mb-2">

                <h5 class="card-title mb-0"> <span class="titulo titulo-modulo"><i class="bi bi-shop pr-2"></i> Alta de nueva Empresa</span></h5>

            </div>
    
            <div class="card-body shadow-inset rounded-lg border mb-1 border-white text-left">

                <form class="form-data formEmpresaComunidad form-floating text-left " autocomplete="off" hs-model="Empresa">

                    <!-- CIF, Nombre -->
                    <div class="form-group row mb-2">
                        <div class="col-12 col-md-2 text-left">
                            <label for="cif" class="pl-0"><i class="bi bi-credit-card-2-front pr-2"></i>CIF*</label>
                            <input type="text" maxlength="20" class="form-control data text-center form-required" id="cif" name="cif" placeholder="CIF" hs-entity="Empresa" hs-field="cif" required>
                        </div>
                        <div class="col-12 col-md-8 text-left">
                            <label for="razonsocial"><i class="bi bi-person pr-2"></i>Nombre / Razón social*</label>              
                            <input type="text" class="form-control data text-left form-required" id="razonsocial" name="razonsocial" hs-entity="Empresa" hs-field="razonsocial" maxlength="100" required>
                        </div> 
                        <div class="col-12 col-md-2 text-left">
                            <label for="estado"><i class="bi bi-geo-alt pr-2"></i>Tipo*</label>
                            <select id="tipoEmpresaComunidad" name="tipoEmpresaComunidad" class="select-data data custom-select form-control selectpicker form-required" data-live-search="true" hs-entity="Empresa" hs-field="idtipoempresa" hs-list-entity="Empresatipo" hs-list-field="nombre" hs-list-value="id"></select>
                        </div>
                    </div>

                    <!-- dirección -->
                    <div class="form-group row mb-2">
                        <div class="col-12">
                            <label for="direccion"><i class="bi bi-map pr-2"></i>Dirección</label>                        
                                <input type="text" class="form-control data" id="direccion" name="direccion" placeholder="Dirección" maxlength="255" hs-entity="Empresa" hs-field="direccion" required>
                        </div>
                    </div> 

                    <!-- Código postal, Localidad y provincia -->                   
                    <div class="form-group row mb-2">
                        <div class="col-12 col-md-2">
                            <label for="codpostal"><i class="bi bi-mailbox pr-2"></i>Código postal</label>
                            <input type="text" maxlength="5" class="form-control data text-center" id="codpostal" name="codpostal" placeholder="Código Postal" hs-entity="Empresa" hs-field="codpostal">
                        </div>
                        <div class="col-12 col-md-5">
                            <label for="localidad"><i class="bi bi-geo-alt pr-2"></i>Localidad</label>
                                <input type="text" class="form-control data" id="localidad" name="localidad" placeholder="Localidad" maxlength="100" hs-entity="Empresa" hs-field="localidad" required>
                        </div>
                        <div class="col-12 col-md-5">
                            <label for="provinciaEmpresaComunidad"><i class="bi bi-geo-alt pr-2"></i>Provincia</label>
                            <select id="provinciaEmpresaComunidad" name="provinciaEmpresaComunidad" class="select-data data custom-select form-control selectpicker" data-live-search="true" hs-entity="Empresa" hs-field="provinciaid"  hs-list-entity="Provincia" hs-list-field="Nombre" hs-list-value="Id"></select>
                        </div>                        
                    </div> 

                    <!-- Teléfono y e-mail -->
                    <div class="form-group row mb-2">

                        <div class="col-12 col-md-2">
                            <label for="telefono"><i class="bi bi-phone pr-2"></i>Teléfono</label>                          
                            <input type="text" class="form-control data text-center" id="telefono" name="telefono" placeholder="Teléfono" maxlength="20" hs-entity="Empresa" hs-field="telefono">
                        </div>

                        <div class="col-12 col-md-5">
                            <label for="email"><i class="bi bi-envelope pr-2"></i>Email*</label>                          
                            <input type="text" maxlength="255" class="form-control data text-left form-required" id="email" name="email" placeholder="Email" hs-entity="Empresa" hs-field="email">
                        </div>                     

                        <div class="col-12 col-md-5">
                            <label for="personacontacto"><i class="bi bi-person-badge pr-2"></i>Persona de contacto</label>                          
                            <input type="text" maxlength="255" class="form-control data text-left" id="personacontacto" name="personacontacto" placeholder="Nombre persona de contacto" hs-entity="Empresa" hs-field="personacontacto">
                        </div> 

                    </div>                    

                </form>

            </div>

    </div>

</div>