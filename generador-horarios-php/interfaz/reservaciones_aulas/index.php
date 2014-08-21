<?php
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorSesion.php';    
chdir(dirname(__FILE__));
ManejadorSesion::sec_session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="../js/jquery-ui/css/smoothness/css/smoothness/jquery-ui-1.10.4.custom.min.css">
        <link href="../bootstrap/xeditable/css/bootstrap-editable.css" rel="stylesheet">                
        <!-- Estilo de pagina -->
        <link href="css/index.css" rel="stylesheet" type="text/css">
        <!-- FontAwesome -->
        <link rel="stylesheet" href="../css/font-awesome.min.css">
    </head>
    <body>                        
        <?php if (ManejadorSesion::comprobar_sesion() == true) :
                include 'menuPrincipal.php';
        ?>
        <div id="mostrarHorario">
            <?php                                                          
                include_once './mostrarHorario.php';
            ?>
        </div>
        
        <?php                                                                     
            
            else : ?>
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
    <script type="text/javascript" src="../bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../bootstrap/xeditable/js/bootstrap-editable.js"></script>        
    <script type="text/javascript" src="js/funciones.js"></script>    
</html>