<?php
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorDocentes.php';
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));

$cicloinfo = parse_ini_file('../cicloinfo.ini');
$año = $cicloinfo['año'];
$ciclo = $cicloinfo['ciclo'];

ManejadorSesion::sec_session_start();

if (isset($_GET)){
    if(isset($_GET['term'])){
        $buscarComo = $_GET['term'];
        echo json_encode(ManejadorDocentes::buscarDocentes($buscarComo,$_SESSION['id_departamento']));                
    }       
}