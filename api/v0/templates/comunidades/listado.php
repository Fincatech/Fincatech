<table class="table table-hover my-0 listadoComunidadesDashboard">
    <thead>
        <tr>
            <th>Código</th>
            <th class="d-table-cell">Nombre comunidad</th>
            <th class="d-table-cell text-center">Documentos Verificados</th>
            <th class="d-table-cell text-center">Documentos Pendientes de subir</th>
            <th class="d-table-cell text-center">Documentos Pendientes de verificar</th>
            <th class="d-table-cell text-center">Fecha de alta</th>
            <th class="d-table-cell"></th>
        </tr>
    </thead>
    <tbody>

    <?php foreach( $datos as $dato ): ?>

        <tr>
            <td><?= $dato['codigo'] ?></td>
            <td class="d-table-cell"><?= $dato['nombre'] ?></td>
            <td class="d-table-cell text-center"><?php //var_dump($dato['created']); ?></td>
            <td class="d-table-cell text-center"><span class="badge bg-warning">10</span></td>
            <td class="d-table-cell text-center"><span class="badge bg-warning">10</span></td>
            <td class="d-table-cell text-center"><?php echo date('d/m/Y', strtotime($dato['created']['date'])) ?></td>
            <td class="d-table-cell text-left">
                <a href="javascript:void(0)" class="btnVerComunidad" data-nombre="<?= $dato['nombre'] ?>" data-id="<?= $dato['id'] ?>"><i data-feather="eye" class="text-info"></i></a>
                <a href="comunidad/<?php echo $dato['id']; ?>" class="btnEditarComunidad" data-id="<?= $dato['id'] ?>"><i data-feather="edit" class="text-success"></i></a>
                <a href="javascript:void(0);" class="btnEliminarComunidad dd<?= $dato['id'] ?>" data-id="<?= $dato['id'] ?>" data-nombre="<?= $dato['nombre'] ?>"><i data-feather="trash-2" class="text-danger"></i></a>
            </td>

        </tr>

    <?php endforeach; ?>

    </tbody>

</table>