<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php

require_once '../reglas_negocio/ManejadorSesion.php';

ManejadorSesion::sec_session_start();
chdir(dirname(__FILE__));
?>

<html lang="es">
  <head>
      <meta charset="UTF-8">
     <meta http-equiv="content-type" content="text/html; charset=UTF-8">         
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width = device-width, initial-scale = 1.0, minimum-scale = 1.0, maximum-scale = 1.0, user-scalable = no"/>
    <!--<meta name="viewport" content="width=device-width, initial-scale=1">-->
    <title>Area de Usuario</title>

    <!-- Bootstrap -->
    <link href="bootstrap/css/bootstrap.min.css" type="text/css" rel="stylesheet">   
    
    <!-- jquery ui -->
    <link rel="stylesheet" type="text/css" href="js/jquery-ui/css/smoothness/css/smoothness/jquery-ui-1.10.4.custom.min.css">
    
    <!-- datepicker -->
    <link href="css/default.css" type="text/css" rel="stylesheet">
    <link href="css/default.date.css" type="text/css" rel="stylesheet">    
    
    <!-- x-table -->
    <link href="bootstrap/xeditable/css/bootstrap-editable.css" rel="stylesheet">
    
    <!-- footable -->
    <link href="bootstrap/FooTable-2/css/footable.core.css?v=2-0-1" rel="stylesheet" type="text/css"/>
    
  </head>
  <body>
      
  
    <?php if (ManejadorSesion::comprobar_sesion() == true) : ?>      
        <!-- Barra de menu -->
        <?php include 'menuPrincipal.php';?>    
    
        <div class="container center-block">       
            <div class="container marketing">
                <!-- Three columns of text below the carousel -->
                <div class="row">
                  <div class="col-lg-4">
                      <a href="agrupaciones_crear/index.php">
                          <img width="140px" height="140px" src="imagenes/placeholder1-3.jpg" class="img-circle">
                          <h2>Crear Agrupaciones</h2>
                          <p>Paso 1: Desde acá usted podrá crear agrupaciones de materias.
                             Las agrupaciones de materias sirven para fusionar materias
                             de una carrera con materias de otras carreras.
                             Si desea fusionar materias de diferentes departamentos
                             deberá pedir al Administrador del sistema que lo haga
                             por usted.
                          </p>    
                      </a>                      
                    <p><a class="btn btn-default" href="#" role="button">Entrar &raquo;</a></p>
                  </div><!-- /.col-lg-4 -->
                  <div class="col-lg-4">
                      <a href="agrupaciones_datos/index.php">
                        <img width="140px" height="140px" src="imagenes/placeholder2.jpg" class="img-circle">
                        <h2>Agrupaciones y Datos</h2>
                        <p>Paso 2: Una vez completado el paso anterior acá podrá indicar cuálos son los requerimientos
                           de cada agrupación: El número de alumnos que llevarán la materia materia, el tamaño de los grupos
                           ó el número de grupos que desea para la materia.
                        </p>
                      </a>                    
                    <p><a class="btn btn-default" href="#" role="button">Entrar &raquo;</a></p>
                  </div><!-- /.col-lg-4 -->
                  <div class="col-lg-4">
                      <a href="agrupaciones_docentes/index.php">
                            <img width="140px" height="140px" src="imagenes/placeholder3-1.jpg" class="img-circle">
                            <h2>Agrupaciones y docentes</h2>
                            <p>Paso 3: Una vez completado el paso 2, en esta sección podrá asignar los docentes que impartirán
                               cada grupo de una materia</p>
                      </a>                    
                    <p><a class="btn btn-default" href="#" role="button">Entrar &raquo;</a></p>
                  </div><!-- /.col-lg-4 -->
                </div><!-- /.row -->
              </div>
        </div>  
      
    <?php else : ?>
        <p>
            <span class="">Usted no está autorizado para acceder a esta página.</span> Por favor <a href="index.php">Autentíquese</a>.
        </p>
    <?php endif; ?>

    <script type="text/javascript" src="js/jquery-ui/jquery-1.10.2.js"></script>
    <script type="text/javascript" src="js/jquery-ui/ui/jquery.ui.core.js"></script>
    <script type="text/javascript" src="js/jquery-ui/ui/jquery.ui.widget.js"></script>
    <script type="text/javascript" src="js/jquery-ui/ui/jquery.ui.position.js"></script>
    <script type="text/javascript" src="js/jquery-ui/ui/jquery.ui.menu.js"></script>
    <script type="text/javascript" src="js/jquery-ui/ui/jquery.ui.autocomplete.js"></script>
    <script type="text/javascript" src="js/jquery-ui/ui/jquery.ui.datepicker.js"></script>
    <script src="bootstrap/FooTable-2/js/footable.js?v=2-0-1" type="text/javascript"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>    
    <!--<script type="text/javascript" src="js/placeholder.js"></script>--> 
    <script type="text/javascript" src="js/funciones.js"></script> 
    

    </body>
</html>
