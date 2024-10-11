<?php
    if( !is_null($imagen) )
    {
        $imagen = '<img src="'. ASSETS_IMG . $imagen . '.png" class="img-fluid icono-boton-menu" />';
    }else{
        $imagen = '
        <svg class="bi text-secondary img-fluid" width="32" height="32" fill="currentColor">
            <use xlink:href="' . APPFOLDER . '/public/assets/icons/bootstrap-icons.svg#'.$icono.'"/>
        </svg>        
        ';
    }

    $classBoton = "";

    // $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $actual_link = (string)HOME_URL . substr( $_SERVER['REQUEST_URI'], 1, strlen($_SERVER['REQUEST_URI'])-1);
    
    if((@strpos($actual_link, $urlDestino) !== false) && !is_null($urlDestino) )
    {
        $classBoton = "activo";
    }

    if(!is_null($urlDestino) )
    {
        $urlDestino = HOME_URL . $urlDestino;
    }else{
        $urlDestino = 'javascript:void(0);';
    }

?>  
<a href="<?php echo $urlDestino; ?>" class="">
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