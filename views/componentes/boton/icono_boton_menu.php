<?php
    if( !is_null($imagen) )
    {
        $imagen = '<img src="'. ASSETS_IMG . $imagen . '.png" class="img-fluid icono-boton-menu" />';
    }else{
        $imagen = '
        <svg class="bi text-secondary img-fluid" width="32" height="32" fill="currentColor">
            <use xlink:href="' . APPFOLDER . 'public/assets/icons/bootstrap-icons.svg#'.$icono.'"/>
        </svg>        
        ';
    }

    $classBoton = "";

    if( strpos($urlDestino, $App->getController() ) !== false ) 
    {
        $classBoton = "activo";
    }

?>  
<a href="<?php echo APPFOLDER . $urlDestino; ?>" class="">
<div class="card card-dashboard <?php echo $destino; ?> icono-menu <?php echo $classBoton; ?>">
    <div class="card-body d-flex align-self-center w-100">
        <div class="row w-100 d-flex">
            <div class="col-xxl-4 col-4 text-center align-self-center d-flex">
                <?php echo $imagen; ?>
            </div>
            <div class="col-xxl-8 col-8 pl-0 align-self-center d-flex">
                <h5 class="card-title titulo m-0">
                    <p class="font-weight-normal text-dark m-0"><?php echo $titulo; ?></p>
                </h5>
            </div>
        </div>
    </div>
</div>
</a>