<?php

require_once '../../../reglas_negocio/ManejadorSesion.php';

ManejadorSesion::sec_session_start();
chdir(dirname(__FILE__));
?>

<html lang="es">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">         
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Horario de Docentes</title>
    <link rel="stylesheet" type="text/css" href="../../js/jquery-ui/css/smoothness/css/smoothness/jquery-ui-1.10.4.custom.min.css">
    <link rel="stylesheet" type="text/css" href="../../js/jqwidgets/styles/jqx.base.css" />
    <link rel="stylesheet" type="text/css" href="../../js/jqwidgets/styles/jqx.bootstrap.css" />
    <link href="css/index.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="../../css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.min.css" />
    <!-- Bootstrap -->
    <link href="../../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    
  </head>
  <body>
      
    <?php if (ManejadorSesion::comprobar_sesion() == true) : ?>      
    
    <!-- Barra de menu -->
    <?php include '../../user/menuPrincipal.php';?>
    <!-- Main content -->
    <div class="row">
        <div class="col-lg-6" id="docArea">
            <div class="panel panel-default panelHorario">
                <div class="panel-heading">  
                    <p class="center center-block">Seleccione un docente</p>
                </div>
                <div class="panel-body">
                    <div id="cmbDocentes"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6" id="horaArea">
            <div class="panel panel-default panelHorario">
                <div class="panel-heading">  
                    <p class="center center-block">Horario del docente seleccionado. Seleccione un grupo de horas continuas para asignar horario.</p>
                </div>
                <div class="panel-body">
                    <a class="btn btn-primary" href="#" id='guardarHoras'>
                        <i class="fa fa-send"></i> Guardar
                    </a>
                    <a class="btn btn-danger" href="#" id='borrarHoras'>
                        <i class="fa fa-eraser"></i> Borrar Horario
                    </a>
                    <hr size="100%" />
                    <div id="mostrarHorario" class="container-fluid">

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script type="text/javascript" src="../../js/jquery-ui/jquery-1.10.2.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script type="text/javascript" src="../../js/jqwidgets/jqxcore.js"></script>
    <script type="text/javascript" src="../../js/jqwidgets/jqxbuttons.js"></script>
    <script type="text/javascript" src="../../js/jqwidgets/jqxscrollbar.js"></script>
    <script type="text/javascript" src="../../js/jqwidgets/jqxdata.js"></script>
    <script type="text/javascript" src="../../js/jqwidgets/jqxlistbox.js"></script>
    <script type="text/javascript" src="../../js/jqwidgets/jqxcombobox.js"></script>
    <script type="text/javascript" src="../../js/bootbox.min.js"></script>
    <script type="text/javascript" src="../../bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/funciones.js"></script>
  </body>
</html>

<?php else : ?>
    <p>
        <span class="">Usted no está autorizado para acceder a esta página.</span> Por favor <a href="../../../interfaz/index.php">Autentíquese</a>.
    </p>
<?php endif;