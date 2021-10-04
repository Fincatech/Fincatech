<li class="sidebar-header">Comunidades</li>
<?php foreach($datos as $dato): ?>

    <li class="sidebar-item">
        <a class="sidebar-link comunidad-<?php echo $dato['id']; ?>" href="comunidad/<?php echo $dato['id']; ?>">
            <img src="assets/img/icon_edificio.png" class="img-responsive feather">
            <span class="align-middle pl-3"><?php echo $dato['codigo'] . ' - ' . $dato['nombre'] ?></span>
        </a>
    </li>

<?php endforeach; ?>