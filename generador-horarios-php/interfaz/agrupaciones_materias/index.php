<?php

require_once '../../reglas_negocio/ManejadorSesion.php';

ManejadorSesion::sec_session_start();
chdir(dirname(__FILE__));
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="../js/jquery-ui/css/smoothness/css/smoothness/jquery-ui-1.10.4.custom.min.css">        
        <link href="css/estilo.css" rel="stylesheet">  
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
            <?php include '../user/menuPrincipal.php';?>

            <div class="row">

                <?php include './materiasArrastrables.php';?>

                <?php include './panelAgrupaciones.php';?>

            </div>
        <?php else : ?>
            <p>
                <span class="">Usted no está autorizado para acceder a esta página.</span> Por favor <a href="../index.php">Autentíquese</a>.
            </p>
        <?php endif; ?>
     
        
    </body>
    
    
    <script type="text/javascript" src="../js/jquery-ui/jquery-1.10.2.js"></script>
    <script type="text/javascript" src="../js/jquery-ui/ui/jquery.ui.core.js"></script>
    <script type="text/javascript" src="../js/jquery-ui/ui/jquery.ui.widget.js"></script>
    <script type="text/javascript" src="../js/jquery-ui/ui/jquery.ui.mouse.js"></script>
    <script type="text/javascript" src="../js/jquery-ui/ui/jquery.ui.draggable.js"></script>
    <script type="text/javascript" src="../js/jquery-ui/ui/jquery.ui.droppable.js"></script>
    <script type="text/javascript" src="../js/jquery-ui/ui/jquery.ui.position.js"></script>
    <script type="text/javascript" src="../js/jquery-ui/ui/jquery.ui.menu.js"></script>
    <script type="text/javascript" src="../js/jquery-ui/ui/jquery.ui.autocomplete.js"></script>
    <script type="text/javascript" src="../bootstrap/FooTable-2/js/footable.js?v=2-0-1"></script>
    <script type="text/javascript" src="../bootstrap/js/bootstrap.min.js"></script>        
    <script type="text/javascript" src="js/funciones.js"></script>    
    <script type="text/javascript" src="../js/bootbox.min.js"></script>
</html>
