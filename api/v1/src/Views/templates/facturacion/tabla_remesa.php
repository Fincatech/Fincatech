<?php

    use HappySoftware\Controller\HelperController;

    global $appSettings; 
    $remesaPath = HelperController::RootURL() . $appSettings['storage']['remesas'];

?>
<table class="table dataTable">
    <thead>
        <tr>
            <th width="50px">ID</th>
            <th width="100px">Fecha</th>
            <th>Referencia</th=>
            <th width="80px">&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <?php for($i = 0; $i < count($data); $i++): ?>
            <?php 
                // Enlace descarga XML
                $file = $data[$i]['referencia'] . '.xml';
            ?>
        <tr>
        <td><?php echo $data[$i]['idremesa'];?></td>
            <td><?php echo date('d-m-Y', strtotime($data[$i]['dateremesa']));?></td>
            <td><?php echo $data[$i]['referencia'];?></td>
            <td>
                <ul class="nav justify-content-center accionesTabla">
                    <li class="nav-item">
                        <a href="<?php echo HelperController::RootURL(); ?>/remesa/<?php echo $data[$i]['idremesa'];?>" class="mr-2 d-inline-block"><i class="bi bi-eye" style="font-size: 21px;"></i></a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $remesaPath . '/' . $file; ?>" class="d-inline-block" target="_blank" download><i class="bi bi-cloud-arrow-down" style="font-size: 21px;"></i></a>
                    </li>
                </ul>
            </td>
        </tr>
        <?php endfor; ?>
        <!-- <?php //endfor; ?> -->
    </tbody>
</table>