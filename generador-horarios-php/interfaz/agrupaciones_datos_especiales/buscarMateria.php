<?php
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorAgrupaciones.php';
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));

$cicloinfo = parse_ini_file('../cicloinfo.ini');
$año = $cicloinfo['año'];
$ciclo = $cicloinfo['ciclo'];

if (isset($_GET)){
    ManejadorSesion::sec_session_start();
    if(isset($_GET['term'])){
        $buscarComo = $_GET['term'];        
        echo json_encode(ManejadorAgrupaciones::buscarAgrupacionSoloNombre($buscarComo,$_SESSION['id_departamento'],$año,$ciclo));
    }       
}