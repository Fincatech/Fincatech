<!DOCTYPE html>
<html lang="es">

<head>

	<title><?php echo $appSettings['title']; ?></title>

    <?php include_once('metas.php'); ?>

	<link rel="shortcut icon" href="<?php echo ASSETS_IMG; ?>icons/icon-48x48.png" />

    <?php include_once('inc_tipografias.php'); ?>

    <?php include_once('inc_css.php'); ?>

</head>

<body hs-model="<?php echo ucfirst($App->getController()) ?>" hs-action="<?php echo $App->getAction() ?>" hs-model-id="<?php echo $App->getId(); ?>">

	<div class="wrapper">
