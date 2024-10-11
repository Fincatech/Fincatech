<div id="appContainer" class="row flex-grow-1 form-data">

    <div class="col-12 d-flex">

        <div class="card flex-fill shadow-neumorphic">

            <div class="card-header">
                    <h5 class="card-title mb-0 text-center">Reasignación de proveedores</h5>
            </div>
            
            <div class="card-body pt-0 space-between">

                <div class="row">

                    <div class="col-12 col-md-6 text-left">
                        <label for="proveedorAntiguoId"><i class="bi bi-person-dash mr-2"></i> Seleccione el proveedor que actualmente está asignado</label>
                        <select id="proveedorAntiguoId" name="proveedorAntiguoId" class="select-data custom-select data form-control selectpicker" data-live-search="true" hs-list-entity="Empresa" hs-list-field="Empresa.cif,Empresa.razonsocial,Empresa.email" hs-list-value="Empresa.id" hs-seleccionar="true"></select>
                    </div>                
 
                    <div class="col-12 col-md-6 text-left">
                        <label for="proveedorNuevoId"><i class="bi bi-person-check mr-2"></i> Seleccione el proveedor que desea asignar</label>
                        <select id="proveedorNuevoId" name="proveedorNuevoId" class="select-data custom-select data form-control selectpicker" data-live-search="true" hs-list-entity="Empresa" hs-list-field="Empresa.cif,Empresa.razonsocial,Empresa.email" hs-list-value="Empresa.id" hs-seleccionar="true"></select>
                    </div>                       

                </div>
                
                <div class="row mt-3 align-items-end">
                    <div class="col-12 text-center">
                        <a href="javascript:void(0);" class="btn btn-outline-success shadow-neumorphic rounded-lg pt-3 pb-3 btnReasignarProveedor" tabindex="-1" role="button" aria-disabled="true"><i class="bi bi-arrow-repeat"></i> Asignar nuevo proveedor</a>
                    </div>
                </div>   

                <div class="row p-4 flex-grow-1">

                    <div class="col-12 p-3 shadow-neumorphic br-10">

                        <h5 class="card-title mb-0">Comunidades actualmente asociadas al proveedor</h5>
                        <p class="text-secondary mb-0"><i class="bi bi-info-circle mr-2"></i>Seleccione el proveedor actual que desea reasignar para ver las comunidades que tiene actualmente asignadas.</p>

                        <div class="row">
                            <div class="col-12 text-left">
                                <table class="table table-bordered table-hover my-0 hs-tabla no-footer no-clicable" data-order='[[ 0, "desc"]]' name="listadoComunidadesProveedor" id="listadoComunidadesProveedor" data-model="Comunidades">
                                    <thead class="thead"></thead>
                                    <tbody class="tbody"></tbody>
                                </table>                        
                            </div>                
                        </div>                        
                        
                    </div>

                </div>            

            </div>

        </div>

    </div>

</div>