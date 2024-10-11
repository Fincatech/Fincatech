<!DOCTYPE html>
<html lang="es">

<head>
	<title><?php echo $appSettings['title']; ?></title>

    <?php include_once('metas.php'); ?>

	<link rel="shortcut icon" href="<?php echo ASSETS_IMG; ?>icons/icon-48x48.png" />

    <?php include_once('inc_tipografias.php'); ?>

    <?php include_once('inc_css.php'); ?>

</head>

<?php

    $bodyModel = ucfirst($App->getController());
    $bodyModel = ucfirst($App->getController());
    $bodyAction = $App->getAction();
    $bodyModelId = $App->getId();

    $rolUser = "";

    if($App->isLogged())
        $rolUser = str_replace('ROLE_', '', $App->getLoggedUserRole());

?>

<body hs-role="<?php echo $rolUser; ?>" hs-model="<?php echo $bodyModel ?>" hs-action="<?php echo $bodyAction ?>" hs-model-id="<?php echo $bodyModelId; ?>">
<div class="loading">
    <div class="spinner-border text-warning mr-2" role="status">
        <span class="sr-only">Loading...</span>
    </div>
    <p class="text-white">Cargando...</p>
</div>
	<div class="wrapper">
