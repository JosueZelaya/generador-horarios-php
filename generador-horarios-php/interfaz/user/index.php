<?php

require_once '../../reglas_negocio/ManejadorSesion.php';

ManejadorSesion::sec_session_start();
chdir(dirname(__FILE__));
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">         
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Generador Horarios de Clase</title>
<!-- Bootstrap -->
<link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
<!-- Estilo de pagina -->
<link href="css/index.css" rel="stylesheet" type="text/css">
<!-- FontAwesome -->
<link rel="stylesheet" href="../css/font-awesome.min.css">
</head>
<body>

<?php if (ManejadorSesion::comprobar_sesion() == true) : ?>
 <!-- Barra de menu -->
    <?php include 'menuPrincipal.php';?>
 
 <div class="row">  
 <!-- barra vertical con opciones de administración -->
    <?php include 'barraVertical.php';?>
    <div class="col-lg-10" id="main-content">
        <div class="row">
            <div class="col-lg-3">
                <a href="../agrupaciones_crear/index.php" target="_blank">
                    <span class="index_icon"><i class="fa fa-cubes fa-lg"></i></span>
                    <!--<img width="140px" height="140px" src="../imagenes/placeholder1-3.jpg" class="img-circle">-->
                    <h2>Crear Agrupaciones</h2>
                    <p>Paso 1: Desde acá usted podrá crear agrupaciones de materias.
                       Las agrupaciones de materias sirven para fusionar materias
                       de una carrera con materias de otras carreras.
                       Si desea fusionar materias de diferentes departamentos
                       deberá pedir al Administrador del sistema que lo haga
                       por usted.
                    </p>    
                </a>                      
                <p><a class="btn btn-default" href="../agrupaciones_crear/index.php" target="_blank" role="button">Entrar &raquo;</a></p>
            </div><!-- /.col-lg-4 -->
            <div class="col-lg-3">
                <a href="../agrupaciones_datos/index.php" target="_blank">
                    <!--<img width="140px" height="140px" src="../imagenes/placeholder2.jpg" class="img-circle">-->
                    <span class="index_icon"><i class="fa fa-edit fa-lg"></i></span>
                    <h2>Agrupaciones y Datos</h2>
                    <p>Paso 2: Una vez completado el paso anterior acá podrá indicar cuálos son los requerimientos
                       de cada agrupación: El número de alumnos que llevarán la materia materia, el tamaño de los grupos
                       ó el número de grupos que desea para la materia.
                    </p>
                </a>                    
                <p><a class="btn btn-default" href="../agrupaciones_datos/index.php" target="_blank" role="button">Entrar &raquo;</a></p>
            </div><!-- /.col-lg-4 -->
            <div class="col-lg-3">
                <a href="../agrupaciones_datos_especiales/index.php"  target="_blank">
                    <!--<img width="140px" height="140px" src="../imagenes/placeholder-datos-especiales.jpg" class="img-circle">-->
                    <span class="index_icon"><i class="fa fa-star fa-lg"></i></span>
                    <h2>Agrupaciones y Datos Especiales</h2>
                    <p>Puede agregar grupos de laboratorio y de discusión a sus materias.
                    </p>
                </a>                    
                <p><a class="btn btn-default" href="#" role="button">Entrar &raquo;</a></p>
            </div><!-- /.col-lg-4 -->
            <div class="col-lg-3">
                <a href="../agrupaciones_docentes/index.php" target="_blank">
                    <!--<img width="140px" height="140px" src="../imagenes/placeholder3-1.jpg" class="img-circle">-->
                    <span class="index_icon"><i class="fa fa-male fa-lg"></i><i class="fa fa-female fa-lg"></i></span>
                    <h2>Agrupaciones y docentes</h2>
                    <p>Paso 3: Una vez completado el paso 2, en esta sección podrá asignar los docentes que impartirán
                       cada grupo de una materia</p>
                </a>                    
                <p><a class="btn btn-default" href="../agrupaciones_docentes/index.php" target="_blank" role="button">Entrar &raquo;</a></p>
            </div><!-- /.col-lg-4 -->

        </div><!-- /.row -->
        <div class="row">
            <div class="col-lg-3">
                <a href="../agrupaciones_materias/index.php" target="_blank">
                    <!--<img width="140px" height="140px" src="../imagenes/libros3.jpg" class="img-circle">-->
                    <span class="index_icon"><i class="fa fa-exchange fa-lg"></i></span>
                    <h2>Agrupaciones y Materias</h2>
                    <p>Se pueden añadir o quitar materias a las agrupaciones</p>
                </a>                    
                <p><a class="btn btn-default" href="../agrupaciones_materias/index.php" target="_blank" role="button">Entrar &raquo;</a></p>
            </div><!-- /.col-lg-4 -->
            <div class="col-lg-3">
                <a href="../agrupaciones_aulas/index.php" target="_blank">
                    <!--<img width="140px" height="140px" src="../imagenes/placeholder-aulas.jpg" class="img-circle">-->
                    <span class="index_icon"><i class="fa fa-building fa-lg"></i></span>
                    <h2>Agrupaciones y Aulas</h2>
                    <p>Se puede definir en qué aulas se impartirán los grupos de determinada materia.</p>
                </a>                    
              <p><a class="btn btn-default" href="../agrupaciones_aulas/index.php" target="_blank" role="button">Entrar &raquo;</a></p>
            </div>
            <div class="admin-users col-lg-3">
                <a href="#admin_materias_modal" data-toggle="modal">
                    <!--<img width="140px" height="140px" src="../imagenes/libros2.jpg" class="img-circle">-->
                    <span class="index_icon"><i class="fa fa-book fa-lg"></i></span>
                    <h2>Administrar Materias</h2>
                    <p>Agregar, modificar y eliminar Materias</p>
                </a>                    
                <p><a href="#admin_materias_modal" data-toggle="modal" class="btn btn-default" href="#" role="button">Entrar &raquo;</a></p>
            </div><!-- /.col-lg-4 -->
            <div class="admin-users col-lg-3">
                <a href="#admin_users_modal" data-toggle="modal">
                    <!--<img width="140px" height="140px" src="../imagenes/placeholder-usuarios.jpg" class="img-circle">-->
                    <span class="index_icon"><i class="fa fa-users fa-lg"></i></span>
                    <h2>Administrar Personal</h2>
                    <p>Agregar, modificar y eliminar Docentes y/o Usuarios</p>
                </a>                    
              <p><a href="#admin_users_modal" data-toggle="modal" class="btn btn-default" href="#" role="button">Entrar &raquo;</a></p>
            </div>
        </div>
        <?php if($_SESSION['usuario_login'] == "admin") : ?>
        <div class='row'>
            <div class="admin-users col-lg-3">
                <a href="#admin_aulas_modal" data-toggle="modal">
                    <!--<img width="140px" height="140px" src="../imagenes/aula.jpg" class="img-circle">-->
                    <span class="index_icon"><i class="fa fa-university fa-lg"></i></span>
                    <h2>Administrar Aulas</h2>
                    <p>Agregar, modificar y eliminar Aulas</p>
                </a>                    
                <p><a href="#admin_aulas_modal" data-toggle="modal" class="btn btn-default" href="#" role="button">Entrar &raquo;</a></p>
            </div><!-- /.col-lg-4 -->
            <div class="admin-users col-lg-3">
                <a href="#admin_departamentos_modal" data-toggle="modal">
                    <!--<img width="140px" height="140px" src="../imagenes/departamentos.jpg" class="img-circle">-->
                    <span class="index_icon"><i class="fa fa-sitemap fa-lg"></i></span>
                    <h2>Administrar Departamentos</h2>
                    <p>Agregar, modificar y eliminar Departamentos</p>
                </a>                    
                <p><a href="#admin_departamentos_modal" data-toggle="modal" class="btn btn-default" href="#" role="button">Entrar &raquo;</a></p>
            </div>
            <div class="col-lg-3">
                <a href="../reservaciones_aulas/index.php" target="_blank">
                    <!--<img width="140px" height="140px" src="../imagenes/placeholder4.jpg" class="img-circle">-->
                    <span class="index_icon"><i class="fa fa-lock fa-lg"></i></span>
                    <h2>Reservaciones de Aulas</h2>
                    <p>Se pueden hacer reservaciones en las aulas para que nos sean tomadas en cuenta en la generación
                    del nuevo horario.</p>
                </a>                    
                <p><a class="btn btn-default" href="../reservaciones_aulas/index.php" target="_blank" role="button">Entrar &raquo;</a></p>
            </div>
        </div>
        <?php endif; ?>
    </div>
 </div>
 <?php include_once 'adminUsersModal.php'; ?> 
 <?php include_once './adminMateriasModal.php'; ?>
 <?php include_once './adminAulasModal.php'; ?>
 <?php include_once './adminDepartamentosModal.php'; ?>
 <?php include_once './configuracionModal.php'; ?>
 <?php else : ?> 
    <p>
        <span class="">Usted no está autorizado para acceder a esta página.</span> Por favor <a href="../index.php">Autentíquese</a>.
    </p>
<?php endif; ?>
 
<!-- Cargamos los scripts --> 
<script type="text/javascript" src="../js/jquery-ui/jquery-1.10.2.js"></script>
<script type="text/javascript" src="../bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript" src="../js/bootbox.min.js"></script>
<script type="text/javascript" src="../js/jquery.bootpag.min.js"></script>
<script type="text/javascript" src="../js/bootstrap.combobox.js"></script>
<script type="text/javascript" src="../js/funciones.js"></script>
</body>
</html>