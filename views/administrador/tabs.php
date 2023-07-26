<div class="row p-2 shadow-inset mx-1 br-10">
    <div class="col-12">
        <div class="card mb-1 border bg-white p-2 mt-3 br-10">

            <div class="list-group list-group-horizontal list-group-flush" role="tablist">
                <a class="list-group-item list-group-item-action active" data-toggle="list" href="#comunidades" role="tab" aria-selected="false"><i class="bi bi-shop mr-2"></i> Comunidades asignadas</a>
                <a class="list-group-item list-group-item-action" data-toggle="list" href="#representantelegal" role="tab" aria-selected="true"><i class="bi bi-person-vcard mr-2"></i> Representantes legales</a>
            </div>  
            </div>

            <div class="tab-content h-100">
            <?php

                //  Listado de representantes legales
                $App->renderView('administrador/partials/listadorepresentantelegal');


                //  Listado de comunidades asignadas al administrador
                $App->renderView('administrador/partials/listadocomunidades');

            ?>
            </div>
    </div>
</div>
