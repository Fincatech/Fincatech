<?php

namespace HappySoftware\Controller\Traits;

trait TableTrait{

    public function renderTable($tableId, $model, $params = null)
    {
        global $App, $appSettings;

        ob_start();
            // include(ABSPATH.'views/componentes/listado/listado.php');
            include(ABSPATH.'views/'.$this->getListadoRoute() . '.php');
            $htmlOutput = ob_get_contents();
        ob_end_clean();

        echo $htmlOutput;

    }

}
