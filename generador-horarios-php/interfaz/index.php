<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php

include_once '../reglas_negocio/ManejadorSesion.php';

ManejadorSesion::sec_session_start();
chdir(dirname(__FILE__));
?>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Generador de Horarios de Clase</title>

<!-- Bootstrap -->
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
<!-- Carrusel -->
<link href="css/carrusel.css" rel="stylesheet">
<!-- login -->
</head>
<body>
<?php if (ManejadorSesion::comprobar_sesion() == true) : ?>
<?php header("Location: user/index.php") ?>
<?php else : ?>
<!-- Barra de menu -->
<?php include 'menuPrincipal.php';?>

<!-- Carousel -->
<?php include 'carrusel.php';?>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script type="text/javascript" src="js/jquery-ui/jquery-1.10.2.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/placeholder.js"></script>
<script type="text/javascript" src="js/ajaxpost.js"></script>
<?php endif; ?>
</body>
</html>
