<?php
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/Facultad.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
include_once 'config.php';
include_once 'funciones.php';
ManejadorSesion::sec_session_start();
$facultad = $_SESSION['facultad'];

$ret = $facultad->guardarHorario($año,$ciclo);

if($ret == 0){
    exit(json_encode(0));
}