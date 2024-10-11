<div class="row flex-grow-1 form-data ">

    <div class="col-12 d-flex position-relative">

        <div class="card flex-fill pl-3 pb-3 pt-2 pr-3 card-principal">

            <div class="card-header pb-0">

                <div class="row">

                    <div class="col-12 col-lg-3 d-flex flex-column justify-content-center">

                        <h5 class="card-title mb-0"><span class="titulo titulo-modulo pl-0"><i class="bi bi-receipt pr-2"></i> #<span class="form-data data"  hs-entity="Invoice" hs-field="numero"></span></span>                                     
                        <!-- Enlace ver documento asociado -->
                        <a href="" target="_blank" class="form-data data btn btn-outline-secondary text-lowercase rounded-pill shadow pl-2 pr-3 py-0" hs-entity="Invoice" hs-field="pdffile"><i class="bi bi-file-earmark-pdf pr-2"></i> ver documento asociado</a>   </h5>
                        <div class="row">

                            <!-- Estado / Nº factura / Fecha / Importe total -->
                            <div class="col-12">
                                                            
                                <p class="card-title mb-0 text-dark">
                                    <!-- Estado -->
                                    <label class="form-data data mr-1" style="font-size: 14px;text-transform:initial;" hs-entity="Invoice" hs-field="lblestado"></label>
                                    <!-- Número de factura / Fecha / Importe -->
                                    <span style="font-size:12px;">Fecha Fra.:</span> <span class="form-data data"  hs-entity="Invoice" hs-field="dateinvoice" style="font-size: 12px;"></span> / <span style="font-size:12px;">Total (imp. incluidos):</span> <span class="data form-data mb-3" style="font-size: 12px;" hs-entity="Invoice" hs-field="total_taxes_inc"></span><span style="font-size: 12px;">&euro;</span></label>
                                    
                                </p>

                            </div>                                            

                        </div>

                    </div>

                    <!-- Administrador / Comunidad -->
                    <div class="col-12 col-lg-6 d-flex flex-column justify-content-center">
                        <!-- Administrador -->
                        <p class="d-block font-weight-bold mb-0"><i class="bi bi-person"></i> Administrador: <label class="data form-data font-weight-normal" hs-entity="Invoice" hs-field="administrador"></label></p>
                        <!-- Comunidad -->
                        <p class="d-block font-weight-bold mb-0"><i class="bi bi-building"></i> Comunidad: <label class="data form-data font-weight-normal" hs-entity="Invoice" hs-entity-related="comunidad" hs-field="nombre"></label> CIF: <span class="data form-data font-weight-normal" hs-entity="Invoice" hs-entity-related="comunidad" hs-field="cif"></span></p>                                
                    </div>

                    <div class="col-12 col-lg-3 text-right">
                        <a href="javascript:history.back(-1);" class="btn btn-outline-secondary text-uppercase rounded-pill shadow pl-2 pr-4"><i class="bi bi-arrow-left"></i> Volver</a>
                    </div>

                </div>

            </div>
    
            <div class="card-body rounded-lg border mb-1 border-white py-0">

                <div class="row h-100">

                    <div class="col-12">

                        <form class="form-floating form-facturacion h-100" autocomplete="off">

                            <div class="row h-100">

                                <div class="col-12 h-100 br-10 p-3 d-flex flex-column space-between">

                                    <div class="row flex-grow-1">

                                        <!-- Información de facturación -->
                                        <div class="col-12 col-lg-3 px-3 d-flex flex-column space-between">

                                            
                                            <div class="row shadow-neumorphic border br-8">
                                                
                                                <div class="col-12 p-3">

                                                    <label class="d-block card-title" style="text-transform: initial;font-weight: 700;"><i class="bi bi-info-square pr-2"></i>Datos Facturación</label>

                                                    <!-- Liquidada -->
                                                    <label class="d-block font-weight-bold"><i class="bi bi-cash-coin"></i> Estado liquidación</label>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input data" type="checkbox" id="chkLiquidada" hs-entity="Invoice" hs-field="liquidada">
                                                        <label class="form-check-label text-dark" for="chkLiquidada">Liquidada</label>
                                                    </div>  

                                                <!-- Mes de facturación y año -->
                                                    <label class="d-block font-weight-bold"><i class="bi bi-calendar2-range"></i> Ciclo de facturación</label>
                                                    <div class="mb-3 pb-0">
                                                        <label class="data form-data" hs-entity="Invoice" hs-field="mes"></label><span> /</span>
                                                        <label class="data form-data" hs-entity="Invoice" hs-field="anyo"></label>
                                                    </div>                                            
                                                <!-- Referencia contrato -->
                                                    <label class="d-block font-weight-bold"><i class="bi bi-credit-card-2-front"></i> Ref. contrato</label>
                                                    <label class="d-block mb-3 data form-data" hs-entity="Invoice" hs-field="referenciacontrato"></label>                                            
                                                <!-- Iban domiciliación -->
                                                    <label class="d-block font-weight-bold"><i class="bi bi-bank"></i> Cuenta domiciliación</label>
                                                    <label class="d-block mb-3 data form-data" hs-entity="Invoice" hs-field="iban"></label>                                            
                                                <!-- Fecha devolución -->
                                                    <label class="d-block font-weight-bold"><i class="bi bi-calendar2-x"></i> Fecha devolución</label>
                                                    <label class="d-block data form-data mb-3" hs-entity="Invoice" hs-field="datereturned"></label>

                                                </div>

                                            </div>

                                        </div>

                                        <!-- Remesa en las que está incluida la factura -->
                                        <div class="col-12 col-lg-5 px-3 d-flex flex-column space-between">

                                            
                                            <div class="row shadow-neumorphic border br-8 flex-grow-1">
                                                
                                                <div class="col-12 p-3">
                                                    <label class="d-block card-title" style="text-transform: initial;font-weight: 700;"><i class="bi bi-files pr-2"></i>Remesas asociadas</label>
                                                    <div class="row">
                                                        <div class="col-12 form-data data"  hs-entity="Invoice" hs-field="table_remesa"></div>
                                                    </div>                                                    
                                                </div>

                                            </div>

                                        </div>

                                        <!-- Detalle de la factura -->
                                        <div class="col-12 col-lg-4 px-3 d-flex flex-column space-between">

                                            
                                            <div class="row shadow-neumorphic border br-8">
                                                
                                                
                                                <div class="col-12 p-3">
                                                    
                                                    <label class="d-block card-title" style="text-transform: initial;font-weight: 700;"><i class="bi bi-ticket-detailed pr-2"></i>Detalle Factura</label>
                                                    <!-- Conceptos facturados -->
                                                    <label class="d-block font-weight-bold">Conceptos Facturados</label>
                                                    <div class="row">
                                                        <div class="col-12 form-data data"  hs-entity="Invoice" hs-field="table_detail"></div>
                                                    </div>

                                                    <hr>

                                                    <!-- Subtotal -->
                                                    <div class="row text-right">
                                                        <div class="col-12 col-lg mb-3">
                                                            <label class="font-weight-bold">Subtotal</label>
                                                        </div>
                                                        <div class="col-12 col-lg-2 mb-3">
                                                            <label class="d-block"><span class="data form-data" hs-entity="Invoice" hs-field="total_taxes_exc"></span>&euro;</label>
                                                        </div>
                                                    </div>
                                                    <!-- Impuestos -->
                                                    <div class="row text-right">
                                                        <div class="col-12 col-lg mb-3">
                                                            <label class="d-block font-weight-bold">IVA <span class="data form-data" hs-entity="Invoice" hs-field="tax_rate"></span>%</label>
                                                        </div>
                                                        <div class="col-12 col-lg-2 mb-3">
                                                            <label class="d-block"><span class="data form-data" hs-entity="Invoice" hs-field="total_taxes"></span>&euro;</label>
                                                        </div>       
                                                    </div>
                                                    <!-- Total Factura -->
                                                    <div class="row text-right">
                                                        <div class="col-12 col-lg mb-3">
                                                            <label class="d-block font-weight-bold">Total Fra.</label>
                                                        </div>
                                                        <div class="col-12 col-lg-2 mb-3">
                                                            <label class="d-block"><span class="data form-data" hs-entity="Invoice" hs-field="total_taxes_inc"></span>&euro;</label>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>


                                    </div>

                                </div>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>