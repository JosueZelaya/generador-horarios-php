<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php

require_once '../../../reglas_negocio/ManejadorSesion.php';

ManejadorSesion::sec_session_start();
?>

<html lang="es">
  <head>
      <meta charset="UTF-8">
     <meta http-equiv="content-type" content="text/html; charset=UTF-8">         
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Agregar Docentes</title>
    <link rel="stylesheet" type="text/css" href="../../js/jquery-ui/css/smoothness/css/smoothness/jquery-ui-1.10.4.custom.min.css">
    <!-- Bootstrap -->
    <link href="../../bootstrap/css/bootstrap.min.css" rel="stylesheet">
     <style>
        .ui-autocomplete {
            max-height: 400px;
            overflow-y: auto;
            /* prevent horizontal scrollbar */
            overflow-x: hidden;
        }
        /* IE 6 doesn't support max-height
        * we use height instead, but this forces the menu to always be this tall
        */
        * html .ui-autocomplete {
            height: 400px;
        }
    </style>
    
  </head>
  <body>
      
    <?php if (ManejadorSesion::comprobar_sesion() == true) : ?>      
    
    <!-- Barra de menu -->
    <?php include 'menuPrincipal.php';?>  
    
    <ul class='nav nav-tabs'>
        <li id='agregar_docentes' class='active'><a href='#'>Docentes</a></li>
        <?php if($_SESSION['usuario_login'] == "admin") : ?>
        <li  id='agregar_usuarios'><a href='#'>Usuarios</a></li>
        <?php endif; ?>
    </ul>
    <br/>
    
    <div id="contenido">
        <?php include 'agregarDocente.php';?>  
    </div>     

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script type="text/javascript" src="../../js/jquery-ui/jquery-1.10.2.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script type="text/javascript" src="../../js/jquery-ui/ui/jquery.ui.core.js"></script>
    <script type="text/javascript" src="../../js/jquery-ui/ui/jquery.ui.widget.js"></script>
    <script type="text/javascript" src="../../js/jquery-ui/ui/jquery.ui.mouse.js"></script>    
    <script type="text/javascript" src="../../js/jquery-ui/ui/jquery.ui.position.js"></script>
    <script type="text/javascript" src="../../js/jquery-ui/ui/jquery.ui.menu.js"></script>
    <script type="text/javascript" src="../../js/jquery-ui/ui/jquery.ui.autocomplete.js"></script>
    <script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>       
    <script type="text/javascript" src="js/funciones.js"></script>    
  </body>
</html>

<?php else : ?>
    <p>
        <span class="">Usted no está autorizado para acceder a esta página.</span> Por favor <a href="../../../interfaz/index.php">Autentíquese</a>.
    </p>
<?php endif; ?>